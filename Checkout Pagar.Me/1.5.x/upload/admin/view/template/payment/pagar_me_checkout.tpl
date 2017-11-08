<?php echo $header; ?>
	<div id="content">
		<div class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
			<?php } ?>
		</div>
		<?php if ($error_warning) { ?>
			<div class="warning"><?php echo $error_warning; ?></div>
		<?php } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
				<div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
			</div>
			<div class="content">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td><span class="required">*</span> <?php echo $entry_nome; ?></td>
							<td><input type="text" name="pagar_me_checkout_nome" value="<?php echo $pagar_me_checkout_nome; ?>" size="50%" />
								<?php if ($error_nome) { ?>
									<span class="error"><?php echo $error_nome; ?></span>
								<?php } ?></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $entry_criptografia; ?></td>
							<td><input type="text" name="pagar_me_checkout_criptografia" value="<?php echo $pagar_me_checkout_criptografia; ?>" size="50%" />
								<?php if ($error_criptografia) { ?>
									<span class="error"><?php echo $error_criptografia; ?></span>
								<?php } ?></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $entry_api; ?></td>
							<td><input type="text" name="pagar_me_checkout_api" value="<?php echo $pagar_me_checkout_api; ?>" size="50%" />
								<?php if ($error_api) { ?>
									<span class="error"><?php echo $error_api; ?></span>
								<?php } ?></td>
						</tr>

						<tr>
							<td><?php echo $entry_text_information; ?></td>
							<td><textarea name="pagar_me_checkout_text_information" cols="40" rows="5"><?php echo $pagar_me_checkout_text_information; ?></textarea></td>
						</tr>

						<tr>
							<td><?php echo $entry_texto_botao; ?></td>
							<td><input type="text" name="pagar_me_checkout_texto_botao" value="<?php echo $pagar_me_checkout_texto_botao; ?>" size="50%" />
						</tr>

						<tr>
							<td><span class="required">*</span> <?php echo $entry_payment_methods; ?></td>
							<td><input type="checkbox" name="pagar_me_checkout_payment_methods[]" value="boleto"<?php echo in_array('boleto', $pagar_me_checkout_payment_methods) ? ' checked="checked"' : ''; ?> /> Boleto <br>
								<input type="checkbox" name="pagar_me_checkout_payment_methods[]" value="credit_card"<?php echo in_array('credit_card', $pagar_me_checkout_payment_methods) ? ' checked="checked"' : ''; ?> /> Cartão de crédito
								<?php if ($error_payment_methods) { ?>
									<span class="error"><?php echo $error_payment_methods; ?></span>
								<?php } ?></td>
						</tr>

						<tr>
							<td><span class="required">*</span> <?php echo $entry_card_brands; ?></td>
							<td><input type="checkbox" name="pagar_me_checkout_card_brands[]" value="visa"<?php echo in_array('visa', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> Visa <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="mastercard"<?php echo in_array('mastercard', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> MasterCard <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="amex"<?php echo in_array('amex', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> American Express <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="aura"<?php echo in_array('aura', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> Aura <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="jcb"<?php echo in_array('jcb', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> JCB <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="diners"<?php echo in_array('diners', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> Diners <br>
								<input type="checkbox" name="pagar_me_checkout_card_brands[]" value="elo"<?php echo in_array('elo', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> /> Elo
								<?php if ($error_card_brands) { ?>
									<span class="error"><?php echo $error_card_brands; ?></span>
								<?php } ?></td>
						</tr>

						<tr>
							<td><?php echo $entry_max_installments; ?></td>
							<td><input type="text" name="pagar_me_checkout_max_installments" value="<?php echo $pagar_me_checkout_max_installments; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_free_installments; ?></td>
							<td><input type="text" name="pagar_me_checkout_free_installments" value="<?php echo $pagar_me_checkout_free_installments; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_max_installment_value; ?></td>
							<td><input type="text" name="pagar_me_checkout_max_installment_value" value="<?php echo $pagar_me_checkout_max_installment_value; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_interest_rate; ?></td>
							<td><input type="text" name="pagar_me_checkout_interest_rate" value="<?php echo $pagar_me_checkout_interest_rate; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_boleto_discount_percentage; ?></td>
							<td><input type="text" name="pagar_me_checkout_boleto_discount_percentage" value="<?php echo $pagar_me_checkout_boleto_discount_percentage; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_ui_color; ?></td>
							<td><input type="text" name="pagar_me_checkout_ui_color" value="<?php echo $pagar_me_checkout_ui_color; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_button_css_class; ?></td>
							<td><input type="text" name="pagar_me_checkout_button_css_class" value="<?php echo $pagar_me_checkout_button_css_class; ?>" size="50%" />
						</tr>

						<tr>
							<td><?php echo $entry_order_waiting_payment; ?></td>
							<td><select name="pagar_me_checkout_order_waiting_payment" id="pagar_me_checkout_order_waiting_payment">
									<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $pagar_me_checkout_order_waiting_payment) { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_order_paid; ?></td>
							<td><select name="pagar_me_checkout_order_paid" id="pagar_me_checkout_order_paid">
									<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $pagar_me_checkout_order_paid) { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_order_refunded; ?></td>
							<td><select name="pagar_me_checkout_order_refunded" id="pagar_me_checkout_order_refunded">
									<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $pagar_me_checkout_order_refunded) { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_geo_zone; ?></td>
							<td>
								<select name="pagar_me_checkout_geo_zone_id">
									<option value="0"><?php echo $text_all_zones; ?></option>
									<?php foreach ($geo_zones as $geo_zone) { ?>
										<?php if ($geo_zone['geo_zone_id'] == $pagar_me_checkout_geo_zone_id) { ?>
											<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_status; ?></td>
							<td>
								<select name="pagar_me_checkout_status">
									<?php if ($pagar_me_checkout_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_sort_order; ?></td>
							<td><input type="text" name="pagar_me_checkout_sort_order" value="<?php echo $pagar_me_checkout_sort_order; ?>" size="1" /></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$('input[name=pagar_me_checkout_ui_color]').ColorPicker({
			onChange: function (hsb, hex, rgb) {
				$('input[name=pagar_me_checkout_ui_color]').val('#' + hex);
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$(el).val('#' + hex);
				$(el).ColorPickerHide();
			}
		}).bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
	</script>
<?php echo $footer; ?>
