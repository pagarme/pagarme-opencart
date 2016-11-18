<?php
class ModelExtensionTotalPagarMeCheckoutDesconto extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {



			if(isset($this->session->data['payment_method']['code']))
  			$paymethod = $this->session->data['payment_method']['code'];

			if(isset($paymethod) && $paymethod == 'pagar_me_checkout') {


			    $percent = $this->config->get('pagar_me_checkout_boleto_discount_percentage') / 100;
				$percent = $total * $percent;
				$total_data[] = array(
					'code'		 => 'pagar_me_checkout_desconto',
					'title'      => 'Desconto de ' . $this->config->get('pagar_me_checkout_boleto_discount_percentage'). '%',
					'value'      => $percent*-1,
					'sort_order' => $this->config->get('total_sort_order')-1
				);
				$total -= $percent;

		  }

	}
}
?>
