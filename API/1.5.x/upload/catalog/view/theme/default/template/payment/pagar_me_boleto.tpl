<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<script src="catalog/view/javascript/jquery.mask.min.js"></script>
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

<?php if($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="buttons">
    <div class="right">
        <a id="button-confirm" class="button">
                <span id=><?php echo $button_confirm; ?></span>
        </a>
        <span id="aguardando">Gerando boleto...</span>
     </div>
</div>

<script type="text/javascript">
var documentNumberField = $("#customer_document_number");
$('#button-confirm').on('click', function (e) {
        e.preventDefault();
        $('#button-confirm').css('display', 'none');
        $('#aguardando').css('display', 'block');
        transactionData = {};
        if(documentNumberField) {
           transactionData.document_number = documentNumberField.val();
        }
        transactionData.amount = '<?php echo $total; ?>';
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

                  $('#button-confirm').css('display', 'inline');
                  $('#aguardando').css('display', 'none');

                  return false;
                } else {
                    $('#button-confirm').button('loading');
                    location = '<?php echo $url; ?>';
                }
            }
        });
    });

documentNumberField.keydown(function() {

   if(documentNumberField.val().length > 14) {
     documentNumberField.unmask();
     documentNumberField.mask("99.999.999/9999-99");
   } else{
     documentNumberField.mask("999.999.999-990000");
   }

})
    function confirmExit() {
        return "Você está tentando deixar a página sem concluir o pedido. Por favor, antes de sair conclua o pedido clicando em concluir pedido na janela do boleto. Obrigado!";
    }

    function notConfirmExit() {
        null;
    }
</script>

