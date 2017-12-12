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

        var boletoPage = window.open('', 'boletoPage');
        boletoPage.document.body.innerHTML = "<h1>Seu boleto está sendo gerado, por favor aguarde.</h1>";

        $('#button-confirm').attr('disabled', true);
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            async: false,
            data: { amount: '<?php echo $total; ?>' },
            dataType: 'json',
            success: function (response) {
                $(".pagar_me_error_message").remove();
                if (response.hasOwnProperty('error')) {
                  $('#button-confirm').removeAttr('disabled');

                  let errorBox = document.createElement("p");
                  errorBox.innerHTML = response.error;
                  errorBox.className = 'pagar_me_error_message boleto_error';

                  $(".buttons").prepend(errorBox);

                  boletoPage.close();

                  return false;

                } else {
                    boletoPage.location.href = response['pagar_me_boleto_url'];
                    location = '<?php echo $url; ?>';
                }
            }
        });
    });

    function confirmExit() {
        return "Você está tentando deixar a página sem concluir o pedido. Por favor, antes de sair conclua o pedido clicando em concluir pedido na janela do boleto. Obrigado!";
    }

    function notConfirmExit() {
        null;
    }
</script>

