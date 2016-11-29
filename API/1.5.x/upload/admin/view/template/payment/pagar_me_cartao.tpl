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
	        <td><input type="text" name="pagar_me_cartao_nome" value="<?php echo $pagar_me_cartao_nome; ?>" size="50%" />
	          <?php if ($error_nome) { ?>
	          <span class="error"><?php echo $error_nome; ?></span>
	          <?php } ?></td>
	      </tr>
	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_criptografia; ?></td>
	        <td><input type="text" name="pagar_me_cartao_criptografia" value="<?php echo $pagar_me_cartao_criptografia; ?>" size="50%" />
	          <?php if ($error_criptografia) { ?>
	          <span class="error"><?php echo $error_criptografia; ?></span>
	          <?php } ?></td>
	      </tr>
	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_api; ?></td>
	        <td><input type="text" name="pagar_me_cartao_api" value="<?php echo $pagar_me_cartao_api; ?>" size="50%" />
	          <?php if ($error_api) { ?>
	          <span class="error"><?php echo $error_api; ?></span>
	          <?php } ?></td>
	      </tr>

              <tr>
	        <td><?php echo $entry_text_information; ?></td>
                <td><textarea name="pagar_me_cartao_text_information" cols="40" rows="5"><?php echo $pagar_me_cartao_text_information; ?></textarea></td>
	      </tr>
              
              <tr>
	        <td><span class="required">*</span> <?php echo $entry_max_parcelas; ?></td>
	        <td><input type="text" name="pagar_me_cartao_max_parcelas" value="<?php echo $pagar_me_cartao_max_parcelas; ?>" size="50%" />
	          <?php if ($error_max_parcelas) { ?>
	          <span class="error"><?php echo $error_max_parcelas; ?></span>
	          <?php } ?></td>
	      </tr>
              
              <tr>
	        <td><span class="required">*</span> <?php echo $entry_parcelas_sem_juros; ?></td>
	        <td><input type="text" name="pagar_me_cartao_parcelas_sem_juros" value="<?php echo $pagar_me_cartao_parcelas_sem_juros; ?>" size="50%" />
	          <?php if ($error_parcelas_sem_juros) { ?>
	          <span class="error"><?php echo $error_parcelas_sem_juros; ?></span>
	          <?php } ?></td>
	      </tr>
              
              <tr>
	        <td><span class="required">*</span> <?php echo $entry_valor_parcela; ?></td>
	        <td><input type="text" name="pagar_me_cartao_valor_parcela" value="<?php echo $pagar_me_cartao_valor_parcela; ?>" size="50%" />
	          <?php if ($error_valor_parcela) { ?>
	          <span class="error"><?php echo $error_valor_parcela; ?></span>
	          <?php } ?></td>
	      </tr>
              
              <tr>
	        <td><span class="required">*</span> <?php echo $entry_taxa_juros; ?></td>
	        <td><input type="text" name="pagar_me_cartao_taxa_juros" value="<?php echo $pagar_me_cartao_taxa_juros; ?>" size="50%" />
	          <?php if ($error_taxa_juros) { ?>
	          <span class="error"><?php echo $error_taxa_juros; ?></span>
	          <?php } ?></td>
	      </tr>

              <tr>
	        <td><?php echo $entry_order_processing; ?></td>
	        <td><select name="pagar_me_cartao_order_processing" id="pagar_me_cartao_order_processing">
	          <?php foreach ($order_statuses as $order_status) { ?>
	          <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_processing) { ?>
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
	        <td><select name="pagar_me_cartao_order_paid" id="pagar_me_cartao_order_paid">
	          <?php foreach ($order_statuses as $order_status) { ?>
	          <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_paid) { ?>
	          <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
	          <?php } else { ?>
	          <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
	          <?php } ?>
	          <?php } ?>
	        </select>
	        </td>
	      </tr>
	      <tr>
	        <td><?php echo $entry_order_refused; ?></td>
	        <td><select name="pagar_me_cartao_order_refused" id="pagar_me_cartao_order_refused">
	          <?php foreach ($order_statuses as $order_status) { ?>
	          <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_refused) { ?>
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
	        <td><select name="pagar_me_cartao_order_refunded" id="pagar_me_cartao_order_refunded">
	          <?php foreach ($order_statuses as $order_status) { ?>
	          <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_refunded) { ?>
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
			  <select name="pagar_me_cartao_geo_zone_id">
	            <option value="0"><?php echo $text_all_zones; ?></option>
	            <?php foreach ($geo_zones as $geo_zone) { ?>
	            <?php if ($geo_zone['geo_zone_id'] == $pagar_me_cartao_geo_zone_id) { ?>
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
			  <select name="pagar_me_cartao_status">
	            <?php if ($pagar_me_cartao_status) { ?>
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
	        <td><input type="text" name="pagar_me_cartao_sort_order" value="<?php echo $pagar_me_cartao_sort_order; ?>" size="1" /></td>
	      </tr>
	    </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>