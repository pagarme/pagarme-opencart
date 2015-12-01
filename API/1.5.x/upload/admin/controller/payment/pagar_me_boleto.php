<?php

class ControllerPaymentPagarMeBoleto extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('payment/pagar_me_boleto');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('pagar_me_boleto', $this->request->post);

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
        $this->data['entry_dias_vencimento'] = $this->language->get('entry_dias_vencimento');
        $this->data['entry_text_information'] = $this->language->get('entry_text_information');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_order_waiting_payment'] = $this->language->get('entry_order_waiting_payment');
        $this->data['entry_order_paid'] = $this->language->get('entry_order_paid');
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

        if (isset($this->error['dias_vencimento'])) {
            $this->data['error_dias_vencimento'] = $this->error['dias_vencimento'];
        } else {
            $this->data['error_dias_vencimento'] = '';
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
            'href' => $this->url->link('payment/pagar_me_boleto', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/pagar_me_boleto', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['pagar_me_boleto_criptografia'])) {
            $this->data['pagar_me_boleto_criptografia'] = $this->request->post['pagar_me_boleto_criptografia'];
        } else {
            $this->data['pagar_me_boleto_criptografia'] = $this->config->get('pagar_me_boleto_criptografia');
        }

        if (isset($this->request->post['pagar_me_boleto_dias_vencimento'])) {
            $this->data['pagar_me_boleto_dias_vencimento'] = $this->request->post['pagar_me_boleto_dias_vencimento'];
        } else {
            $this->data['pagar_me_boleto_dias_vencimento'] = $this->config->get('pagar_me_boleto_dias_vencimento');
        }

        if (isset($this->request->post['pagar_me_boleto_api'])) {
            $this->data['pagar_me_boleto_api'] = $this->request->post['pagar_me_boleto_api'];
        } else {
            $this->data['pagar_me_boleto_api'] = $this->config->get('pagar_me_boleto_api');
        }

        if (isset($this->request->post['pagar_me_boleto_nome'])) {
            $this->data['pagar_me_boleto_nome'] = $this->request->post['pagar_me_boleto_nome'];
        } else {
            $this->data['pagar_me_boleto_nome'] = $this->config->get('pagar_me_boleto_nome');
        }

        if (isset($this->request->post['pagar_me_boleto_text_information'])) {
            $this->data['pagar_me_boleto_text_information'] = $this->request->post['pagar_me_boleto_text_information'];
        } else {
            $this->data['pagar_me_boleto_text_information'] = $this->config->get('pagar_me_boleto_text_information');
        }

        if (isset($this->request->post['pagar_me_boleto_order_waiting_payment'])) {
            $this->data['pagar_me_boleto_order_waiting_payment'] = $this->request->post['pagar_me_boleto_order_waiting_payment'];
        } else {
            $this->data['pagar_me_boleto_order_waiting_payment'] = $this->config->get('pagar_me_boleto_order_waiting_payment');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['pagar_me_boleto_geo_zone_id'])) {
            $this->data['pagar_me_boleto_geo_zone_id'] = $this->request->post['pagar_me_boleto_geo_zone_id'];
        } else {
            $this->data['pagar_me_boleto_geo_zone_id'] = $this->config->get('pagar_me_boleto_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pagar_me_boleto_status'])) {
            $this->data['pagar_me_boleto_status'] = $this->request->post['pagar_me_boleto_status'];
        } else {
            $this->data['pagar_me_boleto_status'] = $this->config->get('pagar_me_boleto_status');
        }

        if (isset($this->request->post['pagar_me_boleto_sort_order'])) {
            $this->data['pagar_me_boleto_sort_order'] = $this->request->post['pagar_me_boleto_sort_order'];
        } else {
            $this->data['pagar_me_boleto_sort_order'] = $this->config->get('pagar_me_boleto_sort_order');
        }

        $this->template = 'payment/pagar_me_boleto.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate()
    {

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

    public function install()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pagar_me_transaction` (
  `pagar_me_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(512) DEFAULT NULL,
  `n_parcela` int(11) DEFAULT '0',
  `bandeira` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pagar_me_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` ADD `pagar_me_boleto_url` VARCHAR( 512 ) NULL DEFAULT NULL AFTER  `order_id`");
    }

    public function uninstall()
    {
        if (!$this->config->get('pagar_me_cartao_status')) {
            $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pagar_me_transaction`");
        }

        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP COLUMN `pagar_me_boleto_url`");
    }

}

?>