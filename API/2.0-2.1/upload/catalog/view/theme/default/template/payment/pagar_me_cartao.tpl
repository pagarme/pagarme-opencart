<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<script src="catalog/view/javascript/jquery.mask.min.js"/>
<script src="catalog/view/javascript/jquery.creditCardValidator.js"/>

<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="dados_cartao">
    <form id="payment_form" method="POST" class="form-horizontal">
        <input type="hidden" name="totalValue" id="totalValue" value="<?php echo $total; ?>">
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
        <div class="row">
            <div class="col-xs-12 col-md-7">
                <label for="card_number">Número do cartão</label>
                <input class="form-control" type="text" id="card_number"/>
            </div>
            <div class="col-xs-12 col-md-5">
                <label for="card_cvv" id="label-cvv">CVV <span id="tool-tip-cvv"><i
                            class="fa fa-question-circle"></i> <span
                            id="tool-tip-content"><img src="catalog/view/theme/default/image/bancos/cartao-cvv.png"
                                                       alt=""></span></span></label>
                <input type="text" id="card_cvv" size="4" placeholder="CVV" maxlength="4"
                       class="so_numeros form-control"/>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <label>Validade do cartão</label>
            </div>
            <div class="col-xs-6">
                <input type="text" class="so_numeros form-control" placeholder="MM" maxlength="2" size="2"
                       id="card_expiration_month">

            </div>
            <div class="col-xs-6">
                <input type="text" class="so_numeros form-control" maxlength="4" placeholder="AAAA" size="4"
                       id="card_expiration_year">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <label for="card_holder_name">Nome impresso no cartão</label>
                <input type="text" id="card_holder_name" value="" class="form-control"/>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <label for="cpf_customer">CPF/CNPJ (somente números)</label>
                <input type="text" id="cpf_customer" value="" class="form-control"/>
            </div>
        </div>
        <div class="row">
            <div id="installmentsWrapper">
                <div class="col-xs-12">
                    <label for="installmentQuantity">Parcelamento</label>
                    <select name="installments" id="installments" class="form-control">
                        <?php foreach ($parcelas['installments'] as $parcela): ?>
                            <option value="<?php echo $parcela['installment'] ?>"><?php echo $parcela['installment'] ?>x
                                de
                                R$ <?php echo substr_replace((string)$parcela['installment_amount'], ',', -2, 0); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="buttons">
    <div class="text-center">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary"
               data-loading-text="Processando..."/>
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
        $("#card_number").mask("0000000000000000999999", {clearIfNotMatch: true});

        $("#card_cvv").mask("0009", {clearIfNotMatch: true});

        $("#card_expiration_month").mask("00", {clearIfNotMatch: true});

        $("#card_expiration_year").mask("0000", {clearIfNotMatch: true});

    });

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

    $('#button-confirm').bind('click', function () {

        /*Função Pagar.me*/
        PagarMe.encryption_key = "<?php echo $pagar_me_cartao_criptografia; ?>";

        var form = $("#payment_form");

        //form.submit(function (event) { // quando o form for enviado...
        // inicializa um objeto de cartão de crédito e completa
        // com os dados do form
        var creditCard = new PagarMe.creditCard();
        creditCard.cardHolderName = $("#payment_form #card_holder_name").val();
        creditCard.cardExpirationMonth = $("#payment_form #card_expiration_month").val();
        creditCard.cardExpirationYear = $("#payment_form #card_expiration_year").val();
        creditCard.cardNumber = $("#payment_form #card_number").val();
        creditCard.cardCVV = $("#payment_form #card_cvv").val();

        // pega os erros de validação nos campos do form
        var fieldErrors = creditCard.fieldErrors();

        //Verifica se há erros
        var hasErrors = false;
        for (var field in fieldErrors) {
            hasErrors = true;
            break;
        }

        if (hasErrors) {
            // realiza o tratamento de errors
            alert("Verifique se os dados informados estão corretos. Qualquer problema entre em contato com a loja.");
            $('#button-confirm').button('reset');
            return false;
        } else {
            $('#button-confirm').button('loading');
            //console.log("oi")
            // se não há erros, gera o card_hash...
            creditCard.generateHash(function (cardHash) {
                // ...coloca-o no form...
                form.append($('<input type="hidden" id="card_hash" name="card_hash">').val(cardHash));
                // e envia o form
                $.ajax({
                    type: 'POST',
                    url: 'index.php?route=payment/pagar_me_cartao/payment',
                    dataType: 'json',
                    data: {
                        amount: $("#totalValue").val(),
                        card_hash: $("#card_hash").val(),
                        installments: $("#installments").val(),
                        bandeira: $("#bandeira").val(),
                        cpf_customer: $("#cpf_customer").val()
                    },
                    success: function (response) {
                        if (response['error']) {
                            alert('Ocorreu um erro inesperado. Por favor contate a loja.')
                        } else if (response['success']) {
                            // alert(response['success']);
                            //$.colorbox({href: response['success']});
                            //window.open(response['success']);
                            location = '<?php echo $url; ?>';
                        } else {
                            location = '<?php echo $url2; ?>';
                        }
                    }
                });
            });
        }
    });
    //--></script>
</script>

