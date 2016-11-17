<?php

class ControllerPaymentPagarMeCheckout extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('payment/pagar_me_checkout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/pagar_me_checkout/colorpicker/js/colorpicker.js');

        $this->document->addStyle('view/javascript/pagar_me_checkout/colorpicker/css/colorpicker.css');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('pagar_me_checkout', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');

        $this->data['entry_criptografia'] = $this->language->get('entry_criptografia');
        $this->data['entry_api'] = $this->language->get('entry_api');
        $this->data['entry_nome'] = $this->language->get('entry_nome');        
        $this->data['entry_customer_data'] = $this->language->get('entry_customer_data');        
        $this->data['entry_texto_botao'] = $this->language->get('entry_texto_botao');
        $this->data['entry_payment_methods'] = $this->language->get('entry_payment_methods');
        $this->data['entry_card_brands'] = $this->language->get('entry_card_brands');
        $this->data['entry_max_installments'] = $this->language->get('entry_max_installments');
        $this->data['entry_free_installments'] = $this->language->get('entry_free_installments');
        $this->data['entry_max_installment_value'] = $this->language->get('entry_max_installment_value');
        $this->data['entry_interest_rate'] = $this->language->get('entry_insterest_rate');
        $this->data['entry_ui_color'] = $this->language->get('entry_ui_color');
        $this->data['entry_button_css_class'] = $this->language->get('entry_button_css_class');
        $this->data['entry_text_information'] = $this->language->get('entry_text_information');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_order_waiting_payment'] = $this->language->get('entry_order_waiting_payment');
        $this->data['entry_order_paid'] = $this->language->get('entry_order_paid');
        $this->data['entry_order_processing'] = $this->language->get('entry_order_processing');
        $this->data['entry_order_authorized'] = $this->language->get('entry_order_authorized');
        $this->data['entry_order_pending_refund'] = $this->language->get('entry_order_pending_refund');
        $this->data['entry_order_refunded'] = $this->language->get('entry_order_refunded');
        $this->data['entry_order_refused'] = $this->language->get('entry_order_refused');
        $this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_update_status_alert'] = $this->language->get('entry_update_status_alert');
        $this->data['entry_total'] = $this->language->get('entry_total');
        $this->data['entry_boleto_discount_percentage'] = $this->language->get('entry_boleto_discount_percentage');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['criptografia'])) {
            $this->data['error_criptografia'] = $this->error['criptografia'];
        } else {
            $this->data['error_criptografia'] = '';
        }

        if (isset($this->error['texto_botao'])) {
            $this->data['error_texto_botao'] = $this->error['texto_botao'];
        } else {
            $this->data['error_texto_botao'] = '';
        }

        if (isset($this->error['payment_methods'])) {
            $this->data['error_payment_methods'] = $this->error['payment_methods'];
        } else {
            $this->data['error_payment_methods'] = '';
        }

        if (isset($this->error['card_brands'])) {
            $this->data['error_card_brands'] = $this->error['card_brands'];
        } else {
            $this->data['error_card_brands'] = '';
        }

        if (isset($this->error['max_installments'])) {
            $this->data['error_max_installments'] = $this->error['max_installments'];
        } else {
            $this->data['error_max_installments'] = '';
        }

        if (isset($this->error['api'])) {
            $this->data['error_api'] = $this->error['api'];
        } else {
            $this->data['error_api'] = '';
        }

        if (isset($this->error['nome'])) {
            $this->data['error_nome'] = $this->error['nome'];
        } else {
            $this->data['error_nome'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('payment/pagar_me_checkout', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/pagar_me_checkout', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_checkout_criptografia'])) {
            $this->data['pagar_me_checkout_criptografia'] = $this->request->post['pagar_me_checkout_criptografia'];
        } else {
            $this->data['pagar_me_checkout_criptografia'] = $this->config->get('pagar_me_checkout_criptografia');
        }

        if (isset($this->request->post['pagar_me_checkout_texto_botao'])) {
            $this->data['pagar_me_checkout_texto_botao'] = $this->request->post['pagar_me_checkout_texto_botao'];
        } else {
            $this->data['pagar_me_checkout_texto_botao'] = $this->config->get('pagar_me_checkout_texto_botao');
        }

        if (isset($this->request->post['pagar_me_checkout_payment_methods'])) {
            $this->data['pagar_me_checkout_payment_methods'] = $this->request->post['pagar_me_checkout_payment_methods'];
        } elseif ($this->config->get('pagar_me_checkout_payment_methods')) {
            $this->data['pagar_me_checkout_payment_methods'] = $this->config->get('pagar_me_checkout_payment_methods');
        } else {
            $this->data['pagar_me_checkout_payment_methods'] = array();
        }

        if (isset($this->request->post['pagar_me_checkout_card_brands'])) {
            $this->data['pagar_me_checkout_card_brands'] = $this->request->post['pagar_me_checkout_card_brands'];
        } elseif ($this->config->get('pagar_me_checkout_card_brands')) {
            $this->data['pagar_me_checkout_card_brands'] = $this->config->get('pagar_me_checkout_card_brands');
        } else {
            $this->data['pagar_me_checkout_card_brands'] = array();
        }

        if (isset($this->request->post['pagar_me_checkout_max_installments'])) {
            $this->data['pagar_me_checkout_max_installments'] = $this->request->post['pagar_me_checkout_max_installments'];
        } else {
            $this->data['pagar_me_checkout_max_installments'] = $this->config->get('pagar_me_checkout_max_installments');
        }

        if (isset($this->request->post['pagar_me_checkout_free_installments'])) {
            $this->data['pagar_me_checkout_free_installments'] = $this->request->post['pagar_me_checkout_free_installments'];
        } else {
            $this->data['pagar_me_checkout_free_installments'] = $this->config->get('pagar_me_checkout_free_installments');
        }

        if (isset($this->request->post['pagar_me_checkout_max_installment_value'])) {
            $this->data['pagar_me_checkout_max_installment_value'] = $this->request->post['pagar_me_checkout_max_installment_value'];
        } else {
            $this->data['pagar_me_checkout_max_installment_value'] = $this->config->get('pagar_me_checkout_max_installment_value');
        }

        if (isset($this->request->post['pagar_me_checkout_interest_rate'])) {
            $this->data['pagar_me_checkout_interest_rate'] = $this->request->post['pagar_me_checkout_interest_rate'];
        } else {
            $this->data['pagar_me_checkout_interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');
        }

        if (isset($this->request->post['pagar_me_checkout_ui_color'])) {
            $this->data['pagar_me_checkout_ui_color'] = $this->request->post['pagar_me_checkout_ui_color'];
        } else {
            $this->data['pagar_me_checkout_ui_color'] = $this->config->get('pagar_me_checkout_ui_color');
        }

        if (isset($this->request->post['pagar_me_checkout_button_css_class'])) {
            $this->data['pagar_me_checkout_button_css_class'] = $this->request->post['pagar_me_checkout_button_css_class'];
        } else {
            $this->data['pagar_me_checkout_button_css_class'] = $this->config->get('pagar_me_checkout_button_css_class');
        }

        if (isset($this->request->post['pagar_me_checkout_api'])) {
            $this->data['pagar_me_checkout_api'] = $this->request->post['pagar_me_checkout_api'];
        } else {
            $this->data['pagar_me_checkout_api'] = $this->config->get('pagar_me_checkout_api');
        }

        if (isset($this->request->post['pagar_me_checkout_nome'])) {
            $this->data['pagar_me_checkout_nome'] = $this->request->post['pagar_me_checkout_nome'];
        } else {
            $this->data['pagar_me_checkout_nome'] = $this->config->get('pagar_me_checkout_nome');
        }

        if (isset($this->request->post['pagar_me_checkout_customer_data'])) {
            $this->data['pagar_me_checkout_customer_data'] = $this->request->post['pagar_me_checkout_customer_data'];
        } else {
            $this->data['pagar_me_checkout_customer_data'] = $this->config->get('pagar_me_checkout_customer_data');
        }

        if (isset($this->request->post['pagar_me_checkout_text_information'])) {
            $this->data['pagar_me_checkout_text_information'] = $this->request->post['pagar_me_checkout_text_information'];
        } else {
            $this->data['pagar_me_checkout_text_information'] = $this->config->get('pagar_me_checkout_text_information');
        }

        if (isset($this->request->post['pagar_me_checkout_order_waiting_payment'])) {
            $this->data['pagar_me_checkout_order_waiting_payment'] = $this->request->post['pagar_me_checkout_order_waiting_payment'];
        } else {
            $this->data['pagar_me_checkout_order_waiting_payment'] = $this->config->get('pagar_me_checkout_order_waiting_payment');
        }

        if (isset($this->request->post['pagar_me_checkout_order_paid'])) {
            $this->data['pagar_me_checkout_order_paid'] = $this->request->post['pagar_me_checkout_order_paid'];
        } else {
            $this->data['pagar_me_checkout_order_paid'] = $this->config->get('pagar_me_checkout_order_paid');
        }

        if (isset($this->request->post['pagar_me_checkout_order_authorized'])) {
            $this->data['pagar_me_checkout_order_authorized'] = $this->request->post['pagar_me_checkout_order_authorized'];
        } else {
            $this->data['pagar_me_checkout_order_authorized'] = $this->config->get('pagar_me_checkout_order_authorized');
        }

        if (isset($this->request->post['pagar_me_checkout_order_pending_refund'])) {
            $this->data['pagar_me_checkout_order_pending_refund'] = $this->request->post['pagar_me_checkout_order_pending_refund'];
        } else {
            $this->data['pagar_me_checkout_order_pending_refund'] = $this->config->get('pagar_me_checkout_order_pending_refund');
        }

        if (isset($this->request->post['pagar_me_checkout_order_refunded'])) {
            $this->data['pagar_me_checkout_order_refunded'] = $this->request->post['pagar_me_checkout_order_refunded'];
        } else {
            $this->data['pagar_me_checkout_order_refunded'] = $this->config->get('pagar_me_checkout_order_refunded');
        }

        if (isset($this->request->post['pagar_me_checkout_order_processing'])) {
            $this->data['pagar_me_checkout_order_processing'] = $this->request->post['pagar_me_checkout_order_processing'];
        } else {
            $this->data['pagar_me_checkout_order_processing'] = $this->config->get('pagar_me_checkout_order_processing');
        }

        if (isset($this->request->post['pagar_me_checkout_order_refused'])) {
            $this->data['pagar_me_checkout_order_refused'] = $this->request->post['pagar_me_checkout_order_refused'];
        } else {
            $this->data['pagar_me_checkout_order_refused'] = $this->config->get('pagar_me_checkout_order_refused');
        }

        if (isset($this->request->post['pagar_me_checkout_boleto_discount_percentage'])) {
            $this->data['pagar_me_checkout_boleto_discount_percentage'] = $this->request->post['pagar_me_checkout_boleto_discount_percentage'];
        } else {
            $this->data['pagar_me_checkout_boleto_discount_percentage'] = $this->config->get('pagar_me_checkout_boleto_discount_percentage');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_checkout_geo_zone_id'])) {
            $this->data['pagar_me_checkout_geo_zone_id'] = $this->request->post['pagar_me_checkout_geo_zone_id'];
        } else {
            $this->data['pagar_me_checkout_geo_zone_id'] = $this->config->get('pagar_me_checkout_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_checkout_status'])) {
            $this->data['pagar_me_checkout_status'] = $this->request->post['pagar_me_checkout_status'];
        } else {
            $this->data['pagar_me_checkout_status'] = $this->config->get('pagar_me_checkout_status');
        }

        if (isset($this->request->post['pagar_me_checkout_sort_order'])) {
            $this->data['pagar_me_checkout_sort_order'] = $this->request->post['pagar_me_checkout_sort_order'];
        } else {
            $this->data['pagar_me_checkout_sort_order'] = $this->config->get('pagar_me_checkout_sort_order');
        }

        $this->template = 'payment/pagar_me_checkout.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate()
    {

        if (!$this->user->hasPermission('modify', 'payment/pagar_me_checkout')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['pagar_me_checkout_criptografia']) {
            $this->error['criptografia'] = $this->language->get('error_criptografia');
        }

        if (!$this->request->post['pagar_me_checkout_texto_botao']) {
            $this->error['texto_botao'] = $this->language->get('error_texto_botao');
        }

        if (!$this->request->post['pagar_me_checkout_payment_methods']) {
            $this->error['payment_methods'] = $this->language->get('error_payment_methods');
        }

        if (!$this->request->post['pagar_me_checkout_card_brands']) {
            $this->error['card_brands'] = $this->language->get('error_card_brands');
        }

        if (!$this->request->post['pagar_me_checkout_max_installments']) {
            $this->error['max_installments'] = $this->language->get('error_max_installments');
        }

        if (!$this->request->post['pagar_me_checkout_api']) {
            $this->error['api'] = $this->language->get('error_api');
        }

        if (!$this->request->post['pagar_me_checkout_nome']) {
            $this->error['nome'] = $this->language->get('error_nome');
        }

//        var_dump($this->error); exit;

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function install()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pagar_me_checkout_transaction` (
  `pagar_me_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(512) DEFAULT NULL,  
  `n_parcela` int(11) DEFAULT '0',
  `bandeira` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pagar_me_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` ADD `pagar_me_checkout_url` VARCHAR( 512 ) NULL DEFAULT NULL AFTER  `order_id`");
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pagar_me_checkout_transaction`");

        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP COLUMN `pagar_me_checkout_url`");
    }

}

?>