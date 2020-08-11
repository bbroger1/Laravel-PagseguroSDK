@extends('layouts.app')

@section('content')
    <div id="pagamento-lancamento">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card text-center">
                        <div class="row">
                            <div class="col-12 msg"></div>

                            <div class="col-12">
                                <p><i class="fas fa-credit-card"></i> Cartão de Crédito</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form name="formPagamento" id="formPagamento"
                                action="{{ route('pagamento.processamento.cartao') }}" method="post">
                                @csrf

                                <div class="row justify-content-center">
                                    <div class="col-9 form-group">
                                        <label>Nome no Cartão</label>
                                        <input type="text" class="form-control" name="card_holder" id="card_holder">
                                    </div>

                                    <div class="col-9 form-group">
                                        <label>Número do Cartão <span class="brand"></span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="card_number" id="card_number"
                                                maxlength="16">
                                            <input type="text" name="card_brand" id="card_brand">
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label>Mês de Expiração</label>
                                        <select class="form-control" name="card_month" id="card_month" required>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}">
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-3 form-group">
                                        <label>Ano de Expiração</label>
                                        <select class="form-control" name="card_year" id="card_year" required>
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option value="{{ now()->year + $i }}">{{ now()->year + $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-3 form-group">
                                        <label>CVV</label>
                                        <input type="text" class="form-control @error('card_cvv') is-invalid @enderror"
                                            name="card_cvv" id="card_cvv">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-5 form-group">
                                        <label>Valor total</label>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label>Quantidade de parcelas</label>
                                        <select class="form-control select_installments" name="parcelas" id="parcelas">
                                        </select>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-4 form-group">
                                        <input type="text" class="form-control" name="encryptedCard" id="encryptedCard">
                                        <input type="text" class="form-control" name="hash" id="hash">
                                        <button type="button" id="enviar" name="enviar" class="form-control btn btn-primary btn-lg">Confirmar
                                            Pagamento</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

    <script>
        $(function() {
            const sessionId = '{{session('pagseguro_session_code')}}';
            const amountTransaction = 2999.00;
            PagSeguroDirectPayment.setSessionId(sessionId);

            $("#enviar").click(function(e) {
                //e.preventDefault();
                criptografar();
                $("#hash").val(PagSeguroDirectPayment.getSenderHash());
                setTimeout( function () { 
                    $("#formPagamento").submit();
                }, 3000);
            });
        });

    </script>

    <script src="{{ asset('js/pagseguro/pagseguro_functions.js') }}"></script>
    <script src="{{ asset('js/pagseguro/pagseguro_events.js') }}"></script>
@endsection
