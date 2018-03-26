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
