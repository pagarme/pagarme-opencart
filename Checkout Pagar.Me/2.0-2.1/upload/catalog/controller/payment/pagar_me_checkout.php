<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeCheckout extends Controller
{

    public function index()
    {
        $this->language->load('payment/pagar_me_checkout');

        //Dados para o checkout
        $data['encryption_key'] = $this->config->get('pagar_me_checkout_criptografia');

        $data['text_information'] = $this->config->get('pagar_me_checkout_text_information');
        $data['customer_data'] = $this->config->get('pagar_me_checkout_customer_data');
        $data['url'] = $this->url->link('payment/pagar_me_checkout/confirm', '', 'SSL');
        $data['texto_botao'] = $this->config->get('pagar_me_checkout_texto_botao');
        $data['button_css_class'] = $this->config->get('pagar_me_checkout_button_css_class');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_checkout.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/pagar_me_checkout.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/pagar_me_checkout.tpl', $data);
        }


    }

    public function submit()
    {
        $this->language->load('payment/pagar_me_checkout');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        //Dados para o checkout
        $json=array();
        $json['amount'] = str_replace(array(".", ","), array("", ""), number_format($order_info['total'], 2, '', ''));
        $json['button_text'] = $this->config->get('pagar_me_checkout_texto_botao') ? $this->config->get('pagar_me_checkout_texto_botao') : "Pagar";
        $json['boleto_discount_percentage'] = $this->config->get('pagar_me_checkout_boleto_discount_percentage');
        $json['button_class'] = $this->config->get('pagar_me_checkout_button_css_class');
        $payment_methods = $this->config->get('pagar_me_checkout_payment_methods');
        if (count($payment_methods) == 1) {
            $json['payment_methods'] = $payment_methods[0];
        } else {
            $json['payment_methods'] = $payment_methods[0] . ',' . $payment_methods[1];
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

        $json['card_brands'] = $card_brands;

        /* Máximo de parcelas */
        $max_installment = $this->config->get('pagar_me_checkout_max_installments');
        $order_total = $order_info['total'];
        $max_installment_value = $this->config->get('pagar_me_checkout_max_installment_value');

        if($max_installment_value) {
            $installments = floor($order_total / $max_installment_value);
        }else{
            $installments = 0;
        }

        if($installments <= $max_installment) {
            $json['max_installments'] = $installments;
        }else{
            $json['max_installments'] = $max_installment;
        }
        $json['free_installments'] = $this->config->get('pagar_me_checkout_free_installments');
        $json['ui_color'] = $this->config->get('pagar_me_checkout_ui_color');
        $json['postback_url'] = HTTP_SERVER . 'index.php?route=payment/pagar_me_checkout/callback';
        $json['customer_name'] = trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']);

        $json['customer_address_street_number'] = 'Sem número';
        $json['customer_address_complementary'] = '';

        /* Pega os custom fields de CPF/CNPJ, número e complemento */
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields($customer['customer_group_id']);
        foreach($custom_fields as $custom_field){
            if($custom_field['location'] == 'account'){
                if(strpos(strtolower($custom_field['name']), 'cpf') !== false){
                    $json['customer_document_number'] = $order_info['custom_field'][$custom_field['custom_field_id']];
                }
            }elseif($custom_field['location'] == 'address'){
                if(strpos(strtolower($custom_field['name']), 'numero') !== false || strpos(strtolower($custom_field['name']), 'número') !== false){
                    $json['customer_address_street_number'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                }elseif(strpos(strtolower($custom_field['name']), 'complemento')){
                    $json['customer_address_complementary'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                }
            }
        }

        $json['customer_email'] = $order_info['email'];
        $json['customer_address_street'] = $order_info['payment_address_1'];
        $json['customer_address_neighborhood'] = $order_info['payment_address_2'];
        $json['customer_address_city'] = $order_info['payment_city'];

        $this->load->model('localisation/zone');
        $uf = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);

        $json['customer_address_state'] = $uf['code'];
        $json['customer_address_zipcode'] = $this->removeSeparadores($order_info['payment_postcode']);
        $json['customer_phone_ddd'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2);
        $json['customer_phone_number'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2);
        $json['interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');


        $this->response->setOutput(json_encode($json));
    }

    public function confirm()
    {
        if($this->session->data['payment_method']['code'] == 'pagar_me_checkout') {
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
                        'loja' => $this->config->get('config_name'),
                    )));
            } catch (Exception $e) {
                $this->log->write($e->getMessage());
            }

            $status = $transaction->status;

            if ($transaction->status == 'authorized' || $transaction->status == 'paid') {
                $status = 'paid';
                $comentario = "N&uacute;mero da transa&ccedil;&atilde;o: " . $transaction->id . "<br />";
                $comentario .= " Cartão: " . strtoupper($transaction->card->brand) . "<br />";
                $comentario .= " Parcelado em: " . $transaction->installments . "x";
                $this->model_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, NULL, $transaction->installments, $transaction->card->brand);
            } else {
                $this->model_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, $transaction->boleto_url);
                /* Adiciona desconto do boleto ao pedido para que o total seja calculado corretamente */
                /* Pega a ordem do sub-total do pedido e acrescenta o desconto ao pedido */
                if($this->config->get('pagar_me_checkout_boleto_discount_percentage'))
                    $this->model_payment_pagar_me_checkout->addDescontoBoleto($this->session->data['order_id']);
                $this->session->data['checkout_pagar_me_boleto_url'] = $transaction->boleto_url;
                $comentario  = "Para imprimir seu boleto <a href='" . $transaction->boleto_url . "'>clique aqui</a>";
            }

            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pagar_me_checkout_order_' . $status), $comentario, true);

            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
    }

    public function callback()
    {

        try {
            $this->log->write($this->request->post['event']);

            $event = $this->request->post['event'];
            $this->load->model('checkout/order');
            $this->load->model('payment/pagar_me_checkout');

            if ($event == 'transaction_status_changed') {

                $order_id = $this->model_payment_pagar_me_checkout->getPagarMeOrder($this->request->post['id']);

                $current_status = $this->config->get('pagar_me_checkout_order_' . $this->request->post['current_status']);


                if(!$this->model_payment_pagar_me_checkout->getTotalOrderHistoriesByOrderStatusId($current_status, $order_id)) {
                    $this->model_checkout_order->addOrderHistory($order_id, $current_status, '', true);
                }


            } else {
                $this->log->write("Pagar.Me boleto: Notificação inválida");
            }
            echo "OK";
        }catch (Exception $e){
            $this->log->write($e->getMessage());
        }



    }

    private function removeSeparadores($string)
    {
        $nova_string = str_replace(array(',', '.', '-', '/', '(', ')', ' '), array('', '', '', '', '', '', ''), $string);

        return $nova_string;
    }

}

?>
