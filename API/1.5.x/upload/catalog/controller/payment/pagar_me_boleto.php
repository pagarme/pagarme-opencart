<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeBoleto extends Controller
{

    protected function index()
    {

        $this->language->load('payment/pagar_me_boleto');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $this->data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['text_information'] = $this->language->get('text_information');
        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['text_information'] = $this->config->get('pagar_me_boleto_text_information');
        $this->data['url'] = $this->url->link('payment/pagar_me_boleto/confirm', '', 'SSL');
        $this->data['url2'] = $this->url->link('payment/pagar_me_boleto/error', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_boleto.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pagar_me_boleto.tpl';
        } else {
            $this->template = 'default/template/payment/pagar_me_boleto.tpl';
        }
        // incluindo css
        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_boleto.css')) {
            $this->data['stylesheet'] = 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css';
        } else {
            $this->data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me_cartao.css';
        }

        $this->render();
    }

    public function confirm()
    {


        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), 'Imprima seu boleto aqui -> ' . $order['pagar_me_boleto_url']);

        $this->session->data['pagar_me_boleto_url'] = $order['pagar_me_boleto_url'];

        $this->redirect($this->url->link('checkout/success'));
    }

    public function gera()
    {
        $boleto_url = $this->request->get['boleto'];

        $this->redirect($boleto_url);
    }

    public function callback()
    {

        $event = $this->request->post['event'];
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_boleto');

        if ($event == 'transaction_status_changed') {

            $order_id = $this->model_payment_pagar_me_boleto->getPagarMeOrder($this->request->post['id']);

            $current_status = 'pagar_me_boleto_order_' . $this->request->post['current_status'];

            $this->model_checkout_order->update($order_id, $this->config->get($current_status), '', true);

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

        if ($this->config->get('dados_status')) {
            if ($customer['cpf'] != '') {
                $document_number = $this->removeSeparadores($customer['cpf']);
                $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
            } else {
                $document_number = $this->removeSeparadores($customer['cnpj']);
                $customer_name = $customer['razao_social'];

            }
            $numero = $order_info['payment_numero'];
            $complemento = $order_info['payment_company'];
        } else {
            $document_number = $this->removeSeparadores($order_info['payment_tax_id']);
            $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
            $numero = 'Sem número';
            $complemento = '';
        }

        Pagarme::setApiKey($this->config->get('pagar_me_boleto_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $this->request->post['amount'],
            'payment_method' => 'boleto',
            'boleto_expiration_date' => date('Y-m-d', strtotime('+' . $this->config->get('pagar_me_boleto_dias_vencimento') + 1 . ' days')),
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_boleto/callback',
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
                    "number" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2),
                )
            )));

        try {
            $transaction->charge();
        } catch (Exception $e) {
            $this->log->write("Erro Pagar.Me boleto: " . $e->getMessage());
            die();
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
            $json['boleto_url'] = $boleto_url;
        } else {
            $json['success'] = false;
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
