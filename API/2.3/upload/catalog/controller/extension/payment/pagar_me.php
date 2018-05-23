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
}
