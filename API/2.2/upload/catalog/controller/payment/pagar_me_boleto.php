<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeBoleto extends Controller
{
    private $error;

    public function index()
    {

        $this->language->load('payment/pagar_me_boleto');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_information'] = $this->language->get('text_information');
        $data['text_wait'] = $this->language->get('text_wait');
        $data['text_information'] = $this->config->get('pagar_me_boleto_text_information');
        $data['url'] = $this->url->link('payment/pagar_me_boleto/confirm', '', 'SSL');
        $data['url2'] = $this->url->link('payment/pagar_me_boleto/error', '', 'SSL');

         //incluindo css
        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_boleto.css')) {
            $data['stylesheet'] = 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css';
        } else {
            $data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me_cartao.css';
        }

        return $this->load->view('payment/pagar_me_boleto', $data);
    }

    public function confirm()
    {


        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), 'Imprima seu boleto aqui -> ' . $order['pagar_me_boleto_url']);

        $this->session->data['pagar_me_boleto_url'] = $order['pagar_me_boleto_url'];

        $this->response->redirect($this->url->link('checkout/success'));
    }

    public function gera()
    {
        $boleto_url = $this->request->get['boleto'];

        $this->response->redirect($boleto_url);
    }

    public function callback()
    {

        $event = $this->request->post['event'];
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_boleto');

        if ($event == 'transaction_status_changed') {

            $order_id = $this->model_payment_pagar_me_boleto->getPagarMeOrder($this->request->post['id']);

            $current_status = 'pagar_me_boleto_order_' . $this->request->post['current_status'];

            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get($current_status), '', true);

        } else {
            $this->log->write("Pagar.Me boleto: Notificação inválida");
        }

        echo "OK";

    }

    public function payment()
    {

        $this->load->model('checkout/order');
        $this->load->model('account/customer');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $document_number = '';
        $numero = 'Sem Número';
        $complemento = '';
        $customer_name = trim($order_info['payment_firstname']).' '.trim($order_info['payment_lastname']);
        /* Pega os custom fields de CPF/CNPJ, número e complemento */
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields($customer['customer_group_id']);
        foreach($custom_fields as $custom_field){
            if($custom_field['location'] == 'account'){
                if(strtolower($custom_field['name']) == 'cpf' || strtolower($custom_field['name']) == 'cnpj'){
                    $document_number = $order_info['custom_field'][$custom_field['custom_field_id']];
                }
            }elseif($custom_field['location'] == 'address'){
                if(strtolower($custom_field['name']) == 'numero' || strtolower($custom_field['name']) == 'número'){
                    $numero = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                }elseif(strtolower($custom_field['name'] == 'complemento')){
                    $complemento = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                }
            }
        }

        Pagarme::setApiKey($this->config->get('pagar_me_boleto_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $this->request->post['amount'],
            'payment_method' => 'boleto',
            'boleto_expiration_date' => date('Y-m-d', strtotime('+' . $this->config->get('pagar_me_boleto_dias_vencimento') + 1 . ' days')),
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_boleto/callback',
            'async' => 'false',
            "customer" => array(
                "name" => $customer_name,
                "document_number" => $document_number,
                "email" => $order_info['email'],
                "address" => array(
                    "street" => $order_info['payment_address_1'],
                    "neighborhood" => $order_info['payment_address_2'],
                    "zipcode" => $this->removeSeparadores($order_info['payment_postcode']),
                    "street_number" => $numero,
                    "complementary" => $complemento
                ),
                "phone" => array(
                    "ddd" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2),
                    "number" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2, 9),
                )
            ),
            'metadata' => array(
                'id_pedido' => $order_info['order_id'],
                'loja' => $this->config->get('config_name'),
            )));

        try {
            $transaction->charge();
        } catch (Exception $e) {
            $this->log->write("Erro Pagar.Me boleto: " . $e->getMessage());
            $this->error = $e->getMessage();
        }

        $status = $transaction->status; // status da transação

        $boleto_url = $transaction->boleto_url; // URL do boleto bancário

        $id_transacao = $transaction->id;

        $json = array();

        if ($status == 'waiting_payment') {
            $this->load->model('payment/pagar_me_boleto');
            $this->model_payment_pagar_me_boleto->addTransactionId($this->session->data['order_id'], $id_transacao, $boleto_url);
            $json['transaction'] = $transaction->id;
            $json['success'] = true;
            $json['pagar_me_boleto_url'] = $boleto_url;
        } else {
            $json['success'] = false;
        }

        if ($this->error) {
            $json['error'] = $this->error;
        }

        $this->response->setOutput(json_encode($json));
    }

    private function removeSeparadores($string)
    {
        $nova_string = str_replace(array('.', '-', '/', '(', ')', ' '), array('', '', '', '', '', ''), $string);

        return $nova_string;
    }

}

?>
