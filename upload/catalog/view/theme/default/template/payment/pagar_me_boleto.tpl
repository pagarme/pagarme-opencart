<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>
<div class="buttons">
    <div class="right"><a id="button-confirm" class="button"><span><?php echo $button_confirm; ?></span></a><span id="aguardando">Gerando boleto...</span></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function (e) {
        e.preventDefault();
        $('#button-confirm').hide();
        $('#aguardando').show();
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            async: false,
            data: { amount: '<?php echo $total; ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#button-confirm').attr('disabled', true);

                $('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
            },
            success: function (response) {
                if (response['error']) {
                    alert('Ocorreu um erro inesperado. Por favor contate a loja.');
                } else {
                    console.log(response);
                    window.onbeforeunload = confirmExit;
                    $.colorbox({
                        href: response['boleto_url'],
                        iframe: true,
                        width: '90%',
                        height: '90%',
                        onLoad: function () {
                            $("#cboxClose").css('background', 'transparent');
                            $("#cboxClose").css('width', '400px');
                            $("#cboxClose").css('text-indent', '0');
                            $("#cboxClose").html("<span style='display: block; text-align: center; font-weight: bold; width: auto; background-color: #D34937; padding: 2px; color: #FFFFFF;'>Após imprimir o boleto, clique aqui para concluir a compra.</span>");
                        },
                        onClosed: function () {
                            window.onbeforeunload = function () {
                                null;
                            };
                            location = '<?php echo $url; ?>';
                        }
                    });
                }
            },
            complete: function () {
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

