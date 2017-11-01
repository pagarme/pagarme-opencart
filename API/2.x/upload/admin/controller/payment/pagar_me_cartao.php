<?php

class ControllerPaymentPagarMeCartao extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/pagar_me_cartao');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('pagar_me_cartao', $this->request->post);

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

        $admin_options_texts = array(
            'entry_criptografia',
            'entry_api',
            'entry_nome',
            'entry_max_parcelas',
            'entry_parcelas_sem_juros',
            'entry_valor_parcela',
            'entry_taxa_juros',
            'entry_text_information',
            'entry_order_status',
            'entry_order_processing',
            'entry_order_paid',
            'entry_order_refused',
            'entry_order_refunded',
            'entry_geo_zone',
            'entry_async',
            'entry_status',
            'entry_sort_order',
            'entry_total'
        );

        foreach($admin_options_texts as $text){
            $data = $this->setAdminConfigurationTexts($data, $text);
        }

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $admin_help_texts = array (
            'help_criptografia',
            'help_api',
            'help_nome',
            'help_max_parcelas',
            'help_parcelas_sem_juros',
            'help_taxa_juros',
            'help_async'
        );

        foreach($admin_help_texts as $help_text){
            $data = $this->setAdminConfigurationHelpTexts($data, $help_text);
        }

        $admin_error_fields = array(
            'warning',
            'criptografia',
            'max_parcelas',
            'parcelas_sem_juros',
            'valor_parcela',
            'taxa_juros',
            'api',
            'nome'
        );

        foreach($admin_error_fields as $error){
            $data = $this->setAdminConfigurationErrorMessages($data, $error);
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
            'href' => $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $admin_configuration_options = array(
            'pagar_me_cartao_nome',
            'pagar_me_cartao_criptografia',
            'pagar_me_cartao_api',
            'pagar_me_cartao_text_information',
            'pagar_me_cartao_max_parcelas',
            'pagar_me_cartao_taxa_juros',
            'pagar_me_cartao_parcelas_sem_juros',
            'pagar_me_cartao_valor_parcela',
            'pagar_me_cartao_order_processing',
            'pagar_me_cartao_order_paid',
            'pagar_me_cartao_order_refused',
            'pagar_me_cartao_order_refunded',
            'pagar_me_cartao_async',
            'pagar_me_cartao_geo_zone_id',
            'pagar_me_cartao_status',
            'pagar_me_cartao_sort_order'
        );

        foreach($admin_configuration_options as $option){
            $data = $this->setAdminConfigurations($data, $option);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/pagar_me_cartao.tpl', $data));
    }

    private function validate() {

        if (!$this->user->hasPermission('modify', 'payment/pagar_me_cartao')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $validateFields = array(
            'criptografia',
            'taxa_juros',
            'max_parcelas',
            'parcelas_sem_juros',
            'valor_parcela',
            'api',
            'nome'
        );

        foreach($validateFields as $field){
            if(!$this->request->post['pagar_me_cartao_'.$field]){
                $this->error[$field] = $this->language->get('error_'.$field);
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function install() {
        $this->load->model('payment/pagar_me_cartao');
        $this->model_payment_pagar_me_cartao->install();
    }

    public function uninstall() {
        $this->load->model('payment/pagar_me_cartao');
        $this->model_payment_pagar_me_cartao->uninstall();
    }

    private function setAdminConfigurationErrorMessages($data, $error) {
        $data['error_'.$error] = '';
        if(isset($this->error[$error])){
            $data['error_'.$error] = $this->error[$error];
        }
        return $data;
    }

    private function setAdminConfigurationHelpTexts($data, $help_text) {
        $data[$help_text] = $this->language->get($help_text);
        return $data;
    }

    private function setAdminConfigurationTexts($data, $text) {
        $data[$text] = $this->language->get($text);
        return $data;
    }

    private function setAdminConfigurations($data, $option) {
        $data[$option] = $this->config->get($option);
        if(isset($this->request->post[$option])){
            $data[$option] = $this->request->post[$option];
        }
        return $data;
    }
}

?>
