<?php

class ModelPaymentPagarMeBoleto extends Model {

    public function getMethod($address, $total) {
        $this->load->language('payment/pagar_me_boleto');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('pagseguro_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('pagar_me_boleto_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'pagar_me_boleto',
                'title' => $this->config->get('pagar_me_boleto_nome'),
                'sort_order' => $this->config->get('pagar_me_boleto_sort_order')
            );
        }

        return $method_data;
    }

    public function addTransactionId($order_id, $transaction_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET pagar_me_transaction = '" . $transaction_id . "' WHERE order_id = '" . (int) $order_id . "'");
    }

    public function getOrderByTransactionId($transaction_id) {
        $order_query = $this->db->query("SELECT o.order_id FROM `" . DB_PREFIX . "order` o WHERE o.pagar_me_transaction = '" . (int) $transaction_id . "'");

        if ($order_query->num_rows) {
            return $order_query->row['order_id'];
        } else {
            return false;
        }
    }

}

?>