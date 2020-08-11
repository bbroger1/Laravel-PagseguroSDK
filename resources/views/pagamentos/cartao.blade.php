@extends('layouts.app')

@section('content')
<div id="pagamento-lancamento">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card text-center">
                    <div class="row">
                        <div class="col-12">
                            
                            <p><i class="fas fa-credit-card"></i> Cartão de Crédito</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form name="formPagamento" id="formPagamento" action="{{route('pagamento.processamento.cartao')}}"
                            method="post">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-9 form-group">
                                    <label>Nome no Cartão</label>
                                    <input type="text" class="form-control" name="card_holder" id="card_holder"
                                        value="{{old('card_holder')}}">
                                </div>

                                <div class="col-9 form-group">
                                    <label>Número do Cartão <span class="brand"></span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="card_number" id="card_number"
                                            maxlength="16" value="{{old('card_number')}}">
                                        <input type="hidden" name="card_brand" id="card_brand">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-3 form-group">
                                    <label>Mês de Expiração</label>
                                    <select class="form-control" name="card_month" id="card_month" required>
                                        @for ($i = 1; $i <= 12; $i++) <option value="{{sprintf('%02d', $i)}}">
                                            {{sprintf('%02d', $i)}}
                                            </option>
                                            @endfor
                                    </select>
                                </div>

                                <div class="col-3 form-group">
                                    <label>Ano de Expiração</label>
                                    <select class="form-control" name="card_year" id="card_year" required>
                                        @for ($i = 0; $i <= 9; $i++) <option value="{{now()->year+$i}}">{{now()->year+$i}}
                                            </option>
                                            @endfor
                                    </select>
                                </div>

                                <div class="col-3 form-group">
                                    <label>CVV</label>
                                    <input type="text" class="form-control @error('card_cvv') is-invalid @enderror" name="card_cvv" id="card_cvv"
                                        value="{{old('card_cvv')}}">

                                    @error('card_cvv')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-5 form-group">
                                    <label>Valor total</label>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label>Quantidade de parcelas</label>
                                    {{-- <select class="form-control" name="parcelas" id="parcelas">
                                        @for ($i = 1; $i <= 10; $i++)
                                            @if($i == 1) 
                                                <option value="{{$i}}">
                                                    {{$i}}x de R${{number_format($curso->valor/$i, 2, ',', '.')}}
                                                </option>
                                            @else
                                                <option value="{{$i}}">
                                                    {{$i}}x de R${{number_format($curso->valor_juros/$i, 2, ',', '.')}} c/juros {{number_format($curso->valor_juros, 2, ',', '.')}}
                                                </option>
                                            @endif
                                        @endfor
                                    </select> --}}
                                    <div class="col-md-12 installments form-group"></div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-4 form-group">
                                    <button id="submit" name="submit" class="form-control btn btn-primary btn-lg" dusk="confirmar-button">Confirmar
                                        Pagamento</button>
                                </div>
                            </div>

                            <input type="text" class="form-control" name="encryptedCard" id="encryptedCard">

                            <input type="text" class="form-control" name="encryptedCard" id="encryptedCard">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- <script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js"></script> --}}
<script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<script>
$(function () {
    //$("#card_number").mask("0000 0000 0000 0000");
    $("#card_cvv").mask("0009");

    const sessionId = '{{session('pagseguro_session_code')}}';
    PagSeguroDirectPayment.setSessionId(sessionId);
    // var encrypted = criptografar();

    $("#formPagamento").submit(function(e){
        e.preventDefault();
        //alert(PagSeguroDirectPayment.setSessionId(sessionId));
        //alert(sessionId);
        // alert(encrypted);
        criptografar();
    });

    // $("#formPagamento").submit(function(e){
    //     var encrypted = criptografar();
    //     if(encrypted != null){
    //         $('#encryptedCard').val(encrypted);
    //         return true;
    //     } else {
    //         $(".spinner").fadeOut();
    //         return false; 
    //     }
    // });
});
</script>

{{-- <script src="{{asset('js/pagseguro/criptografia.js')}}"></script> --}}
<script src="{{asset('js/pagseguro/pagseguro_functions.js')}}"></script>
<script src="{{asset('js/pagseguro/pagseguro_events.js')}}"></script>
@endsection