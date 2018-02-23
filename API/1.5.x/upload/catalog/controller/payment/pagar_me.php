<?php
require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';
abstract class ControllerPaymentPagarMe extends Controller
{
    public function callback()
    {
        Pagarme::setApiKey($this->config->get('pagar_me_'.$this->getPaymentMethod().'_api'));

        $requestBody = file_get_contents("php://input");
        $xHubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

        if(!PagarMe::validateRequestSignature($requestBody, $xHubSignature)) {
            $this->log->write('Pagar.me Postback: Dados inválidos');

            return header("HTTP/1.0 400 Invalid POSTback data");
        }

        if(!isset($this->request->post['transaction']['metadata']['id_pedido'])){
            $this->log->write('Pagar.me Postback: Pedido não encontrado');

            return header("HTTP/1.0 400 Order not found");
        }

        $order_id = $this->request->post['transaction']['metadata']['id_pedido'];

        $this->updateOrderStatus($order_id);

        return header("HTTP/1.0 200 Order status updated");

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
        $opencart_order_status = $this->config->get('pagar_me_'.$this->getPaymentMethod().'_order_' . $pagar_me_current_status);

        $this->model_checkout_order->update($order_id, $opencart_order_status, '', true);

        $this->log->write('Pagar.me Postback: Pedido '.$order_id.' atualizado para '.$pagar_me_current_status);
    }        

}
