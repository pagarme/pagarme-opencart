<form id="form-pagarme" method="POST" action="<?php echo $url; ?>">
<div class="buttons"><div id="button-right" class="right"></div></div>
</form>
<script type="text/javascript">
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://assets.pagar.me/checkout/checkout.js';
        script.setAttribute('data-button-text', "<?php echo $button_text; ?>");
        script.setAttribute('data-encryption-key', "<?php echo $encryption_key; ?>");
        script.setAttribute('data-amount', "<?php echo $amount; ?>");
        script.setAttribute('data-button-class', "<?php echo $button_class; ?>");
        script.setAttribute('data-payment-methods', "<?php echo $payment_methods; ?>");
        script.setAttribute('data-card-brands', "<?php echo $card_brands; ?>");
        script.setAttribute('data-max-installments', "<?php echo $max_installments; ?>");
        script.setAttribute('data-ui-color', "<?php echo $ui_color; ?>");
        script.setAttribute('data-postback-url', "<?php echo $postback_url; ?>");
        script.setAttribute('data-customer-name', "<?php echo $customer_name; ?>");
        script.setAttribute('data-customer-document-number', "<?php echo $customer_document_number; ?>");
        script.setAttribute('data-customer-email', "<?php echo $customer_email; ?>");
        script.setAttribute('data-customer-address-street', "<?php echo $customer_address_street; ?>");
        script.setAttribute('data-customer-address-street-number', "<?php echo $customer_address_street_number; ?>");
        script.setAttribute('data-customer-address-complementary', "<?php echo $customer_address_complementary; ?>");
        script.setAttribute('data-customer-address-neighborhood', "<?php echo $customer_address_neighborhood; ?>");
        script.setAttribute('data-customer-address-city', "<?php echo $customer_address_city; ?>");
        script.setAttribute('data-customer-address-state', "<?php echo $customer_address_state; ?>");
        script.setAttribute('data-customer-address-zipcode', "<?php echo $customer_address_zipcode; ?>");
        script.setAttribute('data-customer-phone-ddd', "<?php echo $customer_phone_ddd; ?>");
        script.setAttribute('data-customer-phone-number', "<?php echo $customer_phone_number; ?>");
        script.setAttribute('data-interest-rate', "<?php echo $interest_rate; ?>");

        document.getElementById('button-right').appendChild(script);


</script>

