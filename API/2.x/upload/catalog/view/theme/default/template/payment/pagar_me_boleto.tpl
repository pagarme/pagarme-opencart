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

        var boletoPage = window.open('', 'boletoPage');
        boletoPage.document.body.innerHTML = "<h1>Seu boleto está sendo gerado, por favor aguarde.</h1>";

        $('#button-confirm').attr('disabled', true);
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            async: false,
            data: {amount: '<?php echo $total; ?>'},
            dataType: 'json',

            success: function (response) {
                $(".pagar_me_error_message").remove();

                if (response.hasOwnProperty('error')) {
                  $('#button-confirm').removeAttr('disabled');

                  let errorBox = document.createElement("p");
                  errorBox.innerHTML = response.error;
                  errorBox.className = 'pagar_me_error_message boleto_error';

                  $(".buttons").prepend(errorBox);

                  //Scroll to error messages
                  $('html, body').animate({
                    scrollTop: $(".pagar_me_error_message").first().offset().top
                  }, 500);

                  boletoPage.close();

                  return false;

                } else {
                  boletoPage.location.href = response['pagar_me_boleto_url'];
                  window.location = '<?php echo $url; ?>';
                }
            }
        });
    });
    //--></script>
</script>

