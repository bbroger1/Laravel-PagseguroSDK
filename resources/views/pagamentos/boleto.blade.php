@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card text-center">
                <div class="card-header">
                    <i class="fas fa-file-invoice-dollar"></i> Boleto
                </div>
                <div class="card-body">
                    <form name="formPagamento" id="formPagamento" action="{{route('pagamento.processamento.boleto')}}"
                        method="post">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-4 form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome"
                                    id="nome" value="{{old('nome')}}">

                                @error('nome')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-4 form-group">
                                <label>CPF</label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf"
                                    id="cpf" value="{{old('cpf')}}">

                                @error('cpf')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-4 form-group">
                                <label>Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="email" value="{{old('email')}}">

                                @error('email')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-4 form-group">
                                <label>CEP</label>
                                <input type="text" class="form-control @error('cep') is-invalid @enderror" name="cep"
                                    id="cep" minlength="8" maxlength="8" value="{{old('cep')}}">

                                @error('cep')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-2 form-group">
                                <label>Sigla</label>
                                <input type="text" class="form-control @error('sigla') is-invalid @enderror" name="sigla"
                                    id="sigla" value="{{old('sigla')}}" readonly>
        
                                @error('sigla')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-3 form-group">
                                <label for="estado">Estado</label>
                                <input id="estado" type="text"
                                    class="form-control endereço @error('estado') is-invalid @enderror" name="estado"
                                    value="{{ old('estado') }}" readonly>

                                @error('estado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-3 form-group">
                                <label for="cidade">Cidade</label>
                                <input id="cidade" type="text"
                                    class="form-control endereço @error('cidade') is-invalid @enderror" name="cidade"
                                    value="{{ old('cidade') }}" readonly>

                                @error('cidade')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-4 form-group">
                                <label for="bairro">Bairro</label>
                                <input id="bairro" type="text"
                                    class="form-control endereço @error('bairro') is-invalid @enderror" name="bairro"
                                    value="{{ old('bairro') }}" readonly>

                                @error('bairro')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-4 form-group">
                                <label for="rua">Rua</label>
                                <input id="rua" type="text"
                                    class="form-control endereço @error('rua') is-invalid @enderror" name="rua"
                                    value="{{ old('rua') }}" readonly>

                                @error('rua')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-2 form-group">
                                <label for="numero">Número</label>
                                <input id="numero" type="text"
                                    class="form-control @error('numero') is-invalid @enderror" name="numero"
                                    value="{{ old('numero') }}">

                                @error('numero')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            {{-- <div class="col-2 form-group">
                                <label for="complemento">Complemento</label>
                                <input id="complemento" type="text"
                                    class="form-control @error('complemento') is-invalid @enderror" name="complemento"
                                    value="{{ old('complemento') }}">

                                @error('complemento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div> --}}
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-4 form-group">
                                <input type="text" class="form-control" name="hash" id="hash"
                                    readonly="true">
                                <button id="submit" name="submit" class="form-control btn btn-primary btn-lg"
                                    dusk="confirmar-button">Confirmar
                                    Pagamento</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
<script>
    $(function () {
        $('#cpf').mask('000.000.000-00');
        $("#cep").mask("00000-000");
        const ConverterEstados = function(val) {
            var data;
            switch (val.toUpperCase()) {
                case "AC" : data = "Acre";                  break;
                case "AL" : data = "Alagoas";               break;
                case "AM" : data = "Amazonas";              break;
                case "AP" : data = "Amapa";                 break;
                case "BA" : data = "Bahia";                 break;
                case "CE" : data = "Ceara";                 break;
                case "DF" : data = "Distrito Federal";      break;
                case "ES" : data = "Espirito Santo";        break;
                case "GO" : data = "Goias";                 break;
                case "MA" : data = "Maranhao";              break;
                case "MG" : data = "Minas Gerais";          break;
                case "MS" : data = "Mato Grosso do Sul";    break;
                case "MT" : data = "Mato Grosso";           break;
                case "PA" : data = "Para";                  break;
                case "PB" : data = "Paraiba";               break;
                case "PE" : data = "Pernambuco";            break;
                case "PI" : data = "Piaui";                 break;
                case "PR" : data = "Parana";                break;
                case "RJ" : data = "Rio de Janeiro";        break;
                case "RN" : data = "Rio Grande do Norte";   break;
                case "RO" : data = "Rondonia";              break;
                case "RR" : data = "Roraima";               break;
                case "RS" : data = "Rio Grande do Sul";     break;
                case "SC" : data = "Santa Catarina";        break;
                case "SE" : data = "Sergipe";               break;
                case "SP" : data = "São Paulo";             break;
                case "TO" : data = "Tocantins";             break;
            }
            return data;
        };
        function limparEndereço() {
            $(".endereço").val("");
        }
        function limparCep() {
            $("#cep").removeClass('is-valid');
            $("#cep").removeClass('is-invalid');
        }
        function cepValido() {
            $("#cep").removeClass('is-invalid');
            $("#cep").addClass('is-valid');
        }
        function cepInvalido() {
            $("#cep").removeClass('is-valid');
            $("#cep").addClass('is-invalid');
        }
        $("#cep").keyup(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep != "") {
                var validacep = /^[0-9]{8}$/;
                if(validacep.test(cep)) {
                    $.get('/cep/' + cep, function (dados) {
                        if (!("erro" in dados)) {
                            cepValido();
                            $("#sigla").val(dados.uf);
                            $("#estado").val(ConverterEstados(dados.uf));
                            $("#cidade").val(dados.localidade);
                            $("#bairro").val(dados.bairro);
                            $("#rua").val(dados.logradouro);      
                        } else {
                            cepInvalido();
                            limparEndereço();
                        }
                    });
                } else {
                    limparCep();
                    limparEndereço();
                }
            }
        });
        $("#formPagamento").submit(function() {
            $("#cpf").unmask();
            $("#cep").unmask();
            $("#hash").val(PagSeguroDirectPayment.getSenderHash());
        });
    });
</script>
@endsection