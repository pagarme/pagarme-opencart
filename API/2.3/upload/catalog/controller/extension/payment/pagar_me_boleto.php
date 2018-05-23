<?php
require_once('pagar_me.php');
class ControllerExtensionPaymentPagarMeBoleto extends ControllerExtensionPaymentPagarMe
{
    public function index()
    {

        $this->language->load('extension/payment/pagar_me_boleto');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_information'] = $this->language->get('text_information');
        $data['text_wait'] = $this->language->get('text_wait');
        $data['text_information'] = $this->config->get('pagar_me_boleto_text_information');
        $data['url'] = $this->url->link('extension/payment/pagar_me_boleto/confirm', '', 'SSL');

        $data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me.css';

        return $this->load->view('extension/payment/pagar_me_boleto.tpl', $data);
    }

    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/pagar_me_boleto');
        
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $pagar_me_transaction = $this->model_extension_payment_pagar_me_boleto->getPagarMeTransactionId($order['order_id']);

        $comment = "<a href='".htmlspecialchars($order['pagar_me_boleto_url'])."'>Imprima seu boleto aqui</a><br>";
        $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), $comment, true);

        $admin_comment = 'Pagar.me Transaction: '.$pagar_me_transaction['transaction_id'];
        $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), $admin_comment);

        $this->session->data['pagar_me_boleto_url'] = $order['pagar_me_boleto_url'];

        $this->response->redirect($this->url->link('checkout/success'));
    }

    public function gera()
    {
        $boleto_url = $this->request->get['boleto'];

        $this->response->redirect($boleto_url);
    }

    public function payment()
    {

        $this->load->model('checkout/order');
        $this->load->model('account/customer');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $document_number = $this->getCustomerDocumentNumber($customer, $order_info);

        $customer_address = $this->getCustomerAdditionalAddressData($customer, $order_info);

        $customer_name = trim($order_info['payment_firstname']).' '.trim($order_info['payment_lastname']);

        Pagarme::setApiKey($this->config->get('pagar_me_boleto_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $this->request->post['amount'],
            'payment_method' => 'boleto',
            'boleto_expiration_date' => date('Y-m-d', strtotime('+' . $this->config->get('pagar_me_boleto_dias_vencimento') + 1 . ' days')),
            'postback_url' => HTTP_SERVER . 'index.php?route=extension/payment/pagar_me_boleto/callback',
            'async' => 'false',
            "customer" => array(
                "name" => $customer_name,
                "document_number" => $document_number,
                "email" => $order_info['email'],
                "address" => array(
                    "street" => $order_info['payment_address_1'],
                    "street_number" => $customer_address['street_number'],
                    "neighborhood" => $order_info['payment_address_2'],
                    "complementary" => $customer_address['complementary'],
                    "city" => $order_info['payment_city'],
                    "state" => $order_info['payment_zone_code'],
                    "country" => $order_info['payment_country'],
                    "zipcode" => $this->removeSeparadores($order_info['payment_postcode'])
                ),
                "phone" => array(
                    "ddd" => substr(preg_replace('/\D/', '', $order_info['telephone']), 0, 2),
                    "number" => substr(preg_replace('/\D/', '', $order_info['telephone']), 2, 9),
                )
            ),
            'metadata' => array(
                'id_pedido' => $order_info['order_id'],
                'loja' => $this->config->get('config_name'),
            )
        ));

        $json = array();

        try {
            $transaction->charge();

            $this->load->model('extension/payment/pagar_me_boleto');

            $this->model_extension_payment_pagar_me_boleto->addTransactionId($this->session->data['order_id'], $transaction->id, $transaction->boleto_url);

            $this->log->write('Pagar.me Transaction: '.$transaction->id.' | Status: '.$transaction->status.' | Pedido: '.$order_info['order_id']);

            $json['pagar_me_boleto_url'] = $transaction->boleto_url;
            $json['success'] = true;

        } catch (Exception $e) {
            $this->log->write("Erro Pagar.me boleto: " . $e->getMessage());
            $json['error'] = $e->getMessage();
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
