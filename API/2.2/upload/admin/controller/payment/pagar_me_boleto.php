<?php

class ControllerPaymentPagarMeBoleto extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/pagar_me_boleto');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('pagar_me_boleto', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['entry_criptografia'] = $this->language->get('entry_criptografia');
        $data['entry_api'] = $this->language->get('entry_api');
        $data['entry_nome'] = $this->language->get('entry_nome');
        $data['entry_dias_vencimento'] = $this->language->get('entry_dias_vencimento');
        $data['help_criptografia'] = $this->language->get('help_criptografia');
        $data['help_api'] = $this->language->get('help_api');
        $data['help_nome'] = $this->language->get('help_nome');
        $data['help_dias_vencimento'] = $this->language->get('help_dias_vencimento');
        $data['entry_text_information'] = $this->language->get('entry_text_information');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_waiting_payment'] = $this->language->get('entry_order_waiting_payment');
        $data['entry_order_paid'] = $this->language->get('entry_order_paid');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_update_status_alert'] = $this->language->get('entry_update_status_alert');
        $data['entry_total'] = $this->language->get('entry_total');

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

        if (isset($this->error['dias_vencimento'])) {
            $data['error_dias_vencimento'] = $this->error['dias_vencimento'];
        } else {
            $data['error_dias_vencimento'] = '';
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
            'href' => $this->url->link('payment/pagar_me_boleto', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/pagar_me_boleto', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_boleto_criptografia'])) {
            $data['pagar_me_boleto_criptografia'] = $this->request->post['pagar_me_boleto_criptografia'];
        } else {
            $data['pagar_me_boleto_criptografia'] = $this->config->get('pagar_me_boleto_criptografia');
        }

        if (isset($this->request->post['pagar_me_boleto_dias_vencimento'])) {
            $data['pagar_me_boleto_dias_vencimento'] = $this->request->post['pagar_me_boleto_dias_vencimento'];
        } else {
            $data['pagar_me_boleto_dias_vencimento'] = $this->config->get('pagar_me_boleto_dias_vencimento');
        }

        if (isset($this->request->post['pagar_me_boleto_api'])) {
            $data['pagar_me_boleto_api'] = $this->request->post['pagar_me_boleto_api'];
        } else {
            $data['pagar_me_boleto_api'] = $this->config->get('pagar_me_boleto_api');
        }

        if (isset($this->request->post['pagar_me_boleto_nome'])) {
            $data['pagar_me_boleto_nome'] = $this->request->post['pagar_me_boleto_nome'];
        } else {
            $data['pagar_me_boleto_nome'] = $this->config->get('pagar_me_boleto_nome');
        }

        if (isset($this->request->post['pagar_me_boleto_text_information'])) {
            $data['pagar_me_boleto_text_information'] = $this->request->post['pagar_me_boleto_text_information'];
        } else {
            $data['pagar_me_boleto_text_information'] = $this->config->get('pagar_me_boleto_text_information');
        }

        if (isset($this->request->post['pagar_me_boleto_order_waiting_payment'])) {
            $data['pagar_me_boleto_order_waiting_payment'] = $this->request->post['pagar_me_boleto_order_waiting_payment'];
        } else {
            $data['pagar_me_boleto_order_waiting_payment'] = $this->config->get('pagar_me_boleto_order_waiting_payment');
        }

        if (isset($this->request->post['pagar_me_boleto_order_paid'])) {
            $data['pagar_me_boleto_order_paid'] = $this->request->post['pagar_me_boleto_order_paid'];
        } else {
            $data['pagar_me_boleto_order_paid'] = $this->config->get('pagar_me_boleto_order_paid');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_boleto_geo_zone_id'])) {
            $data['pagar_me_boleto_geo_zone_id'] = $this->request->post['pagar_me_boleto_geo_zone_id'];
        } else {
            $data['pagar_me_boleto_geo_zone_id'] = $this->config->get('pagar_me_boleto_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_boleto_status'])) {
            $data['pagar_me_boleto_status'] = $this->request->post['pagar_me_boleto_status'];
        } else {
            $data['pagar_me_boleto_status'] = $this->config->get('pagar_me_boleto_status');
        }

        if (isset($this->request->post['pagar_me_boleto_sort_order'])) {
            $data['pagar_me_boleto_sort_order'] = $this->request->post['pagar_me_boleto_sort_order'];
        } else {
            $data['pagar_me_boleto_sort_order'] = $this->config->get('pagar_me_boleto_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/pagar_me_boleto.tpl', $data));
    }

    private function validate() {

        if (!$this->user->hasPermission('modify', 'payment/pagar_me_boleto')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['pagar_me_boleto_criptografia']) {
            $this->error['criptografia'] = $this->language->get('error_criptografia');
        }

        if (!$this->request->post['pagar_me_boleto_dias_vencimento']) {
            $this->error['dias_vencimento'] = $this->language->get('error_dias_vencimento');
        }

        if (!$this->request->post['pagar_me_boleto_api']) {
            $this->error['api'] = $this->language->get('error_api');
        }

        if (!$this->request->post['pagar_me_boleto_nome']) {
            $this->error['nome'] = $this->language->get('error_nome');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function install() {
        $this->load->model('payment/pagar_me_boleto');
        $this->model_payment_pagar_me_boleto->install();
    }

    public function uninstall() {
        $this->load->model('payment/pagar_me_boleto');
        $this->model_payment_pagar_me_boleto->uninstall();
    }

}

?>