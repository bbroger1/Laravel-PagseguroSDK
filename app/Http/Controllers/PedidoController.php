<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use App\Http\Requests\BoletoRequest;
use App\Http\Requests\CartaoRequest;
use Illuminate\Support\Facades\Storage;

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
        try {
            $sessionCode = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            session()->put('pagseguro_session_code', $sessionCode->getResult());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function receberStatus(Request $request)
    {
        $code = $request->notificationCode;
        Storage::put('code', $code);
        $notification = $this->pedidoService->consultaNotificacao();
        $reference = base64_decode($notification->getReference());
        Storage::put('reference', $reference);
        //Storage::put('request', $request);

	    //$pedido = Pedido::where('uuid', $reference)->firstOrFail();
	    //$pedido->pagseguro_status = $notification->getStatus();
	    //$pedido->save();
    }
}
