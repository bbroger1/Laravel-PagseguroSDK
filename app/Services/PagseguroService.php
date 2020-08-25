<?php

namespace App\Services;

use Http\Client\Exception;

class PagseguroService
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

    public function criarSession()
    {
        try {
            $sessionCode = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            session()->put('pagseguro_session_code', $sessionCode->getResult());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function efetuarPagamentoBoleto($dadosPedido)
    {
        $boleto = new \PagSeguro\Domains\Requests\DirectPayment\Boleto();
		$boleto->setMode('DEFAULT');
		$boleto->setCurrency("BRL");
        $boleto->addItems()->withParameters(
            'id',
            'item',
            1,
            25.00
        );
        $boleto->setReference(Uuid::uuid4()->toString());
        $boleto->setSender()->setName(auth()->user()->nome_completo);
        $boleto->setSender()->setEmail('c89975035603679169701@sandbox.pagseguro.com.br');//$dadosPedido['email']
        $boleto->setSender()->setPhone()->withParameters(
            substr(auth()->user()->celular, 0, 2),
            substr(auth()->user()->celular, 2)
        );
        $boleto->setSender()->setDocument()->withParameters(
            'CPF',
            auth()->user()->cpf
        );
        //$boleto->setSender()->setHash($dadosPedido['hash']); //para production
        $boleto->setShipping()->setAddressRequired()->withParameters('FALSE');

        try {
            $response = $boleto->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
			);
			return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
		}
    }

    public function efetuarPagamentoCartao($dadosPedido)
    {
        $creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
        $creditCard->setReceiverEmail(config('pagseguro.sandbox.email'));
        $creditCard->setReference(Uuid::uuid4()->toString());
        $creditCard->setCurrency("BRL");
        $creditCard->addItems()->withParameters(
            session('product.reference'),
            session('product.tipo'),
            1,
            session('product.valor')
        );
        $creditCard->setSender()->setName($dadosPedido['card_holder']);
        $creditCard->setSender()->setEmail('c89975035603679169701@sandbox.pagseguro.com.br'); //auth()->user()->email
        $creditCard->setSender()->setPhone()->withParameters(
            substr(auth()->user()->celular, 0, 2),
            substr(auth()->user()->celular, 2)
        );
        $creditCard->setSender()->setDocument()->withParameters(
            'CPF',
            $dadosPedido['cpf']
        );
        //$creditCard->setSender()->setHash($dadosPedido['hash']); //só pra production
        $creditCard->setShipping()->setAddressRequired()->withParameters('FALSE');
        // $creditCard->setShipping()->setAddress()->withParameters(
        //     auth()->user()->rua,
        //     auth()->user()->número,
        //     auth()->user()->bairro,
        //     auth()->user()->cep,
        //     auth()->user()->cidade,
        //     auth()->user()->estado,
        //     'BRA',
        //     auth()->user()->complemento
        // );

        $creditCard->setBilling()->setAddress()->withParameters(
            $dadosPedido['rua'],
            $dadosPedido['numero'],
            $dadosPedido['bairro'],
            $dadosPedido['cep'],
            $dadosPedido['cidade'],
            $dadosPedido['sigla'],
            'BRA',
            $dadosPedido['complemento'] ?? null
        );

        $creditCard->setToken($dadosPedido['encryptedCard']);

        list($quantity, $installmentAmount) = explode('|', $dadosPedido['parcelas']);
        $installmentAmount = number_format($installmentAmount, 2, '.', '');

        $creditCard->setInstallment()->withParameters($quantity, $installmentAmount);
        $creditCard->setHolder()->setName($dadosPedido['card_holder']);
        $creditCard->setHolder()->setPhone()->withParameters(
            substr(auth()->user()->celular, 0, 2),
            substr(auth()->user()->celular, 2)
        );
        $creditCard->setHolder()->setDocument()->withParameters(
            'CPF',
            $dadosPedido['cpf']
        );
        $creditCard->setMode('DEFAULT');

        try {
            $response = $creditCard->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function efetuarReembolso($pedido)
    {
        try {
            $response = \PagSeguro\Services\Transactions\Refund::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials(),
                $pedido->pagseguro_code
            );
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function efetuarCancelamento($pedido)
    {
        try {
            $response = \PagSeguro\Services\Transactions\Cancel::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials(),
                $pedido->pagseguro_code
            );
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function consultarNotificacao()
    {
        try {
            if (\PagSeguro\Helpers\Xhr::hasPost()) {
                $response = \PagSeguro\Services\Transactions\Notification::check(
                    \PagSeguro\Configuration\Configure::getAccountCredentials()
                );
                return $response;
            } else {
                throw new \InvalidArgumentException($_POST);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
