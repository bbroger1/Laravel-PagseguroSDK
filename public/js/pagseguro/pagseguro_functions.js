function limpar() {
    $('#parcelas option').each(function() {
        $(this).remove();
    });
    $('span.brand').html("");
    $('#card_brand').val("");
}

function criptografar() {
    PagSeguroDirectPayment.createCardToken({
        cardNumber:         $('#card_number').val(),
        brand:              $('#card_brand').val(),
        cvv:                $('#card_cvv').val(),
        expirationMonth:    $('#card_month').val(),
        expirationYear:     $('#card_year').val(),
        success: function(res) {
            console.log(res.card.token);
            $('#encryptedCard').val(res.card.token);
        },
        error: function(err) {
            for(let i in err.errors) {
                console.log(errorsMapPagseguroJS(i));
                $('#mensagem_erro').html(showErrorMessages(errorsMapPagseguroJS(i)));
            }
        }
    });
}

function getBrand(amount) {
    PagSeguroDirectPayment.getBrand({
        cardBin: $('#card_number').val().substr(0, 6),
        success: function(res) {
            let imgFlag = `<img src="https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/68x30/${res.brand.name}.png">`;
            $('span.brand').html(imgFlag);
            $('#card_brand').val(res.brand.name);

            getInstallments(amount, res.brand.name);
        },
        error: function(err) {
            console.log(err);
        },
    });
}

// function getBrand() {
//     PagSeguroDirectPayment.getBrand({
//         cardBin: $('#card_number').val().substr(0, 6),
//         success: function(res) {
//             $('#card_brand').val(res.brand.name);
//         },
//         error: function(err) {
//             console.log(err);
//         },
//     });
// }

function getInstallments(amount, brand) {
    PagSeguroDirectPayment.getInstallments({
        amount: amount,
        brand: brand,
        maxInstallmentNoInterest: 0,
        success: function(res) {
            drawSelectInstallments(res.installments[brand]);
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function drawSelectInstallments(installments) {
    $('#parcelas option').each(function() {
            $(this).remove();
    });
    for(let l of installments) {
        $('#parcelas').append($('<option>', {
            value: l.quantity+"|"+l.installmentAmount,
            text: l.quantity+"x de "+l.installmentAmount+" c/juros "+l.totalAmount
        }));
    }
}

function showErrorMessages(message)
{
    return `
        <div class="alert alert-danger">${message}</div>
    `;
}

function errorsMapPagseguroJS(code)
{
    switch(code) {
        case "10000":                 return 'Bandeira do cartão inválida!';              break;
        case "10001":                 return 'Número do Cartão com tamanho inválido!';    break;
        case "10002": case  "30405":  return 'Data com formato inválido!';                break;
        case "10003":                 return 'Código de segurança inválido';              break;
        case "10004":                 return 'Código de segurança é obrigatório!';        break;
        case "10006":                 return 'Tamanho do código de segurança inválido!';  break;
        default:                      return 'Houve um erro na validação do seu cartão de crédito! '+code;
    }
}