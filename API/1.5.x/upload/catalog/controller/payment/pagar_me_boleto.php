<?php

require ('pagar_me.php');
class ControllerPaymentPagarMeBoleto extends ControllerPaymentPagarMe
{
    protected function index()
    {
        $this->language->load('payment/pagar_me_boleto');
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);
        $this->data['total'] = str_replace(".", "", number_format($order_info['total'], 2, ".", ""));
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['text_information'] = $this->language->get('text_information');
        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['text_information'] = $this->config->get('pagar_me_boleto_text_information');
        $this->data['url'] = $this->url->link('payment/pagar_me_boleto/confirm', '', 'SSL');
        $this->data['url2'] = $this->url->link('payment/pagar_me_boleto/error', '', 'SSL');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_boleto.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pagar_me_boleto.tpl';
        } else {
            $this->template = 'default/template/payment/pagar_me_boleto.tpl';
        }
        // incluindo css
        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_boleto.css')) {
            $this->data['stylesheet'] = 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css';
        } else {
            $this->data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me_cartao.css';
        }
        $this->render();
    }

    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_boleto');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $pagar_me_transaction = $this->model_payment_pagar_me_boleto->getPagarMeOrderByOrderId($order['order_id']);

        $comment = "<a href='".htmlspecialchars($order['pagar_me_boleto_url'])."'>Imprima seu boleto aqui</a>";
        $this->model_checkout_order->confirm($order['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), $comment, true);

        $admin_comment = "Pagar.me Transaction: ". $pagar_me_transaction['transaction_id'];
        $this->model_checkout_order->update($order['order_id'], $this->config->get('pagar_me_boleto_order_waiting_payment'), $admin_comment, false);

        $this->session->data['pagar_me_boleto_url'] = $order['pagar_me_boleto_url'];
        $this->redirect($this->url->link('checkout/success'));
    }

    public function gera()
    {
        $boleto_url = $this->request->get['boleto'];
        $this->redirect($boleto_url);
    }

    public function payment()
    {
        $this->load->model('checkout/order');
        $this->load->model('account/customer');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        $possibleDocumentFields = array(
            'cpf', 'cnpj', 'document_number', 'payment_tax_id'
        );

        foreach($possibleDocumentFields as $document){
            if(isset($order_info[$document])){
                $document_number = $this->removeSeparadores($order_info[$document]);
            }
        }
        $documentNumberLenght = strlen($document_number);

        $isCpf = $documentNumberLenght == 11;
        $isCnpj = $documentNumberLenght == 14;

        if ($isCpf) {
            $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
        } elseif ($isCnpj) {
            $customer_name = $customer['razao_social'];
        }

        $numero = isset($order_info['payment_numero']) ? $order_info['payment_numero'] : 'Sem número';
        $complemento = isset($order_info['payment_company']) ? $order_info['payment_company'] : '';

        Pagarme::setApiKey($this->config->get('pagar_me_boleto_api'));
        $transaction = new PagarMe_Transaction(array(
            'amount' => $this->request->post['amount'],
            'async' => false,
            'payment_method' => 'boleto',
            'boleto_expiration_date' => date('Y-m-d', strtotime('+' . $this->config->get('pagar_me_boleto_dias_vencimento') + 1 . ' days')),
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_boleto/callback',
            "customer" => array(
                "name" => $customer_name,
                "document_number" => $document_number,
                "email" => $order_info['email'],
                "address" => array(
                    "street" => $order_info['payment_address_1'],
                    "neighborhood" => $order_info['payment_address_2'],
                    "zipcode" => $this->removeSeparadores($order_info['payment_postcode']),
                    "street_number" => $numero,
                    "complementary" => $complemento
                ),
                "phone" => array(
                    "ddd" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2),
                    "number" => substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2),
                )
            ),
            'metadata' => array(
                'id_pedido' => $order_info['order_id'],
                'loja' => $this->config->get('config_title'),
            ))
        );

        $json = array();

        try {
            $transaction->charge();

            $this->load->model('payment/pagar_me_boleto');

            $this->model_payment_pagar_me_boleto->addTransactionId($this->session->data['order_id'], $transaction->id, $transaction->boleto_url);
            $this->log->write('Pagar.me Transaction: '. $transaction->id.' | Pedido: '.$order_info["order_id"].' | Status: '.$transaction->status);

            $json['transaction'] = $transaction->id;
            $json['success'] = true;
            $json['pagar_me_boleto_url'] = $transaction->boleto_url;
        } catch (Exception $e) {
            $this->log->write("Erro Pagar.me boleto: " . $e->getMessage());
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
