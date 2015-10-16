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
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_criptografia'] = $this->language->get('entry_criptografia');
        $data['entry_api'] = $this->language->get('entry_api');
        $data['entry_nome'] = $this->language->get('entry_nome');
        $data['entry_max_parcelas'] = $this->language->get('entry_max_parcelas');
        $data['entry_parcelas_sem_juros'] = $this->language->get('entry_parcelas_sem_juros');
        $data['help_criptografia'] = $this->language->get('help_criptografia');
        $data['help_api'] = $this->language->get('help_api');
        $data['help_nome'] = $this->language->get('help_nome');
        $data['help_max_parcelas'] = $this->language->get('help_max_parcelas');
        $data['help_parcelas_sem_juros'] = $this->language->get('help_parcelas_sem_juros');
        $data['entry_valor_parcela'] = $this->language->get('entry_valor_parcela');
        $data['entry_taxa_juros'] = $this->language->get('entry_taxa_juros');
        $data['help_taxa_juros'] = $this->language->get('help_taxa_juros');
        $data['entry_text_information'] = $this->language->get('entry_text_information');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_processing'] = $this->language->get('entry_order_processing');
        $data['entry_order_paid'] = $this->language->get('entry_order_paid');
        $data['entry_order_refused'] = $this->language->get('entry_order_refused');
        $data['entry_order_refunded'] = $this->language->get('entry_order_refunded');
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

        if (isset($this->error['max_parcelas'])) {
            $data['error_max_parcelas'] = $this->error['max_parcelas'];
        } else {
            $data['error_max_parcelas'] = '';
        }

        if (isset($this->error['parcelas_sem_juros'])) {
            $data['error_parcelas_sem_juros'] = $this->error['parcelas_sem_juros'];
        } else {
            $data['error_parcelas_sem_juros'] = '';
        }

        if (isset($this->error['valor_parcelas'])) {
            $data['error_valor_parcelas'] = $this->error['valor_parcelas'];
        } else {
            $data['error_valor_parcelas'] = '';
        }

        if (isset($this->error['taxa_juros'])) {
            $data['error_taxa_juros'] = $this->error['taxa_juros'];
        } else {
            $data['error_taxa_juros'] = '';
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
            'href' => $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/pagar_me_cartao', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_cartao_criptografia'])) {
            $data['pagar_me_cartao_criptografia'] = $this->request->post['pagar_me_cartao_criptografia'];
        } else {
            $data['pagar_me_cartao_criptografia'] = $this->config->get('pagar_me_cartao_criptografia');
        }

        if (isset($this->request->post['pagar_me_cartao_taxa_juros'])) {
            $data['pagar_me_cartao_taxa_juros'] = $this->request->post['pagar_me_cartao_taxa_juros'];
        } else {
            $data['pagar_me_cartao_taxa_juros'] = $this->config->get('pagar_me_cartao_taxa_juros');
        }

        if (isset($this->request->post['pagar_me_cartao_max_parcelas'])) {
            $data['pagar_me_cartao_max_parcelas'] = $this->request->post['pagar_me_cartao_max_parcelas'];
        } else {
            $data['pagar_me_cartao_max_parcelas'] = $this->config->get('pagar_me_cartao_max_parcelas');
        }

        if (isset($this->request->post['pagar_me_cartao_parcelas_sem_juros'])) {
            $data['pagar_me_cartao_parcelas_sem_juros'] = $this->request->post['pagar_me_cartao_parcelas_sem_juros'];
        } else {
            $data['pagar_me_cartao_parcelas_sem_juros'] = $this->config->get('pagar_me_cartao_parcelas_sem_juros');
        }

        if (isset($this->request->post['pagar_me_cartao_valor_parcela'])) {
            $data['pagar_me_cartao_valor_parcela'] = $this->request->post['pagar_me_cartao_valor_parcela'];
        } else {
            $data['pagar_me_cartao_valor_parcela'] = $this->config->get('pagar_me_cartao_valor_parcela');
        }

        if (isset($this->request->post['pagar_me_cartao_api'])) {
            $data['pagar_me_cartao_api'] = $this->request->post['pagar_me_cartao_api'];
        } else {
            $data['pagar_me_cartao_api'] = $this->config->get('pagar_me_cartao_api');
        }

        if (isset($this->request->post['pagar_me_cartao_nome'])) {
            $data['pagar_me_cartao_nome'] = $this->request->post['pagar_me_cartao_nome'];
        } else {
            $data['pagar_me_cartao_nome'] = $this->config->get('pagar_me_cartao_nome');
        }

        if (isset($this->request->post['pagar_me_cartao_text_information'])) {
            $data['pagar_me_cartao_text_information'] = $this->request->post['pagar_me_cartao_text_information'];
        } else {
            $data['pagar_me_cartao_text_information'] = $this->config->get('pagar_me_cartao_text_information');
        }

        if (isset($this->request->post['pagar_me_cartao_teste'])) {
            $data['pagar_me_cartao_teste'] = $this->request->post['pagar_me_cartao_teste'];
        } elseif ($this->config->get('pagar_me_cartao_teste')) {
            $data['pagar_me_cartao_teste'] = $this->config->get('pagar_me_cartao_teste');
        } else {
            $data['pagar_me_cartao_teste'] = 0;
        }

        if (isset($this->request->post['pagar_me_cartao_posfixo'])) {
            $data['pagar_me_cartao_posfixo'] = $this->request->post['pagar_me_cartao_posfixo'];
        } else {
            $data['pagar_me_cartao_posfixo'] = $this->config->get('pagar_me_cartao_posfixo');
        }

        if (isset($this->request->post['pagar_me_cartao_total'])) {
            $data['pagar_me_cartao_total'] = $this->request->post['pagar_me_cartao_total'];
        } else {
            $data['pagar_me_cartao_total'] = $this->config->get('pagar_me_cartao_total');
        }

        if (isset($this->request->post['pagar_me_cartao_tipo_frete'])) {
            $data['pagar_me_cartao_tipo_frete'] = $this->request->post['pagar_me_cartao_tipo_frete'];
        } else {
            $data['pagar_me_cartao_tipo_frete'] = $this->config->get('pagar_me_cartao_tipo_frete');
        }

        if (isset($this->request->post['pagar_me_cartao_update_status_alert'])) {
            $data['pagar_me_cartao_update_status_alert'] = $this->request->post['pagar_me_cartao_update_status_alert'];
        } else {
            $data['pagar_me_cartao_update_status_alert'] = $this->config->get('pagar_me_cartao_update_status_alert');
        }

        if (isset($this->request->post['pagar_me_cartao_order_processing'])) {
            $data['pagar_me_cartao_order_processing'] = $this->request->post['pagar_me_cartao_order_processing'];
        } else {
            $data['pagar_me_cartao_order_processing'] = $this->config->get('pagar_me_cartao_order_processing');
        }

        if (isset($this->request->post['pagar_me_cartao_order_paid'])) {
            $data['pagar_me_cartao_order_paid'] = $this->request->post['pagar_me_cartao_order_paid'];
        } else {
            $data['pagar_me_cartao_order_paid'] = $this->config->get('pagar_me_cartao_order_paid');
        }

        if (isset($this->request->post['pagar_me_cartao_order_refused'])) {
            $data['pagar_me_cartao_order_refused'] = $this->request->post['pagar_me_cartao_order_refused'];
        } else {
            $data['pagar_me_cartao_order_refused'] = $this->config->get('pagar_me_cartao_order_refused');
        }

        if (isset($this->request->post['pagar_me_cartao_order_refunded'])) {
            $data['pagar_me_cartao_order_refunded'] = $this->request->post['pagar_me_cartao_order_refunded'];
        } else {
            $data['pagar_me_cartao_order_refunded'] = $this->config->get('pagar_me_cartao_order_refunded');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_cartao_geo_zone_id'])) {
            $data['pagar_me_cartao_geo_zone_id'] = $this->request->post['pagar_me_cartao_geo_zone_id'];
        } else {
            $data['pagar_me_cartao_geo_zone_id'] = $this->config->get('pagar_me_cartao_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_cartao_status'])) {
            $data['pagar_me_cartao_status'] = $this->request->post['pagar_me_cartao_status'];
        } else {
            $data['pagar_me_cartao_status'] = $this->config->get('pagar_me_cartao_status');
        }

        if (isset($this->request->post['pagar_me_cartao_sort_order'])) {
            $data['pagar_me_cartao_sort_order'] = $this->request->post['pagar_me_cartao_sort_order'];
        } else {
            $data['pagar_me_cartao_sort_order'] = $this->config->get('pagar_me_cartao_sort_order');
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
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pagar_me_transaction` (
  `pagar_me_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(512) DEFAULT NULL,
  `n_parcela` int(11) DEFAULT '0',
  `bandeira` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pagar_me_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
    }

    public function uninstall() {
    }

}

?>