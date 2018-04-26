<?php

require ('pagar_me.php');

class ControllerPaymentPagarMeCartao extends ControllerPaymentPagarMe
{
    protected function index()
    {

        $this->language->load('payment/pagar_me_cartao');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        if ($this->customer->isLogged()) {
            $this->data['nome_cartao'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        }

        $this->data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['text_information'] = $this->language->get('text_information');
        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['text_information'] = $this->config->get('pagar_me_cartao_text_information');
        $this->data['customer_document_number'] = $this->getCustomerDocumentNumber();
        $this->data['url'] = $this->url->link('payment/pagar_me_cartao/confirm', '', 'SSL');
        $this->data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me.css';

        /* Parcelas */
        $json = array();

        Pagarme::setApiKey($this->config->get('pagar_me_cartao_api'));

        try {

            $numero_parcelas = floor($order_info['total'] / $this->config->get('pagar_me_cartao_valor_parcela'));

            $max_parcelas = $numero_parcelas ? $numero_parcelas : 1;

            if ($max_parcelas > $this->config->get('pagar_me_cartao_max_parcelas')) {
                $max_parcelas = $this->config->get('pagar_me_cartao_max_parcelas');
            }

            $this->data['interest_rate'] = $interest_rate = $this->config->get('pagar_me_cartao_taxa_juros');
            $this->data['free_installments'] = $free_installments = $this->config->get('pagar_me_cartao_parcelas_sem_juros');

            $this->session->data['calculated_installments'] = $this->data['parcelas'] = PagarMe_Transaction::calculateInstallmentsAmount($this->data['total'], $interest_rate, $max_parcelas, $free_installments);

        } catch (Exception $e) {
            $this->log->write("Erro Pagar.me: " . $e->getMessage());
            $json['error'] = $e->getMessage();
        }

        $this->template = 'default/template/payment/pagar_me_cartao.tpl';

        $this->render();
    }

    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_cartao');

        $status = $this->session->data['transaction_status'];

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $pagar_me_transaction = $this->model_payment_pagar_me_cartao->getPagarMeOrderByOrderId($this->session->data['order_id']);

        $comment = " Cartão: " . strtoupper($pagar_me_transaction['bandeira']) . "<br />";
        $comment .= " Parcelado em: " . $pagar_me_transaction['n_parcela'] . "x";
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_'.$status), $comment, true);

        $admin_comment = "Pagar.me Transaction: " . $pagar_me_transaction['transaction_id'] . "<br />";
        $this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_'.$status), $admin_comment);

        $this->redirect($this->url->link('checkout/success'));
   }

    public function payment()
    {

        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $document_number = $this->getCustomerDocumentNumber() ? $this->getCustomerDocumentNumber() : $this->request->post['document_number'];
        $document_number = preg_replace('/\D/', '', $document_number);

        $documentNumberLenght = strlen($document_number);

        $isCpf = $documentNumberLenght == 11;
        $isCnpj = $documentNumberLenght == 14;

        $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
        if (!$isCpf && isset($customer_name['razao_social'])) {
            $customer_name = $customer['razao_social'];
        }

        $numero = isset($order_info['payment_numero']) ? $order_info['payment_numero'] : 'Sem número';
        $complemento = isset($order_info['payment_company']) ? $order_info['payment_company'] : '';


        $chosen_installments = $this->request->post['installments'];
        $amount = $this->session->data['calculated_installments']['installments'][$chosen_installments]['amount'];
        $interest_amount = $amount - ($order_info['total'] * 100);

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
                    "neighborhood" => $order_info['payment_address_2'],
                    "zipcode" => $order_info['payment_postcode'],
                    "street_number" => $numero,
                    "complementary" => $complemento
                ),
                "phone" => array(
                    "ddd" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2),
                    "number" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2, 9),
                )
            ),
            'metadata' => array(
                'id_pedido' => $order_info['order_id'],
                'loja' => $this->config->get('config_title'),
            )
        ));

        $json = array();

        try{
            $transaction->charge();
            if($transaction->status == 'processing' || $transaction->status == 'paid'){

                $this->session->data['transaction_status'] = $transaction->status;

                $this->load->model('payment/pagar_me_cartao');

                $this->model_payment_pagar_me_cartao->insertInterestRate($order_info['order_id'], $interest_amount);
                $this->model_payment_pagar_me_cartao->updateOrderAmount($order_info['order_id'], $amount);
                $this->model_payment_pagar_me_cartao->addTransactionId($order_info['order_id'], $transaction->id, $chosen_installments, $this->request->post['bandeira']);

                $this->log->write('Pagar.me Transaction: '.$transaction->id.' | Status: '.$transaction->status.' | Pedido: '.$order_info['order_id']);
                $json['success'] = true;
            }else{
                $json['error'] = 'Ocorreu um erro ao realizar a transação. Que tal verificar os dados e tentar novamente?';
            }
        }catch(Exception $e){
            $this->log->write('Erro Pagar.me cartão: '.$e->getMessage());
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
