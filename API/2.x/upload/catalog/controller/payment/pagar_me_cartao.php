<?php
require_once('pagar_me.php');
class ControllerPaymentPagarMeCartao extends ControllerPaymentPagarMe
{
    public function index()
    {

        $this->language->load('payment/pagar_me_cartao');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        if ($this->customer->isLogged()) {
            $data['nome_cartao'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        }

        $data['pagar_me_cartao_criptografia'] = $this->config->get('pagar_me_cartao_criptografia');
        $data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_information'] = $this->language->get('text_information');
        $data['text_wait'] = $this->language->get('text_wait');
        $data['text_information'] = $this->config->get('pagar_me_cartao_text_information');
        $data['url'] = $this->url->link('payment/pagar_me_cartao/confirm', '', 'SSL');

        /* Parcelas */
        Pagarme::setApiKey($this->config->get('pagar_me_cartao_api'));

        try {
            $numero_parcelas = floor($order_info['total'] / $this->config->get('pagar_me_cartao_valor_parcela'));

            $max_parcelas = $numero_parcelas ? $numero_parcelas : 1;

            if ($max_parcelas > $this->config->get('pagar_me_cartao_max_parcelas')) {
                $max_parcelas = $this->config->get('pagar_me_cartao_max_parcelas');
            }
            $data['interest_rate'] = $interest_rate = $this->config->get('pagar_me_cartao_taxa_juros');
            $data['free_installments'] = $free_installments = $this->config->get('pagar_me_cartao_parcelas_sem_juros');
            $this->session->data['calculated_installments'] =  $data['parcelas'] = PagarMe_Transaction::calculateInstallmentsAmount($data['total'], $interest_rate, $max_parcelas, $free_installments);
        } catch (Exception $e) {
            $this->log->write("Erro Pagar.me: " . $e->getMessage());
            $json['error'] = $e->getMessage();
        }

        $data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me.css';

        //Check if Opencart Version is equal to 2.2
        if(preg_match("/^2.2.*/", VERSION))
            return $this->load->view('payment/pagar_me_cartao.tpl', $data);

        return $this->load->view('default/template/payment/pagar_me_cartao.tpl', $data);
    }

    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_cartao');

        $status = $this->session->data['transaction_status'];

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $pagar_me_transaction = $this->model_payment_pagar_me_cartao->getPagarMeOrderByOrderId($order['order_id']);

        $comment = " Cartão: " . strtoupper($pagar_me_transaction['bandeira']) . "<br />";
        $comment .= " Parcelado em: " . $pagar_me_transaction['n_parcela'] . "x";
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_'.$status), $comment, true);

        $admin_comment = "Pagar.me Transaction: " . $pagar_me_transaction['transaction_id'] . "<br />";
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_'.$status), $admin_comment);

        $this->response->redirect($this->url->link('checkout/success'));
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

        $chosen_installments = $this->request->post['installments'];
        $amount = $this->session->data['calculated_installments']['installments'][$chosen_installments]['amount'];
        $interest_amount = (($amount / 100) - $order_info['total']);
        Pagarme::setApiKey($this->config->get('pagar_me_cartao_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $amount,
            'card_hash' => $this->request->post['card_hash'],
            'installments' => $chosen_installments,
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_cartao/callback',
            'async' => $this->config->get('pagar_me_cartao_async'),
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
                    "zipcode" => $this->removeSeparadores($order_info['payment_postcode']),
                ),
                "phone" => array(
                    "ddd" => substr(preg_replace('/\D/', '', $order_info['telephone']), 0, 2),
                    "number" => substr(preg_replace('/\D/', '', $order_info['telephone']), 2, 9),
                )
            ),
            'metadata' => array(
                'id_pedido' => $order_info['order_id'],
                'loja' => $this->config->get('config_name'),
            )));

        $json = array();
        try{
            $transaction->charge();

            if($transaction->status == 'processing' || $transaction->status == 'paid') {

                $this->session->data['transaction_status'] = $transaction->status;

                $this->load->model('payment/pagar_me_cartao');
                $this->model_payment_pagar_me_cartao->insertInterestRate($order_info['order_id'], $interest_amount);
                $this->model_payment_pagar_me_cartao->updateOrderAmount($order_info['order_id'], ($amount/100));

                $this->model_payment_pagar_me_cartao->addTransactionId($this->session->data['order_id'], $transaction->id, $chosen_installments, $this->request->post['bandeira']);

                $this->log->write('Pagar.me Transaction: '.$transaction->id. ' | Status: '.$transaction->status.' | Pedido: '.$order_info['order_id']);

                $json['success'] = true;

            } else {
                $json['error'] = "Ocorreu um erro ao realizar a transação. Que tal verificar os dados e tentar novamente?";
            }
        }catch(Exception $e){
            $this->log->write('Erro Pagar.me cartão: ' . $e->getMessage());
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
