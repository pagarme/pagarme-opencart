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
        $json['checkoutProperties'] = $this->getCheckoutProperties();
        $json['customer'] = $this->generateCustomerInfo();
        $json['billing'] = $this->generateBilling();
        $json['shipping'] = $this->generateShipping();
        $json['items'] = $this->generateItemsArray();
        $json['checkoutProperties']  = $this->getCheckoutProperties();
        $this->response->setOutput(json_encode($json));
    }

    public function getCheckoutProperties(){
        $this->language->load('extension/payment/pagar_me_checkout');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);
        $discountPercentage = $this->config->get('pagar_me_checkout_boleto_discount_percentage');
        $discountAmount = ($this->cart->getSubtotal() * $discountPercentage) / 100;
        //Dados para o checkout
        $checkoutProperties=array();
        $checkoutProperties['amount'] = str_replace(array(".", ","), array("", ""), number_format($order_info['total'], 2, '', ''));
        $checkoutProperties['button_text'] = $this->config->get('pagar_me_checkout_texto_botao') ? $this->config->get('pagar_me_checkout_texto_botao') : "Pagar";
        $checkoutProperties['boleto_discount_amount'] = number_format($discountAmount, 2, '', '');
        $checkoutProperties['button_class'] = $this->config->get('pagar_me_checkout_button_css_class');
        $payment_methods = $this->config->get('pagar_me_checkout_payment_methods');
        if (count($payment_methods) == 1) {
            $checkoutProperties['payment_methods'] = $payment_methods[0];
        } else {
            $checkoutProperties['payment_methods'] = $payment_methods[0] . ',' . $payment_methods[1];
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
        $checkoutProperties['card_brands'] = $card_brands;
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
            $checkoutProperties['max_installments'] = $installments;
        }else{
            $checkoutProperties['max_installments'] = $max_installment;
        }
        $checkoutProperties['free_installments'] = $this->config->get('pagar_me_checkout_free_installments');
        $checkoutProperties['ui_color'] = $this->config->get('pagar_me_checkout_ui_color');
        $checkoutProperties['postback_url'] = HTTP_SERVER . 'index.php?route=extension/payment/pagar_me_checkout/callback';
        $checkoutProperties['interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');
        return $checkoutProperties;
    }

    public function getCustomerDocumentNumber(){
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->load->model('account/custom_field');
        $default_group = $this->config->get('config_customer_group_id');
        $custom_fields = $this->model_account_custom_field->getCustomFields($default_group);

        if(isset($customer['customer_group_id'])){
            $default_group = $customer['customer_group_id'];
        }
        foreach($custom_fields as $custom_field){
            if((strpos(strtolower($custom_field['name']), 'cpf') !== false) || (strpos(strtolower($custom_field['name']), 'cnpj') !== false)){
                $document_number =  $order_info['custom_field'][$custom_field['custom_field_id']];
            }
        }
        return  preg_replace('/\D/','',$document_number);
    }

    public function getCustomerDocumentType(){
        $document_number = $this->getCustomerDocumentNumber();
        return (strlen($document_number) == 11) ? 'cpf' : 'cnpj'; 
    }

    public function generateDocumentsArray(){
        $document_number = $this->getCustomerDocumentNumber();
        $document_type = $this->getCustomerDocumentType();
        return array(
            array(
            'number' => $document_number,
            'type' => $document_type
            )
        );
    }

    public function generateCustomerInfo(){
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $phone_numbers = array('+55' . $order_info['telephone']);
        $documents = $this->generateDocumentsArray(); 
        $customer = array(
            'name' => trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']),
            'email' => $order_info['email'],
            'external_id' => $order_info['customer_id'],
            'phone_numbers' => $phone_numbers,               
            'country' =>  strtolower($order_info['payment_iso_code_2']),
            'type' => $this->getCustomerDocumentType() == 'cpf' ? 'individual' : 'corporation',
            'documents' => $documents
        );
        return $customer;

    }

    public function generateBilling(){
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->load->model('localisation/zone');
        $customFields = $this->getAddressCustomFields('payment');

        $address = array(
            'street' => $order_info['payment_address_1'],
            'street_number' =>  $customFields['number'],
            'neighborhood' => $order_info['payment_address_2'],
            'complementary' => $customFields['complementary'],
            'city' => $order_info['payment_city'],
            'state' => $this->model_localisation_zone->getZone($order_info['payment_zone_id'])['name'],
            'zipcode' => preg_replace('/\D/','',$order_info['payment_postcode'] ),
            'country' =>  strtolower($order_info['payment_iso_code_2'])       
        );
        $billing = array(
            'address' => $address,
            'name' =>  trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']) 
        );
        return $billing; 
    }

    public function generateShipping(){
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->load->model('localisation/zone');
        $customFields = $this->getAddressCustomFields('shipping');
        $address = array(
            'street' => $order_info['shipping_address_1'],
            'street_number' =>  $customFields['number'],
            'neighborhood' => $order_info['shipping_address_2'],
            'complementary' => $customFields['complementary'],
            'city' => $order_info['shipping_city'],
            'state' => $this->model_localisation_zone->getZone($order_info['shipping_zone_id'])['name'],
            'zipcode' => preg_replace('/\D/','',$order_info['shipping_postcode'] ),
            'country' =>  strtolower($order_info['shipping_iso_code_2'])       
        );
        $shipping = array(
            'address' => $address,
            'name' =>  trim($order_info['shipping_firstname']) . ' ' . trim($order_info['shipping_lastname']),
            'fee' => preg_replace('/\D/','',$this->session->data['shipping_method']['cost']) 
        );

        return $shipping;
    }

    public function generateItemsArray(){
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
        return $items;
    }

    public function getAddressCustomFields($field){
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->load->model('account/custom_field');
        $default_group = $this->config->get('config_customer_group_id');
        $custom_fields = $this->model_account_custom_field->getCustomFields($default_group);
        $number = 'Sem número';
        $complementary = '';
        
        foreach($custom_fields as $custom_field){
            if($custom_field['location'] == 'address'){
                if(strpos(strtolower($custom_field['name']), 'numero') !== false || strpos(strtolower($custom_field['name']), 'número') !== false){
                    $number = $order_info[$field.'_custom_field'][$custom_field['custom_field_id']];
                }elseif(strpos(strtolower($custom_field['name']), 'complemento') !== false){
                    $complementary = $order_info[$field.'_custom_field'][$custom_field['custom_field_id']];
                }
            }
        }
        $addrressCustomFields = array(
            'number' => $number,
            'complementary' => $complementary
        );

        return $addrressCustomFields; 
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
