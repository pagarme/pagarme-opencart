<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<script src="catalog/view/javascript/jquery.mask.min.js" />
<script src="catalog/view/javascript/jquery.creditCardValidator.js"/>

<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="payment_data">
    <form id="payment_form" method="POST">
        <ul class="bandeiras">
            <li class="bandeira amex">
                <img src="catalog/view/theme/default/image/bancos/americanexpress.png" alt="">
                <i class="fa fa-check"></i>
            </li>
            <li class="bandeira diners_club_carte_blanche diners_club_international">
                <img src="catalog/view/theme/default/image/bancos/dinersclub.png" alt="">
                <i class="fa fa-check"></i>
            </li>
            <li class="bandeira discover">
                <img src="catalog/view/theme/default/image/bancos/discover.png" alt="">
                <i class="fa fa-check"></i>
            </li>
            <li class="bandeira mastercard">
                <img src="catalog/view/theme/default/image/bancos/mastercard.png" alt="">
                <i class="fa fa-check"></i>
            </li>
            <li class="bandeira visa">
                <img src="catalog/view/theme/default/image/bancos/visa02.png" alt="">
                <i class="fa fa-check"></i>
            </li>
            <li class="bandeira elo">
                <img src="catalog/view/theme/default/image/bancos/elo.png" alt="">
                <i class="fa fa-check"></i>
            </li>
             <li class="bandeira hipercard">
                <img src="catalog/view/theme/default/image/bancos/hipercard.png" alt="">
                <i class="fa fa-check"></i>
            </li>
        </ul>
        <div class="input-block-float">
            <label for="card_number">Número do cartão</label>
            <input type="text" id="card_number"/>
        </div>
        <div class="input-block-float">
            <label for="card_cvv" id="label-cvv">CVV <span id="tool-tip-cvv"><i class="fa fa-question-circle"></i> <span
                        id="tool-tip-content"><img src="catalog/view/theme/default/image/bancos/cartao-cvv.png" alt=""></span></span></label>
            <input type="text" id="card_cvv" size="4" placeholder="CVV" maxlength="4" class="so_numeros"/>
        </div>
        <div class="cf"></div>
        <div class="input-block">
            <label>Validade do cartão</label>
            <input type="text" id="card_expiration_month" size="2" maxlength="2" placeholder="MM" class="so_numeros"/>
            <input type="text" id="card_expiration_year" size="4" placeholder="AAAA" maxlength="4" class="so_numeros"/>
        </div>
        <div class="input-block">
            <label for="card_holder_name">Nome impresso no cartão</label>
            <input type="text" id="card_holder_name" value=""/>
        </div>

        <?php if(empty($customer_document_number)) { ?>
        <div class="input-block">
            <label for="customer_document_number">CPF/CNPJ (somente números)</label>
            <input type="text" id="customer_document_number" value=""/>
        </div>
        <?php } ?>
        
        <div id="installmentsWrapper">
            <div class="input-block">
                <label for="installmentQuantity">Parcelamento</label>
                <select name="installments" id="installments">
                    <?php foreach ($parcelas['installments'] as $parcela): ?>
                        <option value="<?php echo $parcela['installment'] ?>"><?php echo $parcela['installment'] ?>x de
                            R$ <?php echo number_format(($parcela['installment_amount'] / 100), 2, ',', '.') ?>
                            <?php echo $parcela['installment'] <= $free_installments ? " sem juros" : 'com juros de ' . $interest_rate . '% ao mês' ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>
<div class="buttons">
    <div class="center"><a id="button-confirm"
                           class="button disabled"><span><?php echo $button_confirm; ?></span></a><span id="aguardando">Aguarde...</span>
    </div>
</div>
<style>
    #aguardando {
        display: none;
    }
</style>

<script type="text/javascript"><!--
    $(document).ready(function () {
        /* Máscaras dos inputs do cartão */
        $("#card_number").mask("0000000000000009999999", {clearIfNotMatch: true});

        $("#card_cvv").mask("0009", {clearIfNotMatch: true});

        $("#card_expiration_month").mask("00", {clearIfNotMatch: true});

        $("#card_expiration_year").mask("0000", {clearIfNotMatch: true});

    });

    $("#customer_document_number").keydown(function() {

       if($("#customer_document_number").val().length > 14) {
         $("#customer_document_number").unmask();
         $("#customer_document_number").mask("99.999.999/9999-99");
       } else{
         $("#customer_document_number").mask("999.999.999-990000");
       }

    })
    /*Validação Cartão de crédito*/
    $('#card_number').validateCreditCard(function (result) {
        console.log(result);
        if (result.card_type == null) {
            $(".bandeiras li").removeClass('is-selected');
        } else {
            if (result.luhn_valid == true || result.length_valid == true) {
                $("#payment_form").append($('<input type="hidden" id="bandeira" name="bandeira">').val(result.card_type.name));
                $('#card_number').addClass('valid');
            } else {
                $('#card_number').removeClass('valid');
            }
            $(".bandeiras li").removeClass('is-selected');
            $(".bandeiras li." + result.card_type.name).addClass('is-selected');
        }

    });

    //Show error messages
    function errorFields(errorMessage){
      let errorBox = document.createElement("p");

      errorBox.innerHTML = errorMessage;
      errorBox.className = 'pagar_me_error_message';

      $(".payment_data").prepend(errorBox);

      $('#button-confirm').button('reset');
      $("#payment_form #card_hash").remove();
    }

    //Scroll to error messages
    function scrollToError(){
         $('#button-confirm').button('reset');
         $('html, body').animate({
           scrollTop: $(".pagar_me_error_message").first().offset().top
         }, 500);
    }

    $('#button-confirm').bind('click', function () {

        PagarMe.encryption_key = "<?php echo $this->config->get('pagar_me_cartao_criptografia'); ?>";

        var form = $("#payment_form");

        var creditCard = new PagarMe.creditCard();
        creditCard.cardHolderName = $("#payment_form #card_holder_name").val();
        creditCard.cardExpirationMonth = $("#payment_form #card_expiration_month").val();
        creditCard.cardExpirationYear = $("#payment_form #card_expiration_year").val();
        creditCard.cardNumber = $("#payment_form #card_number").val();
        creditCard.cardCVV = $("#payment_form #card_cvv").val();

        //Validate credit card fields
        var fieldErrors = creditCard.fieldErrors();

        $(".payment_data .pagar_me_error_message").remove();
        var hasErrors = false;
        for (var field in fieldErrors) {
          errorFields(fieldErrors[field]);
          hasErrors = true;
        }

        if (hasErrors) {
            $('#button-confirm').button('reset');
            scrollToError();

            return false;
        } else {
            $('#button-confirm').button('loading');

            creditCard.generateHash(function (cardHash) {

                form.append($('<input type="hidden" id="card_hash" name="card_hash">').val(cardHash));

                transactionData = {};

                transactionData.amount = '<?php echo $total; ?>';
                transactionData.card_hash = $("#card_hash").val();
                transactionData.installments = $("#installments").val();
                transactionData.bandeira = $("#bandeira").val();
                if($("#customer_document_number")) {
                    transactionData.document_number = $("#customer_document_number").val();
                }

                $.ajax({
                    type: 'POST',
                    url: 'index.php?route=payment/pagar_me_cartao/payment',
                    dataType: 'json',
                    data: transactionData,
                    success: function (response) {
                        if (response.hasOwnProperty('error')){
                          errorFields(response.error);
                          scrollToError();

                          return false;
                        } else {
                           location = '<?php echo $url; ?>';
                        }
                    }
                });
            });
        }
    });


    </script>
</script>

