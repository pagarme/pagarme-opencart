<script src="https://assets.pagar.me/checkout/1.1.0/checkout.js"></script>
<style>
    .payment-information{text-align:center;font-size:18px;font-weight:800;letter-spacing:-0.55pt;margin-bottom:10px;}
</style>
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $texto_botao; ?>" id="button-confirm" class="<?php echo $button_css_class; ?>"
               data-loading-text="Aguarde..."/>
    </div>
</div>

<script>

    $('#button-confirm').on('click', function (e) {

        $.ajax({
            url: 'index.php?route=extension/payment/pagar_me_checkout/submit',
            dataType: 'json',
            success: function (response) {
                // INICIAR A INSTÂNCIA DO CHECKOUT
                // declarando um callback de sucesso
                console.log(response['checkoutProperties']['postback_url'])
                var checkout = new PagarMeCheckout.Checkout({
                    'customer_data': false,
                    'encryption_key': '<?php echo $encryption_key; ?>', success: function (data) {
                        var input_form = $('<input>').attr({
                            type: 'hidden',
                            name: 'token',
                            value: data.token
                        });
                        var form = $('<form>').attr({
                            action: '<?php echo $url; ?>',
                            id: 'form-pagarme',
                            name: 'form-pagarme',
                            method: 'POST'
                        });
                        form.append(input_form);
                        $('body').append(form);
                        form.submit();
                    }
                });

                // DEFINIR AS OPÇÕES
                // e abrir o modal
                var params = {
                    'buttonText': response['checkoutProperties']['button_text'],
                    'amount': response['checkoutProperties']['amount'],
                    'buttonClass': response['checkoutProperties']['button_class'],
                    'paymentMethods': response['checkoutProperties']['payment_methods'],
                    'cardBrands': response['checkoutProperties']['card_brands'],
                    'maxInstallments': response['checkoutProperties']['max_installments'],
                    'freeInstallments': response['checkoutProperties']['free_installments'],
                    'uiColor': response['checkoutProperties']['ui_color'],
                    customer: response['customer'],
                    items : response['items'],
                    shipping: response['shipping'],
                    billing: response['billing'],
                    'interestRate': response['checkoutProperties']['interest_rate'],
                    'boletoDiscountAmount': response['checkoutProperties']['boleto_discount_amount']
                };
                console.log(params);
                
                checkout.open(params);
            },
            error: function (xhr, error) {
                console.debug(xhr);
                console.debug(error);
            }
        });
    });

</script>