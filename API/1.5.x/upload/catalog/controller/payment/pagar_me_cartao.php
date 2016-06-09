<?php

require_once DIR_SYSTEM . 'library/PagarMe/Pagarme.php';

class ControllerPaymentPagarMeCartao extends Controller
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
        $this->data['url'] = $this->url->link('payment/pagar_me_cartao/confirm', '', 'SSL');
        $this->data['url2'] = $this->url->link('payment/pagar_me_cartao/error', '', 'SSL');

        /* Parcelas */
        Pagarme::setApiKey($this->config->get('pagar_me_cartao_api'));

        try {

            $numero_parcelas = floor($order_info['total'] / $this->config->get('pagar_me_cartao_valor_parcela'));

            $max_parcelas = $numero_parcelas ? $numero_parcelas : 1;

            if ($max_parcelas > $this->config->get('pagar_me_cartao_max_parcelas')) {
                $max_parcelas = $this->config->get('pagar_me_cartao_max_parcelas');
            }

            $this->data['parcelas'] = PagarMe_Transaction::calculateInstallmentsAmount($this->data['total'], $this->config->get('pagar_me_cartao_taxa_juros'), $max_parcelas, $this->config->get('pagar_me_cartao_parcelas_sem_juros'));
        } catch (Exception $e) {
            $this->log->write("Erro Pagar.me: " . $e->getTraceAsString());
            die();
        }


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_cartao.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pagar_me_cartao.tpl';
        } else {
            $this->template = 'default/template/payment/pagar_me_cartao.tpl';
        }
        // incluindo css
        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css')) {
            $this->data['stylesheet'] = 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/pagar_me_cartao.css';
        } else {
            $this->data['stylesheet'] = 'catalog/view/theme/default/stylesheet/pagar_me_cartao.css';
        }

        $this->render();
    }

    public function confirm()
    {


        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_cartao');

        $result = $this->model_payment_pagar_me_cartao->getPagarMeOrderByOrderId($this->session->data['order_id']);

        $comentario = "N&uacute;mero da transa&ccedil;&atilde;o: " . $result['transaction_id'] . "<br />";
        $comentario .= " Cartão: " . strtoupper($result['bandeira']) . "<br />";
        $comentario .= " Parcelado em: " . $result['n_parcela'] . "x";

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_processing'), $comentario, true);

        $this->redirect($this->url->link('checkout/success'));
    }

    public function error()
    {


        $this->load->model('checkout/order');

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pagar_me_cartao_order_refused'));

        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
        }

        $this->language->load('payment/pagar_me_cartao');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'text' => $this->language->get('text_checkout'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('payment/cielo_message'),
            'text' => $this->language->get('text_no_success'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        if ($this->customer->isLogged()) {
            $this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/order', '', 'SSL'), $this->url->link('information/contact'));
        } else {
            $this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
        }

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagar_me_cartao_message.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pagar_me_cartao_message.tpl';
        } else {
            $this->template = 'default/template/payment/pagar_me_cartao_message.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function callback()
    {

        $event = $this->request->post['event'];
        $this->load->model('checkout/order');
        $this->load->model('payment/pagar_me_cartao');

        if ($event == 'transaction_status_changed') {

            $order_id = $this->model_payment_pagar_me_cartao->getPagarMeOrder($this->request->post['id']);

            $current_status = $this->config->get('pagar_me_cartao_order_' . $this->request->post['current_status']);

            if(!$this->model_payment_pagar_me_cartao->getTotalOrderHistoriesByOrderStatusId($current_status, $order_id)) {
                $this->model_checkout_order->update($order_id, $this->config->get($current_status), '', true);
            }
        } else {
            $this->log->write("Pagar.Me cartão de crédito: Notificação inválida");
        }

        echo "OK";
    }

    public function payment()
    {

        $this->load->model('checkout/order');
        $this->load->model('account/customer');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

        if ($this->config->get('dados_status')) {
            if ($customer['cpf'] != '') {
                $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
            } else {
                $customer_name = $customer['razao_social'];

            }
            $numero = $order_info['payment_numero'];
            $complemento = $order_info['payment_company'];
        } else {
            $customer_name = $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
            $numero = 'Sem número';
            $complemento = '';
        }

        Pagarme::setApiKey($this->config->get('pagar_me_cartao_api'));

        $transaction = new PagarMe_Transaction(array(
            'amount' => $this->request->post['amount'],
            'card_hash' => $this->request->post['card_hash'],
            'installments' => $this->request->post['installments'],
            'postback_url' => HTTP_SERVER . 'index.php?route=payment/pagar_me_cartao/callback',
            "customer" => array(
                "name" => $customer_name,
                "document_number" => $this->request->post['cpf_customer'],
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
            )));

        $transaction->charge();

        $status = $transaction->status; // status da transação

        $id_transacao = $transaction->id;

        $json = array();

        $this->log->write($status);

        $this->load->model('payment/pagar_me_cartao');

        if ($status == 'paid' || $status == 'processing') {
            $this->model_payment_pagar_me_cartao->addTransactionId($this->session->data['order_id'], $id_transacao, $this->request->post['installments'], $this->request->post['bandeira']);

            $json['success'] = true;
        } else {
            $this->model_payment_pagar_me_cartao->addTransactionId($this->session->data['order_id'], $id_transacao);
            $json['success'] = false;
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
