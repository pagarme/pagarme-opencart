<?php
require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';
abstract class ControllerExtensionPaymentPagarMe extends Controller
{
    public function callback()
    {
        Pagarme::setApiKey($this->config->get('pagar_me_'.$this->getPaymentMethod().'_api'));
 
        $requestBody = file_get_contents("php://input");
        $xHubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

        if(!PagarMe::validateRequestSignature($requestBody, $xHubSignature)) {
            $this->log->write('Pagar.me Postback: Dados inválidos');

            return http_response_code(400);
        }

        if(!isset($this->request->post['transaction']['metadata']['id_pedido'])){            
            return http_response_code(400);
        }

        $order_id = $this->request->post['transaction']['metadata']['id_pedido'];

        $this->updateOrderStatus($order_id);

        return http_response_code(200);

    }

    public function getCustomerDocumentNumber($customer, $order_info)
    {
        $custom_fields = $this->getCustomFields($customer);

        foreach($custom_fields as $custom_field){
            if((strpos(strtolower($custom_field['name']), 'cpf') !== false) || (strpos(strtolower($custom_field['name']), 'cnpj') !== false)){
                return $order_info['custom_field'][$custom_field['custom_field_id']];
            }
        }

        return '';
    }

    public function getCustomerAdditionalAddressData($customer, $order_info)
    {
        $custom_fields = $this->getCustomFields($customer);

        $address_data = array(
            'street_number' => 'S/N',
            'complementary' => 'Sem complemento'
        );

        foreach($custom_fields as $custom_field) {
            if($custom_field['location'] == 'address') {
                if((strpos(strtolower($custom_field['name']), 'numero') !== false) || (strpos(strtolower($custom_field['name']), 'número') !== false)) {
                    $address_data['street_number'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                } elseif(strtolower($custom_field['name']) == 'complemento'){
                    $address_data['complementary'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                }
            }
        }

        return $address_data;
    }

    private function getCustomFields($customer)
    {
        $this->load->model('account/custom_field');

        $default_group = $this->config->get('config_customer_group_id');
        if(isset($customer['customer_group_id'])) {
            $default_group = $customer['customer_group_id'];
        }

        return $this->model_account_custom_field->getCustomFields($default_group);
    }

    private function getPaymentMethod()
    {
        if($this->request->post['transaction']['payment_method'] != 'boleto') {
            return 'cartao';
        }

        return 'boleto';
    }

    private function updateOrderStatus($order_id)
    {
        $this->load->model('checkout/order');

        $pagar_me_current_status = $this->request->post['current_status'];
        $opencart_order_status = $this->config->get('pagar_me_'.$this->getPaymentMethod().'_order_'.$pagar_me_current_status);

        $this->model_checkout_order->addOrderHistory($order_id, $opencart_order_status, '', true);

        $this->log->write('Pagar.me Postback: Pedido '.$order_id.' atualizado para '.$pagar_me_current_status);
    }

    public function generateItemsArray()
    {
        $items = array(); 
        $cart_info = $this->cart->getProducts(); 
        foreach($cart_info as $item){
            $unit_price =  $item['price'];
            if(strpos($item['price'], ".") !== false || strpos($item['price'], ",") !== false){
                $unit_price = $this->removeSeparadores($item['price']);
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
                    'tangible'=> $tangible
                )
            );
        }
        return $items; 
    }

    public function generateCustomerInfo()
    {
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customerModel = $this->model_account_customer->getCustomer($order_info['customer_id']);
        $document_number =  preg_replace('/\D/', '', $this->getCustomerDocumentNumber($customerModel, $order_info));
        $document_type = 'cpf';
        $customer_type = 'individual';
        if (11 < strlen($document_number)) {
            $document_type = 'cnpj';
            $customer_type = 'corporation';
        }
        $documents = array(
            'number' => $document_number,
            'type' => $document_type
        );
        $customer_name = trim($order_info['payment_firstname']).' '.trim($order_info['payment_lastname']);
        $phone_numbers = array('+55' . $order_info['telephone']);
        return array(
            'name'=> $customer_name,
            'external_id'=> $order_info['customer_id'],
            'type'=> $customer_type,
            'country'=> strtolower($order_info['payment_iso_code_2']),
            'documents' => $documents, 
            'email'=> $order_info['email'],
            'phone_numbers'=> $phone_numbers
        );
    }

    public function generateBillingData()
    {        
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customerModel = $this->model_account_customer->getCustomer($order_info['customer_id']);
        $customer_address = $this->getCustomerAdditionalAddressData($customerModel, $order_info);
        $billing = array(
                'name' => trim($order_info['payment_firstname']).' '.trim($order_info['payment_lastname']),
                'address'=> array(
                    'street' => $order_info['payment_address_1'],
                    'street_number' => $customer_address['street_number'],
                    'neighborhood' => $order_info['payment_address_2'],
                    'complementary' => $customer_address['complementary'],
                    'city' => $order_info['payment_city'],
                    'state' => $order_info['payment_zone_code'],
                    'country' => strtolower($order_info['payment_iso_code_2']),
                    'zipcode' => preg_replace('/\D/', '', $order_info['payment_postcode'])
                )
        );

        return $billing;
    }

    public function generateShippingData()
    {
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customerModel = $this->model_account_customer->getCustomer($order_info['customer_id']);
        $customer_address = $this->getCustomerAdditionalAddressData($customerModel, $order_info);
        $shipping = array(
                'fee' => preg_replace('/\D/', '', $this->session->data['shipping_method']['cost']),
                'name' => trim($order_info['payment_firstname']).' '.trim($order_info['payment_lastname']),
                'address' => array(
                    'street' => $order_info['shipping_address_1'],
                    'street_number' => $customer_address['street_number'],
                    'neighborhood' => $order_info['shipping_address_2'],
                    'complementary' => $customer_address['complementary'],
                    'city' => $order_info['shipping_city'],
                    'state' => $order_info['shipping_zone_code'],
                    'country' => strtolower($order_info['shipping_iso_code_2']),
                    'zipcode' => preg_replace('/\D/', '', $order_info['shipping_postcode']))
                );

        return $shipping;
    }
}
