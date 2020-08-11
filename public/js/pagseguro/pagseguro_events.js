let cardNumber = document.querySelector('input[name=card_number]');
let spanBrand  = document.querySelector('span.brand');

cardNumber.addEventListener('keyup', function(){
    if(cardNumber.value.length >= 6) {
        PagSeguroDirectPayment.getBrand({
            cardBin: cardNumber.value.substr(0, 6),
            success: function(res) {
                let imgFlag = `<img src="https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/68x30/${res.brand.name}.png">`;
                spanBrand.innerHTML = imgFlag;
                document.querySelector('input[name=card_brand]').value = res.brand.name;

                getInstallments(2999, res.brand.name);
            },
            error: function(err) {
                console.log(err);
            },
        });
    }
});