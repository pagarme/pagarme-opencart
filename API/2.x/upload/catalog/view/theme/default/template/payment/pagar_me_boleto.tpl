<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" data-loading-text="Gerando boleto..." />
    </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function (e) {
        e.preventDefault();
        var w = window.open('', 'janelaBoleto', 'height=600,width=800,channelmode=0,dependent=0,directories=0,fullscreen=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,toolbar=0')
        w.document.body.innerHTML = "<h1>Por favor aguarde...</h1>";
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            async: false,
            data: { amount: '<?php echo $total; ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#button-confirm').button('loading');
            },
            success: function (response) {
                if (response['error']) {
                    alert('Ocorreu um erro inesperado. Por favor contate a loja.');
                } else {

                    w.location.href = '<?php echo HTTPS_SERVER ?>index.php?route=payment/pagar_me_boleto/gera&boleto=' + response['boleto_url'], 'janelaBoleto';
                }
            },
            complete: function () {
                location = '<?php echo $url; ?>';
            }
        });
    });

    function confirmExit() {
        return "Você está tentando deixar a página sem concluir o pedido. Por favor, antes de sair conclua o pedido clicando em concluir pedido na janela do boleto. Obrigado!";
    }

    function notConfirmExit() {
        null;
    }
//--></script>
</script>

