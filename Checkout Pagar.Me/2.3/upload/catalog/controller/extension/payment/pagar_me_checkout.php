<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerExtensionPaymentPagarMeCheckout extends Controller
{

    public function index()
    {
        $this->language->load('extension/payment/pagar_me_checkout');

        //Dados para o checkout
        $data['encryption_key'] = $this->config->get('pagar_me_checkout_criptografia');

        $data['text_information'] = $this->config->get('pagar_me_checkout_text_information');
        $data['url'] = $this->url->link('extension/payment/pagar_me_checkout/confirm', '', 'SSL');
        $data['texto_botao'] = $this->config->get('pagar_me_checkout_texto_botao');
        $data['button_css_class'] = $this->config->get('pagar_me_checkout_button_css_class');

        return $this->load->view('extension/payment/pagar_me_checkout.tpl', $data);
    }

    public function submit()
    {
        $this->language->load('extension/payment/pagar_me_checkout');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $discountPercentage = $this->config->get('pagar_me_checkout_boleto_discount_percentage');
        $discountAmount = ($this->cart->getSubtotal() * $discountPercentage) / 100;

        //Dados para o checkout
        $json=array();
        $json['amount'] = str_replace(array(".", ","), array("", ""), number_format($order_info['total'], 2, '', ''));
        $json['button_text'] = $this->config->get('pagar_me_checkout_texto_botao') ? $this->config->get('pagar_me_checkout_texto_botao') : "Pagar";
        $json['boleto_discount_amount'] = number_format($discountAmount, 2, '', '');
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
        $json['postback_url'] = HTTP_SERVER . 'index.php?route=extension/payment/pagar_me_checkout/callback';
        $json['customer_name'] = trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']);

        $json['customer_address_street_number'] = 'Sem número';
        $json['customer_address_complementary'] = '';
        $json['customer_external_id'] = $order_info['customer_id'];

        /* Pega os custom fields de CPF/CNPJ, número e complemento */
        $this->load->model('account/custom_field');

        $default_group = $this->config->get('config_customer_group_id');
        if(isset($customer['customer_group_id'])){
            $default_group = $customer['customer_group_id'];
        }

        $custom_fields = $this->model_account_custom_field->getCustomFields($default_group);
        foreach($custom_fields as $custom_field){
            if($custom_field['location'] == 'account'){
                if((strpos(strtolower($custom_field['name']), 'cpf') !== false) || (strpos(strtolower($custom_field['name']), 'cnpj') !== false)){
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
        
        $json['customer_type'] = (strlen(preg_replace('/\D/','',$json['customer_document_number']))) == 11 ? 'individual' : 'corporation'; 
        $json['document_type'] = ($json['customer_type'] == 'individual' ? 'cpf' : 'cnpj');
        $json['customer_country'] = strtolower($order_info['payment_iso_code_2']);
        $json['customer_email'] = $order_info['email'];

        //Billing
        $json['customer_address_street'] = $order_info['payment_address_1'];
        $json['customer_address_neighborhood'] = $order_info['payment_address_2'];
        $json['customer_address_city'] = $order_info['payment_city'];

        $this->load->model('localisation/zone');
        $uf = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);

        $json['customer_address_state'] = $uf['name'];
        $json['customer_address_zipcode'] = preg_replace('/\D/','',$order_info['payment_postcode']);
        $json['interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');
        $phone_numbers = array();
        array_push($phone_numbers,'+55' . $order_info['telephone']);
        $json['phone_numbers'] = $phone_numbers;
        //Shipping 

        $json['fee'] = preg_replace('/\D/','',$this->session->data['shipping_method']['cost']);

        //Items
        $items = array();
        $cart_info = $this->cart->getProducts();
        foreach($cart_info as $item){
            $unit_price = $item['price'];
             if(strpos($item['price'], ".") !== false || strpos($item['price'], ",") !== false){
                $unit_price = preg_replace('/\D/','',$unit_price);
            } else {
                $unit_price = $item['price'] * 100;
            }
            $tangible = empty($item['download']) ? true : false;
            array_push(
                $items,
                array(
                    'id' => $item['product_id'],
                    'title' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit_price,
                    'tangible' => $tangible
                )
            );
        }
        $json['items'] = $items;
        $this->response->setOutput(json_encode($json));
    }

    public function confirm()
    {
        if($this->session->data['payment_method']['code'] == 'pagar_me_checkout') {
            $this->load->model('checkout/order');
            $this->load->model('extension/payment/pagar_me_checkout');

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
                $this->log->write('Pagar.me Checkout erro: '.$e->getMessage());
            }

            $status = $transaction->status;

            if ($transaction->status == 'authorized' || $transaction->status == 'paid') {
                $status = 'paid';
                $comentario = "N&uacute;mero da transa&ccedil;&atilde;o: " . $transaction->id . "<br />";
                $comentario .= " Cartão: " . strtoupper($transaction->card->brand) . "<br />";
                $comentario .= " Parcelado em: " . $transaction->installments . "x";
                $this->model_extension_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, NULL);
            } else {
                $this->model_extension_payment_pagar_me_checkout->addTransactionId($this->session->data['order_id'], $transaction->id, $transaction->boleto_url);
                /* Adiciona desconto do boleto ao pedido para que o total seja calculado corretamente */
                /* Pega a ordem do sub-total do pedido e acrescenta o desconto ao pedido */
                if($this->config->get('pagar_me_checkout_boleto_discount_percentage'))
                    $this->model_extension_payment_pagar_me_checkout->addDescontoBoleto($this->session->data['order_id']);
                $this->session->data['checkout_pagar_me_boleto_url'] = $transaction->boleto_url;
                $comentario  = "Para imprimir seu boleto <a href='" . $transaction->boleto_url . "'>clique aqui</a>";
            }

            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pagar_me_checkout_order_' . $status), $comentario, true);

            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
    }

    public function callback()
    {
        Pagarme::setApiKey($this->config->get('pagar_me_checkout_api'));

        $requestBody = file_get_contents("php://input");
        $xHubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

        if(!PagarMe::validateRequestSignature($requestBody, $xHubSignature)){
            $this->log->write("Pagar.me Postback: Falha ao validar o POSTback");

            return http_response_code(403);
        }

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/pagar_me_checkout');

        $order_id = $this->model_extension_payment_pagar_me_checkout->getPagarMeOrder($this->request->post['id']);

        $current_status = 'pagar_me_checkout_order_' . $this->request->post['current_status'];

        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get($current_status), '', true);

        $this->log->write('Pagar.me Postback: Pedido ' . $order_id . ' atualizado para ' . $this->request->post['current_status']);

        echo "Ok";

    }

    private function removeSeparadores($string)
    {
        $nova_string = str_replace(array(',', '.', '-', '/', '(', ')', ' '), array('', '', '', '', '', '', ''), $string);

        return $nova_string;
    }

}

?>
