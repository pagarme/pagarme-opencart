<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeBoleto extends Controller {

    protected function index() {

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

    public function confirm() {


        $this->load->model('checkout/order');

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'));

        $this->redirect($this->url->link('checkout/success'));
    }

    public function callback() {

        $event = $_POST['event'];
        $this->load->model('checkout/order');

        if($event == 'transaction_status_changed'){

            $this->log->write($_POST['id']);

            $order_id = $_POST['id'];

            $current_status = 'pagar_me_boleto_order_' . $_POST['current_status'];

            $this->model_checkout_order->update($order_id, $this->config->get($current_status), '', true);

        }else{
            $this->log->write("Pagar.Me boleto: Notificação inválida");
        }

    }

    public function payment() {

        Pagarme::setApiKey($this->config->get('pagar_me_boleto_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $_POST['amount'],
            'payment_method' => 'boleto',
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_boleto/callback'
        ));

        try{
            $transaction->charge();
        }  catch (Exception $e){
            $this->log->write("Erro Pagar.Me boleto: " . $e->getMessage());
            die();
        }

        $status = $transaction->status; // status da transação

        $boleto_url = $transaction->boleto_url; // URL do boleto bancário

        $json = array();

        if ($status == 'waiting_payment') {
            $json['transaction'] = $transaction;
            $json['success'] = true;
            $json['boleto_url'] = $boleto_url;
        } else {
            $json['success'] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

}

?>
