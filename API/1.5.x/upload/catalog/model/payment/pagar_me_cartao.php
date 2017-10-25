<?php

class ModelPaymentPagarMeCartao extends Model {

    public function getMethod($address, $total) {
        $this->load->language('payment/pagar_me_cartao');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('pagar_me_cartao_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('pagar_me_cartao_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'pagar_me_cartao',
                'title' => $this->config->get('pagar_me_cartao_nome'),
                'sort_order' => $this->config->get('pagar_me_cartao_sort_order')
            );
        }

        return $method_data;
    }

    public function addTransactionId($order_id, $transaction_id, $n_parcela, $bandeira) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pagar_me_transaction` SET order_id = '" . (int) $order_id . "', transaction_id = '" . $this->db->escape($transaction_id) . "', n_parcela = '" . $this->db->escape($n_parcela) . "', bandeira = '" . $this->db->escape($bandeira) . "'");
    }

    public function getPagarMeOrder($transaction_id) {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pagar_me_transaction` WHERE transaction_id = '" . $this->db->escape($transaction_id) . "'");

        if ($order_query->num_rows) {
            return $order_query->row['order_id'];
        } else {
            return false;
        }
    }

    public function getPagarMeOrderByOrderId($order_id) {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pagar_me_transaction` WHERE order_id = '" . (int) $order_id . "'");

        if ($order_query->num_rows) {
            return $order_query->row;
        } else {
            return false;
        }
    }

    public function updateOrderAmount($order_id, $taxedAmount){
        $taxedAmount = $this->formatAmount($taxedAmount);

        $this->db->query(
            "UPDATE " . DB_PREFIX . "order_total
            SET value = '" . $taxedAmount['four_decimal_places'] .  "', text = '$". $taxedAmount['two_decimal_places']  ."'
            WHERE order_id = " . (int)$order_id . "
            AND code = 'total'"
        );

        $this->db->query(
            "UPDATE " . DB_PREFIX . "order
            SET total = '" . $taxedAmount['two_decimal_places'] . "'
            WHERE order_id = " . (int)$order_id .""
        );
    }

    public function insertInterestRate($order_id, $interestAmount){
        $interestAmount = $this->formatAmount($interestAmount);
        $this->db->query(
            "INSERT INTO " . DB_PREFIX . "order_total (order_id, code, title, text, value, sort_order)
            VALUES ($order_id, 'tax', 'Interest amount', '$". $interestAmount['two_decimal_places']."', '".$interestAmount['four_decimal_places']."', " . $this->config->get('config_tax') . " )"
        );
    }

    private function formatAmount($amountInCents){

        return  array(
            'two_decimal_places' => number_format((float)($amountInCents / 100), 2, '.', ''),
            'four_decimal_places' => number_format($amountInCents, 4, '.', '')
        );
    }

}

?>
