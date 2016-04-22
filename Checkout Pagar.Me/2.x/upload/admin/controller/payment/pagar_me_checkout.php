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

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_criptografia'] = $this->language->get('entry_criptografia');
        $data['help_criptografia'] = $this->language->get('help_criptografia');
        $data['entry_api'] = $this->language->get('entry_api');
        $data['help_api'] = $this->language->get('help_api');
        $data['entry_nome'] = $this->language->get('entry_nome');
        $data['help_nome'] = $this->language->get('help_nome');
        $data['entry_texto_botao'] = $this->language->get('entry_texto_botao');
        $data['help_texto_botao'] = $this->language->get('help_texto_botao');
        $data['entry_payment_methods'] = $this->language->get('entry_payment_methods');
        $data['entry_card_brands'] = $this->language->get('entry_card_brands');
        $data['entry_max_installments'] = $this->language->get('entry_max_installments');
        $data['entry_free_installments'] = $this->language->get('entry_free_installments');
        $data['entry_max_installment_value'] = $this->language->get('entry_max_installment_value');
        $data['entry_interest_rate'] = $this->language->get('entry_insterest_rate');
        $data['help_interest_rate'] = $this->language->get('help_insterest_rate');
        $data['entry_ui_color'] = $this->language->get('entry_ui_color');
        $data['entry_button_css_class'] = $this->language->get('entry_button_css_class');
        $data['entry_text_information'] = $this->language->get('entry_text_information');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_waiting_payment'] = $this->language->get('entry_order_waiting_payment');
        $data['entry_order_paid'] = $this->language->get('entry_order_paid');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_update_status_alert'] = $this->language->get('entry_update_status_alert');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_boleto_discount_percentage'] = $this->language->get('entry_boleto_discount_percentage');
        $data['help_boleto_discount_percentage'] = $this->language->get('help_boleto_discount_percentage');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['criptografia'])) {
            $data['error_criptografia'] = $this->error['criptografia'];
        } else {
            $data['error_criptografia'] = '';
        }

        if (isset($this->error['texto_botao'])) {
            $data['error_texto_botao'] = $this->error['texto_botao'];
        } else {
            $data['error_texto_botao'] = '';
        }

        if (isset($this->error['payment_methods'])) {
            $data['error_payment_methods'] = $this->error['payment_methods'];
        } else {
            $data['error_payment_methods'] = '';
        }

        if (isset($this->error['card_brands'])) {
            $data['error_card_brands'] = $this->error['card_brands'];
        } else {
            $data['error_card_brands'] = '';
        }

        if (isset($this->error['max_installments'])) {
            $data['error_max_installments'] = $this->error['max_installments'];
        } else {
            $data['error_max_installments'] = '';
        }

        if (isset($this->error['api'])) {
            $data['error_api'] = $this->error['api'];
        } else {
            $data['error_api'] = '';
        }

        if (isset($this->error['nome'])) {
            $data['error_nome'] = $this->error['nome'];
        } else {
            $data['error_nome'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('payment/pagar_me_checkout', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/pagar_me_checkout', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_checkout_criptografia'])) {
            $data['pagar_me_checkout_criptografia'] = $this->request->post['pagar_me_checkout_criptografia'];
        } else {
            $data['pagar_me_checkout_criptografia'] = $this->config->get('pagar_me_checkout_criptografia');
        }

        if (isset($this->request->post['pagar_me_checkout_texto_botao'])) {
            $data['pagar_me_checkout_texto_botao'] = $this->request->post['pagar_me_checkout_texto_botao'];
        } else {
            $data['pagar_me_checkout_texto_botao'] = $this->config->get('pagar_me_checkout_texto_botao');
        }

        if (isset($this->request->post['pagar_me_checkout_payment_methods'])) {
            $data['pagar_me_checkout_payment_methods'] = $this->request->post['pagar_me_checkout_payment_methods'];
        } elseif($this->config->get('pagar_me_checkout_payment_methods')) {
            $data['pagar_me_checkout_payment_methods'] = $this->config->get('pagar_me_checkout_payment_methods');
        }else{
            $data['pagar_me_checkout_payment_methods'] = array();
        }

        if (isset($this->request->post['pagar_me_checkout_card_brands'])) {
            $data['pagar_me_checkout_card_brands'] = $this->request->post['pagar_me_checkout_card_brands'];
        } elseif($this->config->get('pagar_me_checkout_card_brands')) {
            $data['pagar_me_checkout_card_brands'] = $this->config->get('pagar_me_checkout_card_brands');
        }else{
            $data['pagar_me_checkout_card_brands'] = array();
        }

        if (isset($this->request->post['pagar_me_checkout_max_installment_value'])) {
            $data['pagar_me_checkout_max_installment_value'] = $this->request->post['pagar_me_checkout_max_installment_value'];
        } else {
            $data['pagar_me_checkout_max_installment_value'] = $this->config->get('pagar_me_checkout_max_installment_value');
        }

        if (isset($this->request->post['pagar_me_checkout_max_installments'])) {
            $data['pagar_me_checkout_max_installments'] = $this->request->post['pagar_me_checkout_max_installments'];
        } else {
            $data['pagar_me_checkout_max_installments'] = $this->config->get('pagar_me_checkout_max_installments');
        }
        
        if (isset($this->request->post['pagar_me_checkout_free_installments'])) {
            $data['pagar_me_checkout_free_installments'] = $this->request->post['pagar_me_checkout_free_installments'];
        } else {
            $data['pagar_me_checkout_free_installments'] = $this->config->get('pagar_me_checkout_free_installments');
        }

        if (isset($this->request->post['pagar_me_checkout_interest_rate'])) {
            $data['pagar_me_checkout_interest_rate'] = $this->request->post['pagar_me_checkout_interest_rate'];
        } else {
            $data['pagar_me_checkout_interest_rate'] = $this->config->get('pagar_me_checkout_interest_rate');
        }

        if (isset($this->request->post['pagar_me_checkout_ui_color'])) {
            $data['pagar_me_checkout_ui_color'] = $this->request->post['pagar_me_checkout_ui_color'];
        } else {
            $data['pagar_me_checkout_ui_color'] = $this->config->get('pagar_me_checkout_ui_color');
        }

        if (isset($this->request->post['pagar_me_checkout_button_css_class'])) {
            $data['pagar_me_checkout_button_css_class'] = $this->request->post['pagar_me_checkout_button_css_class'];
        } else {
            $data['pagar_me_checkout_button_css_class'] = $this->config->get('pagar_me_checkout_button_css_class');
        }

        if (isset($this->request->post['pagar_me_checkout_api'])) {
            $data['pagar_me_checkout_api'] = $this->request->post['pagar_me_checkout_api'];
        } else {
            $data['pagar_me_checkout_api'] = $this->config->get('pagar_me_checkout_api');
        }

        if (isset($this->request->post['pagar_me_checkout_nome'])) {
            $data['pagar_me_checkout_nome'] = $this->request->post['pagar_me_checkout_nome'];
        } else {
            $data['pagar_me_checkout_nome'] = $this->config->get('pagar_me_checkout_nome');
        }

        if (isset($this->request->post['pagar_me_checkout_text_information'])) {
            $data['pagar_me_checkout_text_information'] = $this->request->post['pagar_me_checkout_text_information'];
        } else {
            $data['pagar_me_checkout_text_information'] = $this->config->get('pagar_me_checkout_text_information');
        }

        if (isset($this->request->post['pagar_me_checkout_order_waiting_payment'])) {
            $data['pagar_me_checkout_order_waiting_payment'] = $this->request->post['pagar_me_checkout_order_waiting_payment'];
        } else {
            $data['pagar_me_checkout_order_waiting_payment'] = $this->config->get('pagar_me_checkout_order_waiting_payment');
        }

        if (isset($this->request->post['pagar_me_checkout_order_paid'])) {
            $data['pagar_me_checkout_order_paid'] = $this->request->post['pagar_me_checkout_order_paid'];
        } else {
            $data['pagar_me_checkout_order_paid'] = $this->config->get('pagar_me_checkout_order_paid');
        }

        if (isset($this->request->post['pagar_me_checkout_boleto_discount_percentage'])) {
            $data['pagar_me_checkout_boleto_discount_percentage'] = $this->request->post['pagar_me_checkout_boleto_discount_percentage'];
        } else {
            $data['pagar_me_checkout_boleto_discount_percentage'] = $this->config->get('pagar_me_checkout_boleto_discount_percentage');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_checkout_geo_zone_id'])) {
            $data['pagar_me_checkout_geo_zone_id'] = $this->request->post['pagar_me_checkout_geo_zone_id'];
        } else {
            $data['pagar_me_checkout_geo_zone_id'] = $this->config->get('pagar_me_checkout_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_checkout_status'])) {
            $data['pagar_me_checkout_status'] = $this->request->post['pagar_me_checkout_status'];
        } else {
            $data['pagar_me_checkout_status'] = $this->config->get('pagar_me_checkout_status');
        }

        if (isset($this->request->post['pagar_me_checkout_sort_order'])) {
            $data['pagar_me_checkout_sort_order'] = $this->request->post['pagar_me_checkout_sort_order'];
        } else {
            $data['pagar_me_checkout_sort_order'] = $this->config->get('pagar_me_checkout_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/pagar_me_checkout.tpl', $data));
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
        $this->load->model('payment/pagar_me_checkout');
        $this->model_payment_pagar_me_checkout->install();
    }

    public function uninstall()
    {
        $this->load->model('payment/pagar_me_checkout');
        $this->model_payment_pagar_me_checkout->uninstall();
    }
}

?>