<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-pagar-me-checkout" data-toggle="tooltip"
                        title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"
                      id="form-pagar-me-checkout" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-nome"><span data-toggle="tootip" title="<?php echo $help_nome; ?>"><?php echo $entry_nome; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_nome"
                                   value="<?php echo $pagar_me_checkout_nome; ?>"
                                   placeholder="<?php echo $entry_nome; ?>" id="input-nome" class="form-control"/>
                            <?php if ($error_nome) { ?>
                                <div class="text-danger"><?php echo $error_nome; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label"
                               for="input-criptografia"><span data-toggle="tootip" title="<?php echo $help_criptografia; ?>"><?php echo $entry_criptografia; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_criptografia"
                                   value="<?php echo $pagar_me_checkout_criptografia; ?>"
                                   placeholder="<?php echo $entry_criptografia; ?>" id="input-criptografia"
                                   class="form-control"/>
                            <?php if ($error_criptografia) { ?>
                                <div class="text-danger"><?php echo $error_criptografia; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-api"><span data-toggle="tootip" title="<?php echo $help_api; ?>"><?php echo $entry_api; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_api"
                                   value="<?php echo $pagar_me_checkout_api; ?>" placeholder="<?php echo $entry_api; ?>"
                                   id="input-api" class="form-control"/>
                            <?php if ($error_api) { ?>
                                <div class="text-danger"><?php echo $error_api; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-information"><?php echo $entry_text_information; ?></label>

                        <div class="col-sm-10">
                            <textarea rows="5" name="pagar_me_checkout_text_information" id="input-information"
                                      class="form-control"><?php echo $pagar_me_checkout_text_information; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-texto_botao"><span data-toggle="tootip" title="<?php echo $help_text_botao; ?>"><?php echo $entry_texto_botao; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_texto_botao"
                                   value="<?php echo $pagar_me_checkout_texto_botao; ?>"
                                   placeholder="<?php echo $entry_texto_botao; ?>" id="input-texto_botao"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label"><?php echo $entry_payment_methods; ?></label>

                        <div class="col-sm-10">
                            <input type="checkbox" name="pagar_me_checkout_payment_methods[]"
                                   value="boleto"<?php echo in_array('boleto', $pagar_me_checkout_payment_methods) ? ' checked="checked"' : ''; ?> />
                            Boleto <br>
                            <input type="checkbox" name="pagar_me_checkout_payment_methods[]"
                                   value="credit_card"<?php echo in_array('credit_card', $pagar_me_checkout_payment_methods) ? ' checked="checked"' : ''; ?> />
                            Cartão de crédito
                            <?php if ($error_payment_methods) { ?>
                                <div class="text-danger"><?php echo $error_payment_methods; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label"><?php echo $entry_card_brands; ?></label>

                        <div class="col-sm-10">
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="visa"<?php echo in_array('visa', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            Visa <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="mastercard"<?php echo in_array('mastercard', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            MasterCard <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="amex"<?php echo in_array('amex', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            American Express <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="aura"<?php echo in_array('aura', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            Aura <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="jcb"<?php echo in_array('jcb', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            JCB <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="diners"<?php echo in_array('diners', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            Diners <br>
                            <input type="checkbox" name="pagar_me_checkout_card_brands[]"
                                   value="elo"<?php echo in_array('elo', $pagar_me_checkout_card_brands) ? ' checked="checked"' : ''; ?> />
                            Elo
                            <?php if ($error_card_brands) { ?>
                                <div class="text-danger"><?php echo $error_card_brands; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-max_installments"><?php echo $entry_max_installments; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_max_installments"
                                   value="<?php echo $pagar_me_checkout_max_installments; ?>"
                                   placeholder="<?php echo $entry_max_installments; ?>" id="input-max_installments"
                                   class="form-control"/>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-free_installments"><?php echo $entry_free_installments; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_free_installments"
                                   value="<?php echo $pagar_me_checkout_free_installments; ?>"
                                   placeholder="<?php echo $entry_free_installments; ?>" id="input-free_installments"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-interest_rate"><span data-toggle="tootip" title="<?php echo $help_interest_rate; ?>"><?php echo $entry_interest_rate; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_interest_rate"
                                   value="<?php echo $pagar_me_checkout_interest_rate; ?>"
                                   placeholder="<?php echo $entry_interest_rate; ?>" id="input-interest_rate"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-ui_color"><?php echo $entry_ui_color; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_ui_color"
                                   value="<?php echo $pagar_me_checkout_ui_color; ?>"
                                   placeholder="<?php echo $entry_ui_color; ?>" id="input-ui_color"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-button_css_class"><?php echo $entry_button_css_class; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_button_css_class"
                                   value="<?php echo $pagar_me_checkout_button_css_class; ?>"
                                   placeholder="<?php echo $entry_button_css_class; ?>" id="input-button_css_class"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-pagar_me_checkout_order_waiting_payment"><?php echo $entry_order_waiting_payment; ?></label>

                        <div class="col-sm-10">
                            <select name="pagar_me_checkout_order_waiting_payment"
                                    id="input-pagar_me_checkout_order_waiting_payment" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_checkout_order_waiting_payment) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                        <option
                                            value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-pagar_me_checkout_order_waiting_payment"><?php echo $entry_order_paid; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_checkout_order_paid" id="input-pagar_me_checkout_order_paid"
                                    class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_checkout_order_paid) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                        <option
                                            value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_checkout_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                    <?php if ($geo_zone['geo_zone_id'] == $pagar_me_checkout_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_checkout_status" id="input-status" class="form-control">
                                <?php if ($pagar_me_checkout_status) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_checkout_sort_order" value="<?php echo $pagar_me_checkout_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
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