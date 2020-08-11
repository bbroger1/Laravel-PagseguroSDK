<?php

namespace App\Services;

use Http\Client\Exception;

class PedidoService
{
    private $environment;

    public function __construct()
    {
        $this->environment = config('pagseguro.env');
        \PagSeguro\Library::initialize();
        \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
        \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
        \PagSeguro\Configuration\Configure::setEnvironment($this->environment);
        \PagSeguro\Configuration\Configure::setLog(true, storage_path('pagseguro.txt'));

        if ($this->environment == 'sandbox') {
            \PagSeguro\Configuration\Configure::setAccountCredentials(
                config('pagseguro.sandbox.email'),
                config('pagseguro.sandbox.token')
            );
        } else {
            \PagSeguro\Configuration\Configure::setAccountCredentials(
                config('pagseguro.production.email'),
                config('pagseguro.production.token')
            );
        }
    }

    public function efetuarPagamentoBoleto($dadosPedido)
    {
        $boleto = new \PagSeguro\Domains\Requests\DirectPayment\Boleto();
        $boleto->setMode('DEFAULT');
        $boleto->setCurrency("BRL");
        $boleto->addItems()->withParameters(
            '0001',
            'Notebook prata',
            1,
            130.00
        );
        $boleto->setReference("LIBPHP000001-boleto");
        //$boleto->setExtraAmount(11.5);
        $boleto->setSender()->setName($dadosPedido['nome']);
        $boleto->setSender()->setEmail('c89975035603679169701@sandbox.pagseguro.com.br');
        $boleto->setSender()->setPhone()->withParameters(
            11,
            956273440
        );
        $boleto->setSender()->setDocument()->withParameters(
            'CPF',
            $dadosPedido['cpf']
        );
        //$boleto->setSender()->setHash($dadosPedido['hash']); //só pra production
        //$boleto->setSender()->setIp('127.0.0.0');
        /*$boleto->setShipping()->setAddress()->withParameters(
        $dadosPedido['rua'],
        $dadosPedido['numero'],
        $dadosPedido['bairro'],
        $dadosPedido['cep'],
        $dadosPedido['cidade'],
        $dadosPedido['sigla'],
        'BRA',
        $dadosPedido['complemento']
        );*/
        $boleto->setShipping()->setAddressRequired()->withParameters('FALSE');

        try {
            $response = $boleto->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            return $response;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function efetuarPagamentoCartao($dadosPedido)
    {
        $creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
        $creditCard->setReceiverEmail('vendedor@lojamodelo.com.br');
        $creditCard->setReference("LIBPHP000001");
        $creditCard->setCurrency("BRL");
        $creditCard->addItems()->withParameters(
            '0001',
            'Notebook prata',
            2,
            10.00
        );
        $boleto->setSender()->setName($dadosPedido['nome']);
        $boleto->setSender()->setEmail('c89975035603679169701@sandbox.pagseguro.com.br');
        $creditCard->setSender()->setPhone()->withParameters(
            11,
            56273440
        );
        $creditCard->setSender()->setDocument()->withParameters(
            'CPF',
            '45320334893'
        );
        //$creditCard->setSender()->setHash($dadosPedido['hash']); //só pra production

        //$creditCard->setSender()->setIp('127.0.0.0');
        // $creditCard->setShipping()->setAddress()->withParameters(
        //     'Av. Brig. Faria Lima',
        //     '1384',
        //     'Jardim Paulistano',
        //     '01452002',
        //     'São Paulo',
        //     'SP',
        //     'BRA',
        //     'apto. 114'
        // );

        // $creditCard->setBilling()->setAddress()->withParameters(
        //     'Av. Brig. Faria Lima',
        //     '1384',
        //     'Jardim Paulistano',
        //     '01452002',
        //     'São Paulo',
        //     'SP',
        //     'BRA',
        //     'apto. 114'
        // );

        $creditCard->setToken($dadosPedido['encryptedCard']);

        list($quantity, $installmentAmount) = explode('|', $this->cardInfo['installment']);
        $installmentAmount = number_format($installmentAmount, 2, '.', '');

        $creditCard->setInstallment()->withParameters($quantity, $installmentAmount);
        $creditCard->setHolder()->setBirthdate('01/10/1979');
        $creditCard->setHolder()->setName('João Comprador'); // Equals in Credit Card
        $creditCard->setHolder()->setPhone()->withParameters(
            11,
            56273440
        );
        $creditCard->setHolder()->setDocument()->withParameters(
            'CPF',
            'insira um numero de CPF valido'
        );
        $creditCard->setMode('DEFAULT');

        try {
            $response = $creditCard->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            return $response;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
