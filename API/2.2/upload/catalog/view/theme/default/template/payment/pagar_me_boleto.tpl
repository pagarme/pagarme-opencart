<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary"
               data-loading-text="Gerando boleto..."/>
    </div>
</div>
<script type="text/javascript"><!--
    $('#button-confirm').bind('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            async: false,
            data: {amount: '<?php echo $total; ?>'},
            dataType: 'json',
            beforeSend: function () {
                $('#button-confirm').button('loading');
            },
            success: function (response) {
                if (response.hasOwnProperty('error')) {
                    alert('ERROR: ' + response.error);
                } else {
                    window.location = '<?php echo $url; ?>';
                }
            }
        });
    });
    //--></script>
</script>

