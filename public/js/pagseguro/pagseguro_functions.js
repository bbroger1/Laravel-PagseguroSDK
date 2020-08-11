function criptografar() {
    //const PagSeguroPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr+ZqgD892U9/HXsa7XqBZUayPquAfh9xx4iwUbTSUAvTlmiXFQNTp0Bvt/5vK2FhMj39qSv1zi2OuBjvW38q1E374nzx6NNBL5JosV0+SDINTlCG0cmigHuBOyWzYmjgca+mtQu4WczCaApNaSuVqgb8u7Bd9GCOL4YJotvV5+81frlSwQXralhwRzGhj/A57CGPgGKiuPT+AOGmykIGEZsSD9RKkyoKIoc0OS8CPIzdBOtTQCIwrLn2FxI83Clcg55W8gkFSOS6rWNbG5qFZWMll6yl02HtunalHmUlRUL66YeGXdMDC2PuRcmZbGO5a/2tbVppW6mfSWG3NPRpgwIDAQAB';

    PagSeguroDirectPayment.createCardToken({
        cardNumber: $('#card_number').val(),
        brand:      $('#card_brand').val(),
        cvv:        $('#card_cvv').val(),
        expirationMonth: $('#card_month').val(),
        expirationYear:  $('#card_year').val(),
        success: function(res) {
            //return res.card.token;
            console.log(res.card.token);
        },
        error: function(err) {
            for(let i in err.errors) {
                console.log(errorsMapPagseguroJS(i));
            }
        }
    });
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
        case "10000":
            return 'Bandeira do cartão inválida!';
        break;

        case "10001":
            return 'Número do Cartão com tamanho inválido!';
        break;

        case "10002":
        case  "30405":
            return 'Data com formato inválido!';
        break;

        case "10003":
            return 'Código de segurança inválido';
        break;

        case "10004":
            return 'Código de segurança é obrigatório!';
        break;

        case "10006":
            return 'Tamanho do código de segurança inválido!';
        break;

        default:
            return 'Houve um erro na validação do seu cartão de crédito!';
    }
}