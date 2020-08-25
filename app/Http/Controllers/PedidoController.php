<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PagseguroService;
use App\Http\Requests\BoletoRequest;
use App\Http\Requests\CartaoRequest;
use Illuminate\Support\Facades\Storage;

class PedidoController extends Controller
{
    protected $pagseguroService;

    public function __construct(PagseguroService $pagseguroService)
    {
        $this->pagseguroService = $pagseguroService;
    }

    public function exibirBoleto()
    {
        return view('pagamentos.boleto');
    }

    public function exibirCartao()
    {
        try {
            $this->makePagSeguroSession();
            return view('pagamentos.cartao');
        } catch (\Exception $e) {
            session()->forget('pagseguro_session_code');
            redirect()->route('pagamentos.index');
        }
    }

    public function processarBoleto(BoletoRequest $request)
    {
        $dadosPedido = $request->validated();
        $response = $this->pagseguroService->efetuarPagamentoBoleto($dadosPedido);
        $valor = $response->getGrossAmount();
        $id = $response->getCode();
        $link = $response->getPaymentLink();
        dd($link);
    }

    public function processarCartao(CartaoRequest $request)
    {
        $dadosPedido = $request->validated();
        $response = $this->pagseguroService->efetuarPagamentoCartao($dadosPedido);
        // $valor = $response->getGrossAmount();
        // $id = $response->getCode();
        // $link = $response->getPaymentLink();
        dd($response);
    }

    public function receberStatus(Request $request)
    {
        $code = $request->notificationCode;
        $response = $this->pagseguroService->consultaNotificacao();
        $reference = $response->getReference();
        $valor = $response->getGrossAmount();
        $id = $response->getCode();
        $status = $response->getStatus();
        Storage::put('reference', $status." - ".$reference." - ".$valor." - ".$id);
        //Storage::put('request', $request);

	    //$pedido = Pedido::where('uuid', $reference)->firstOrFail();
	    //$pedido->pagseguro_status = $notification->getStatus();
	    //$pedido->save();
    }
}
