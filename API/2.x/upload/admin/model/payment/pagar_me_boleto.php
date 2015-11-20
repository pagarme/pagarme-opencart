<?php

class ModelPaymentPagarMeBoleto extends Model
{
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

        $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` ADD `pagar_me_boleto_url` VARCHAR( 512 ) NULL DEFAULT NULL AFTER  `payment_code`");
    }

    public function uninstall()
    {
        if(!$this->config->get('pagar_me_cartao_status')) {
            $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pagar_me_transaction`");
        }

        $this->db->query("ALTER TABLE  `" . DB_PREFIX . "order` DROP `pagar_me_boleto_url`");
    }
}