<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeCheckout extends Controller
{

    protected function index()
    {
        $this->language->load('payment/pagar_me_checkout');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        //Dados para o checkout
        $this->data['encryption_key'] = $this->config->get('pagar_me_checkout_criptografia');
        $this->data['amount'] = str_replace(array(".", ","), array("", ""), number_format($order_info['total'], 2, '', ''));
        $this->data['button_text'] = $this->config->get('pagar_me_checkout_texto_botao') ? $this->config->get('pagar_me_checkout_texto_botao') : "Pagar";
        $this->data['button_class'] = $this->config->get('pagar_me_checkout_button_css_class');
        $payment_methods = $this->config->get('pagar_me_checkout_payment_methods');
        if (count($payment_methods) == 1) {
            $this->data['payment_methods'] = $payment_methods[0];
        } else {
            $this->data['payment_methods'] = $payment_methods[0] . ',' . $payment_methods[1];
        }
        $card_brands = '';
        $card_brands_array = $this->config->get('pagar_me_checkout_card_brands');
        foreach ($card_brands_array as $card_brand) {
            if (reset($card_brands_array) == $card_brand) {
                $card_brands .= $card_brand;
            } else {
                $card_brands .= ',' . $card_brand;
            }
        }

        $this->data['card_brands'] = $card_brands;
        $this->data['max_installments'] = $this->config->get('pagar_me_checkout_max_installments');
        $this->data['free_installments'] = $this->config->get('pagar_me_checkout_free_installments');
        $this->data['ui_color'] = $this->config->get('pagar_me_checkout_ui_color');
        $this->data['postback_url'] = HTTP_SERVER . 'index.php?route=payment/pagar_me_checkout/callback';
        $this->data['customer_name'] = trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']);
        if ($this->config->get('dados_status')) {
            if ($customer['cpf'] != '') {
                $this->data['customer_document_number'] = $this->removeSeparadores($customer['cpf']);
            } else {
                $this->data['customer_document_number'] = $this->removeSeparadores($customer['cnpj']);
                $this->data['customer_name'] = $customer['razao_social'];

            }
            $this->data['customer_address_street_number'] = $order_info['payment_numero'];
            $this->data['customer_address_complementary'] = $order_info['payment_company'];
        } else {
            $this->data['customer_document_number'] = $this->removeSeparadores($order_info['payment_tax_id']);
            $this->data['customer_address_street_number'] = '';
            $this->data['customer_address_complementary'] = '';
        }

        $this->data['customer_email'] = $order_info['email'];
        $this->data['customer_address_street'] = $order_info['payment_address_1'];
        $this->data['customer_address_neighborhood'] = $order_info['payment_address_2'];
        $this->data['customer_address_city'] = $order_info['payment_city'];

        $this->load->model('localisation/zone');
        $uf = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);

        $this->data['customer_address_state'] = $uf['code'];
        $this->data['customer_address_zipcode'] = $this->removeSeparadores($order_info['payment_postcode']);
        $this->data['customer_phone_ddd'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2);
        $this->data['customer_phone_number'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2);
        $this->data['interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');


//        $this->data['button_confirm'] = $this->language->get('button_confirm');
//        $this->data['text_information'] = $this->language->get('text_information');
//        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['text_information'] = $this->config->get('pagar_me_checkout_text_information');
        $this->data['url'] = $this->url->link('payment/pagar_me_checkout/confirm', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_checkout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pagar_me_checkout.tpl';
        } else {
            $this->template = 'default/template/payment/pagar_me_checkout.tpl';
        }
        // incluindo css
//        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_checkout.css')) {
//            $this->data['stylesheet'] = 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css';
//        } else {
//            $this->data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me_cartao.css';
//        }

        $this->render();
    }

    public function confirm()
    {

        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_checkout');

        Pagarme::setApiKey($this->config->get('pagar_me_checkout_api'));

        $transaction = PagarMe_Transaction::findById($this->request->post['token']);
        $amount = $transaction->amount;

        try {
            $transaction->capture(array(
                'amount' => $amount,
                'metadata' => array(
                    'id_pedido' => $this->session->data['order_id'],
                    'loja' => $this->config->get('config_title'),
                )));
        } catch (Exception $e) {
            $this->log->write($e->getMessage() . " amount: " . $amount);
        }

        $status = $transaction->status;

        if ($transaction->status == 'authorized' || $transaction->status == 'paid') {
            $status = 'paid';
            $this->model_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, NULL);
        } else {
            $this->model_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, $transaction->boleto_url);
            $this->session->data['checkout_pagar_me_boleto_url'] = $transaction->boleto_url;
        }

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_checkout_order_' . $status), '', true);

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
        $this->load->model('payment/pagar_me_checkout');

        if ($event == 'transaction_status_changed') {

            $order_id = $this->model_payment_pagar_me_checkout->getPagarMeOrder($this->request->post['id']);

            $current_status = 'pagar_me_checkout_order_' . $this->request->post['current_status'];

            $this->model_checkout_order->update($order_id, $this->config->get($current_status), '', true);

        } else {
            $this->log->write("Pagar.Me boleto: Notificação inválida");
        }

        echo "OK";

    }

    private function removeSeparadores($string)
    {
        $nova_string = str_replace(array(',', '.', '-', '/', '(', ')', ' '), array('', '', '', '', '', '', ''), $string);

        return $nova_string;
    }
}

?>
