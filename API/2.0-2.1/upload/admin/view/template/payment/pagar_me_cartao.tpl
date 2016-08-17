<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-pagar-me-cartao" data-toggle="tooltip"
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
                      id="form-pagar-me-cartao" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-nome"><span data-toggle="tootip" title="<?php echo $help_nome; ?>"><?php echo $entry_nome; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_cartao_nome"
                                   value="<?php echo $pagar_me_cartao_nome; ?>"
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
                            <input type="text" name="pagar_me_cartao_criptografia"
                                   value="<?php echo $pagar_me_cartao_criptografia; ?>"
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
                            <input type="text" name="pagar_me_cartao_api"
                                   value="<?php echo $pagar_me_cartao_api; ?>" placeholder="<?php echo $entry_api; ?>"
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
                            <textarea rows="5" name="pagar_me_cartao_text_information" id="input-information"
                                      class="form-control"><?php echo $pagar_me_cartao_text_information; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-max_parcelas"><?php echo $entry_max_parcelas; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_cartao_max_parcelas"
                                   value="<?php echo $pagar_me_cartao_max_parcelas; ?>"
                                   placeholder="<?php echo $entry_max_parcelas; ?>" id="input-max_parcelas"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-taxa_juros"><span data-toggle="tootip" title="<?php echo $help_taxa_juros; ?>"><?php echo $entry_taxa_juros; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_cartao_taxa_juros"
                                   value="<?php echo $pagar_me_cartao_taxa_juros; ?>"
                                   placeholder="<?php echo $entry_taxa_juros; ?>" id="input-taxa_juros"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-parcelas_sem_juros"><?php echo $entry_parcelas_sem_juros; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_cartao_parcelas_sem_juros"
                                   value="<?php echo $pagar_me_cartao_parcelas_sem_juros; ?>"
                                   placeholder="<?php echo $entry_parcelas_sem_juros; ?>" id="input-parcelas_sem_juros"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-valor_parcela"><?php echo $entry_valor_parcela; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="pagar_me_cartao_valor_parcela"
                                   value="<?php echo $pagar_me_cartao_valor_parcela; ?>"
                                   placeholder="<?php echo $entry_valor_parcela; ?>" id="input-valor_parcela"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-pagar_me_cartao_order_processing"><?php echo $entry_order_processing; ?></label>

                        <div class="col-sm-10">
                            <select name="pagar_me_cartao_order_processing"
                                    id="input-pagar_me_cartao_order_processing" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_processing) { ?>
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
                               for="input-pagar_me_cartao_order_waiting_payment"><?php echo $entry_order_paid; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_cartao_order_paid" id="input-pagar_me_cartao_order_paid"
                                    class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_paid) { ?>
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
                               for="input-pagar_me_cartao_order_waiting_payment"><?php echo $entry_order_refused; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_cartao_order_refused" id="input-pagar_me_cartao_order_refused"
                                    class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_refused) { ?>
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
                               for="input-pagar_me_cartao_order_waiting_payment"><?php echo $entry_order_refunded; ?></label>
                        <div class="col-sm-10">
                            <select name="pagar_me_cartao_order_refunded" id="input-pagar_me_cartao_order_refunded"
                                    class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $pagar_me_cartao_order_refunded) { ?>
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
                            <select name="pagar_me_cartao_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                    <?php if ($geo_zone['geo_zone_id'] == $pagar_me_cartao_geo_zone_id) { ?>
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
                            <select name="pagar_me_cartao_status" id="input-status" class="form-control">
                                <?php if ($pagar_me_cartao_status) { ?>
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
                            <input type="text" name="pagar_me_cartao_sort_order" value="<?php echo $pagar_me_cartao_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?> 