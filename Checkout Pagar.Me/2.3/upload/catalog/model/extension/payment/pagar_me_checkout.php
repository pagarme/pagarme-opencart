<?php

class ModelExtensionPaymentPagarMeCheckout extends Model
{

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/pagar_me_checkout');

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

    public function addTransactionId($order_id, $transaction_id, $boleto_url = null, $n_parcela = 0, $bandeira = null)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pagar_me_checkout_transaction` SET order_id = '" . (int) $order_id . "', transaction_id =
'" . $this->db->escape($transaction_id) . "', n_parcela = '" . (int)$n_parcela . "', bandeira = '" . $this->db->escape($bandeira) . "'");

        if (!is_null($boleto_url)) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET pagar_me_checkout_url = '" . $this->db->escape($boleto_url) . "' WHERE
        order_id = '" . (int)$order_id . "'");
        }
    }

    public function getPagarMeOrder($transaction_id)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pagar_me_checkout_transaction` WHERE transaction_id = '" . $this->db->escape($transaction_id) . "'");

        if ($order_query->num_rows) {
            return $order_query->row['order_id'];
        } else {
            return false;
        }
    }

    public function getTotalOrderHistoriesByOrderStatusId($order_status_id, $order_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "' AND order_id = '" . (int) $order_id . "'");

        return $query->row['total'];
    }

    public function addDescontoBoleto($order_id){
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($order_id);

        $desconto = $this->config->get('pagar_me_checkout_boleto_discount_percentage');

        /* Pega a order do sub-toal */
        //$sub_total_order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'sub_total'");

        $total_order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'total'");

        $discount_order = $total_order_query->row['sort_order'] - 1;

        $valor_desconto = $total_order_query->row['value'] * $desconto / 100;

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = 'pagar_me_checkout_desconto', title = 'Desconto do boleto (" . $this->db->escape($desconto) . "%)', `value` = '" . (float)$valor_desconto*-1 . "', sort_order = '" . $discount_order . "'");

        /* Atualiza total do pedido */
        $valor_com_desconto = $order['total'] - $valor_desconto;

        $this->db->query("UPDATE " . DB_PREFIX . "order_total SET `value` = '" . (float)$valor_com_desconto . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'total'");

        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$valor_com_desconto . "' WHERE order_id = '" . (int)$order_id . "'");

    }

}

?>