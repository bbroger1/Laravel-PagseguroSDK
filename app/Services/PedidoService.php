<?php

namespace App\Services;

use Http\Client\Exception;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

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

    function efetuarPagamentoBoleto($dadosPedido)
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
            56273440
        );
        $boleto->setSender()->setDocument()->withParameters(
            'CPF',
            $dadosPedido['cpf']
        );
        //$boleto->setSender()->setHash('3dc25e8a7cb3fd3104e77ae5ad0e7df04621caa33e300b27aeeb9ea1adf1a24f');
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
}
