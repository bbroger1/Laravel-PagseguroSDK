<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use App\Http\Requests\BoletoRequest;
use App\Http\Requests\CartaoRequest;

class PedidoController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function exibirBoleto()
    {
        return view('pagamentos.boleto');
    }

    public function exibirCartao()
    {
        $this->makePagSeguroSession();
        return view('pagamentos.cartao');
    }

    public function processarBoleto(BoletoRequest $request)
    {
        $dadosPedido = $request->validated();
        $response = $this->pedidoService->efetuarPagamentoBoleto($dadosPedido);
        $valor = $response->getGrossAmount();
        $id = $response->getCode();
        $link = $response->getPaymentLink();
        dd($link);
    }

    public function processarCartao(CartaoRequest $request)
    {
        $dadosPedido = $request->validated();
        $response = $this->pedidoService->efetuarPagamentoCartao($dadosPedido);
        // $valor = $response->getGrossAmount();
        // $id = $response->getCode();
        // $link = $response->getPaymentLink();
        dd($response);
    }

    public function makePagSeguroSession()
    {
        if(!session()->has('pagseguro_session_code')) {
			$sessionCode = \PagSeguro\Services\Session::create(
				\PagSeguro\Configuration\Configure::getAccountCredentials()
			);
			return session()->put('pagseguro_session_code', $sessionCode->getResult());
		}
    }

    public function checkout(Request $request)
    {
        $payment = new \PagSeguro\Domains\Requests\Payment();
        $payment->addItems()->withParameters(
            $request->get('itemId1'),
            $request->get('itemDescription1'),
            $request->get('itemAmount1'),
            $request->get('itemPrice1')
        );
        $payment->setCurrency("BRL");
        $payment->setReference("LIBPHP000001");
        try {
            $onlyCheckoutCode = true;
            $result = $payment->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials(),
                $onlyCheckoutCode
            );
            $code = $result->getCode();
            return response()->json($code, 201);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
