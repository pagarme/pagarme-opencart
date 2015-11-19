<?php

class ModelPaymentPagarMeCheckout extends Model
{

    public function getMethod($address, $total)
    {
        $this->load->language('payment/pagar_me_checkout');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pagar_me_checkout_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('pagar_me_checkout_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'pagar_me_checkout',
                'title' => $this->config->get('pagar_me_checkout_nome'),
                'terms' => '',
                'sort_order' => $this->config->get('pagar_me_checkout_sort_order')
            );
        }

        return $method_data;
    }

    public function addTransactionId($order_id, $transaction_id, $boleto_url = null)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pagar_me_transaction` SET order_id = '" . (int) $order_id . "', transaction_id =
'" . $this->db->escape($transaction_id) . "'");

        if (!is_null($boleto_url)) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET pagar_me_checkout_url = '" . $this->db->escape($boleto_url) . "' WHERE
        order_id = '" . (int)$order_id . "'");
        }
    }

    public function getPagarMeOrder($transaction_id)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pagar_me_transaction` WHERE transaction_id = '" . $this->db->escape($transaction_id) . "'");

        if ($order_query->num_rows) {
            return $order_query->row['order_id'];
        } else {
            return false;
        }
    }

}

?>