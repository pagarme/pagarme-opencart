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

            success: function (response) {
                $(".pagar_me_error_message").remove();

                if (response.hasOwnProperty('error')) {
                  $('#button-confirm').button('reset');

                  let errorBox = document.createElement("p");
                  errorBox.innerHTML = response.error;
                  errorBox.className = 'pagar_me_error_message';

                  $(".buttons").prepend(errorBox);
                  $(errorBox).css("background-color", "#E82C0C");
                  $(errorBox).css("padding", "5px");
                  $(errorBox).css("color", "#ffffff");
                  $(errorBox).css("width", "50%");
                  $(errorBox).css("border-radius", "2px");
                  $(errorBox).css("text-align", "center");
                  $(errorBox).css("border-left", "2px solid #991D08");
                  $(errorBox).css("margin-left", "auto");
                  $(errorBox).css("margin-right", "auto");

                  return false;
                } else {
                  $('#button-confirm').button('loading');
                  window.location = '<?php echo $url; ?>';
                }
            }
        });
    });
    //--></script>
</script>

