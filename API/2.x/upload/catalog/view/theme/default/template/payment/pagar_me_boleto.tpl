<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<script src="catalog/view/javascript/jquery.mask.min.js"></script>
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>
<?php if(empty($customer_document_number)) { ?>
<div class="payment_data">
    <form id="payment_form" method="POST">
        <div>
            <img src="catalog/view/theme/default/image/logo-full.png" class="logo"></img>
            <label for="customer_document_number">CPF/CNPJ(somente números)</label>
            <input type="text" id="customer_document_number" value="" maxlength=18/>
        </div>
   </form>
</div>
<?php } ?>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary"
               data-loading-text="Gerando boleto..."/>
    </div>
</div>
<script type="text/javascript">
    var documentNumberField = $("#customer_document_number");
    $('#button-confirm').bind('click', function (e) {
        e.preventDefault();
        $('#button-confirm').button('loading');
        transactionData = {
            amount: '<?php echo $total; ?>'
        };
        if(documentNumberField) {
            transactionData.document_number = documentNumberField.val();
        }
        $.ajax({
            type: 'POST',
            url: 'index.php?route=payment/pagar_me_boleto/payment',
            data: transactionData,
            dataType: 'json',
            success: function (response) {
                $(".pagar_me_error_message").remove();

                if (response.hasOwnProperty('error')) {
                  $('#button-confirm').button('reset');

                  let errorBox = document.createElement("p");
                  errorBox.innerHTML = response.error;
                  errorBox.className = 'pagar_me_error_message boleto_error';

                  $(".buttons").prepend(errorBox);

                  //Scroll to error messages
                  $('html, body').animate({
                    scrollTop: $(".pagar_me_error_message").first().offset().top
                  }, 500);

                  return false;
                } else {
                  $('#button-confirm').button('loading');
                  window.location = '<?php echo $url; ?>';
                }
            }
        });
    });

    var documentNumberMask = function (val) {
        return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
    },
    cpfOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(documentNumberMask.apply({}, arguments), options);
        }
    };
    documentNumberField.mask(documentNumberMask, cpfOptions);
</script>

