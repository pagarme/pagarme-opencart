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
        $this->data['entry_max_parcelas'] = $this->language->get('entry_max_parcelas');
        $this->data['entry_parcelas_sem_juros'] = $this->language->get('entry_parcelas_sem_juros');
        $this->data['entry_valor_parcela'] = $this->language->get('entry_valor_parcela');
        $this->data['entry_taxa_juros'] = $this->language->get('entry_taxa_juros');
        $this->data['entry_text_information'] = $this->language->get('entry_text_information');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_order_processing'] = $this->language->get('entry_order_processing');
        $this->data['entry_order_paid'] = $this->language->get('entry_order_paid');
        $this->data['entry_order_refused'] = $this->language->get('entry_order_refused');
        $this->data['entry_order_refunded'] = $this->language->get('entry_order_refunded');
        $this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_update_status_alert'] = $this->language->get('entry_update_status_alert');
        $this->data['entry_total'] = $this->language->get('entry_total');

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

        if (isset($this->error['max_parcelas'])) {
            $this->data['error_max_parcelas'] = $this->error['max_parcelas'];
        } else {
            $this->data['error_max_parcelas'] = '';
        }

        if (isset($this->error['parcelas_sem_juros'])) {
            $this->data['error_parcelas_sem_juros'] = $this->error['parcelas_sem_juros'];
        } else {
            $this->data['error_parcelas_sem_juros'] = '';
        }

        if (isset($this->error['valor_parcelas'])) {
            $this->data['error_valor_parcelas'] = $this->error['valor_parcelas'];
        } else {
            $this->data['error_valor_parcelas'] = '';
        }

        if (isset($this->error['taxa_juros'])) {
            $this->data['error_taxa_juros'] = $this->error['taxa_juros'];
        } else {
            $this->data['error_taxa_juros'] = '';
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
            'href' => $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_cartao_criptografia'])) {
            $this->data['pagar_me_cartao_criptografia'] = $this->request->post['pagar_me_cartao_criptografia'];
        } else {
            $this->data['pagar_me_cartao_criptografia'] = $this->config->get('pagar_me_cartao_criptografia');
        }

        if (isset($this->request->post['pagar_me_cartao_taxa_juros'])) {
            $this->data['pagar_me_cartao_taxa_juros'] = $this->request->post['pagar_me_cartao_taxa_juros'];
        } else {
            $this->data['pagar_me_cartao_taxa_juros'] = $this->config->get('pagar_me_cartao_taxa_juros');
        }

        if (isset($this->request->post['pagar_me_cartao_max_parcelas'])) {
            $this->data['pagar_me_cartao_max_parcelas'] = $this->request->post['pagar_me_cartao_max_parcelas'];
        } else {
            $this->data['pagar_me_cartao_max_parcelas'] = $this->config->get('pagar_me_cartao_max_parcelas');
        }

        if (isset($this->request->post['pagar_me_cartao_parcelas_sem_juros'])) {
            $this->data['pagar_me_cartao_parcelas_sem_juros'] = $this->request->post['pagar_me_cartao_parcelas_sem_juros'];
        } else {
            $this->data['pagar_me_cartao_parcelas_sem_juros'] = $this->config->get('pagar_me_cartao_parcelas_sem_juros');
        }

        if (isset($this->request->post['pagar_me_cartao_valor_parcela'])) {
            $this->data['pagar_me_cartao_valor_parcela'] = $this->request->post['pagar_me_cartao_valor_parcela'];
        } else {
            $this->data['pagar_me_cartao_valor_parcela'] = $this->config->get('pagar_me_cartao_valor_parcela');
        }

        if (isset($this->request->post['pagar_me_cartao_api'])) {
            $this->data['pagar_me_cartao_api'] = $this->request->post['pagar_me_cartao_api'];
        } else {
            $this->data['pagar_me_cartao_api'] = $this->config->get('pagar_me_cartao_api');
        }

        if (isset($this->request->post['pagar_me_cartao_nome'])) {
            $this->data['pagar_me_cartao_nome'] = $this->request->post['pagar_me_cartao_nome'];
        } else {
            $this->data['pagar_me_cartao_nome'] = $this->config->get('pagar_me_cartao_nome');
        }

        if (isset($this->request->post['pagar_me_cartao_text_information'])) {
            $this->data['pagar_me_cartao_text_information'] = $this->request->post['pagar_me_cartao_text_information'];
        } else {
            $this->data['pagar_me_cartao_text_information'] = $this->config->get('pagar_me_cartao_text_information');
        }

        if (isset($this->request->post['pagar_me_cartao_teste'])) {
            $this->data['pagar_me_cartao_teste'] = $this->request->post['pagar_me_cartao_teste'];
        } elseif ($this->config->get('pagar_me_cartao_teste')) {
            $this->data['pagar_me_cartao_teste'] = $this->config->get('pagar_me_cartao_teste');
        } else {
            $this->data['pagar_me_cartao_teste'] = 0;
        }

        if (isset($this->request->post['pagar_me_cartao_posfixo'])) {
            $this->data['pagar_me_cartao_posfixo'] = $this->request->post['pagar_me_cartao_posfixo'];
        } else {
            $this->data['pagar_me_cartao_posfixo'] = $this->config->get('pagar_me_cartao_posfixo');
        }

        if (isset($this->request->post['pagar_me_cartao_total'])) {
            $this->data['pagar_me_cartao_total'] = $this->request->post['pagar_me_cartao_total'];
        } else {
            $this->data['pagar_me_cartao_total'] = $this->config->get('pagar_me_cartao_total');
        }

        if (isset($this->request->post['pagar_me_cartao_tipo_frete'])) {
            $this->data['pagar_me_cartao_tipo_frete'] = $this->request->post['pagar_me_cartao_tipo_frete'];
        } else {
            $this->data['pagar_me_cartao_tipo_frete'] = $this->config->get('pagar_me_cartao_tipo_frete');
        }

        if (isset($this->request->post['pagar_me_cartao_update_status_alert'])) {
            $this->data['pagar_me_cartao_update_status_alert'] = $this->request->post['pagar_me_cartao_update_status_alert'];
        } else {
            $this->data['pagar_me_cartao_update_status_alert'] = $this->config->get('pagar_me_cartao_update_status_alert');
        }

        if (isset($this->request->post['pagar_me_cartao_order_processing'])) {
            $this->data['pagar_me_cartao_order_processing'] = $this->request->post['pagar_me_cartao_order_processing'];
        } else {
            $this->data['pagar_me_cartao_order_processing'] = $this->config->get('pagar_me_cartao_order_processing');
        }

        if (isset($this->request->post['pagar_me_cartao_order_paid'])) {
            $this->data['pagar_me_cartao_order_paid'] = $this->request->post['pagar_me_cartao_order_paid'];
        } else {
            $this->data['pagar_me_cartao_order_paid'] = $this->config->get('pagar_me_cartao_order_paid');
        }

        if (isset($this->request->post['pagar_me_cartao_order_refused'])) {
            $this->data['pagar_me_cartao_order_refused'] = $this->request->post['pagar_me_cartao_order_refused'];
        } else {
            $this->data['pagar_me_cartao_order_refused'] = $this->config->get('pagar_me_cartao_order_refused');
        }

        if (isset($this->request->post['pagar_me_cartao_order_refunded'])) {
            $this->data['pagar_me_cartao_order_refunded'] = $this->request->post['pagar_me_cartao_order_refunded'];
        } else {
            $this->data['pagar_me_cartao_order_refunded'] = $this->config->get('pagar_me_cartao_order_refunded');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_cartao_geo_zone_id'])) {
            $this->data['pagar_me_cartao_geo_zone_id'] = $this->request->post['pagar_me_cartao_geo_zone_id'];
        } else {
            $this->data['pagar_me_cartao_geo_zone_id'] = $this->config->get('pagar_me_cartao_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_cartao_status'])) {
            $this->data['pagar_me_cartao_status'] = $this->request->post['pagar_me_cartao_status'];
        } else {
            $this->data['pagar_me_cartao_status'] = $this->config->get('pagar_me_cartao_status');
        }

        if (isset($this->request->post['pagar_me_cartao_sort_order'])) {
            $this->data['pagar_me_cartao_sort_order'] = $this->request->post['pagar_me_cartao_sort_order'];
        } else {
            $this->data['pagar_me_cartao_sort_order'] = $this->config->get('pagar_me_cartao_sort_order');
        }

        $this->template = 'payment/pagar_me_cartao.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate() {

        if (!$this->user->hasPermission('modify', 'payment/pagar_me_cartao')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['pagar_me_cartao_criptografia']) {
            $this->error['criptografia'] = $this->language->get('error_criptografia');
        }

        if (!$this->request->post['pagar_me_cartao_taxa_juros']) {
            $this->error['taxa_juros'] = $this->language->get('error_taxa_juros');
        }

        if (!$this->request->post['pagar_me_cartao_max_parcelas']) {
            $this->error['max_parcelas'] = $this->language->get('error_max_parcelas');
        }

        if (!$this->request->post['pagar_me_cartao_parcelas_sem_juros']) {
            $this->error['parcelas_sem_juros'] = $this->language->get('error_parcelas_sem_juros');
        }

        if (!$this->request->post['pagar_me_cartao_valor_parcela']) {
            $this->error['valor_parcela'] = $this->language->get('error_valor_parcela');
        }

        if (!$this->request->post['pagar_me_cartao_api']) {
            $this->error['api'] = $this->language->get('error_api');
        }

        if (!$this->request->post['pagar_me_cartao_nome']) {
            $this->error['nome'] = $this->language->get('error_nome');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function install() {
        if ($this->config->get('pagar_me_boleto_status') === null) {
            $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` ADD `pagar_me_transaction` VARCHAR( 512 ) NULL DEFAULT NULL AFTER  `payment_code`");
        }
    }

    public function uninstall() {
        if ($this->config->get('pagar_me_boleto_status') === null) {
            $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` DROP `pagar_me_transaction`");
        }
    }

}

?>