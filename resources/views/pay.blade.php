@extends('layouts.app')
@section('title', 'Pague @' . $stream->username . ' com PicPay')
@section('content')
@auth
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body">
                    <form id="picpay">
                        <input name="streamer_id" type="hidden" value="{{ $stream->id }}">
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="name">Nome:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-user fa-fw"></i></div>
                                        </div>
                                        <input name="name" id="name" type="text" class="form-control" placeholder="Seu Nome" maxlength="25" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="surname">Sobrenome:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-user fa-fw"></i></div>
                                        </div>
                                        <input name="surname" id="surname" class="form-control" type="text" placeholder="Seu Sobrenome" maxlength="25" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="cpf">CPF:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-id-card fa-fw"></i></div>
                                        </div>
                                        <input name="cpf" id="cpf" class="form-control" type="text" placeholder="Seu CPF" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="phone">Celular:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-mobile-alt fa-fw"></i></div>
                                        </div>
                                        <input name="phone" id="phone" class="form-control" type="text" placeholder="+55" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="email">E-mail:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-envelope fa-fw"></i></div>
                                        </div>
                                        <input name="email" id="email" class="form-control" type="text" placeholder="seu@email.com" value="{{ Auth::user()->email }}"  maxlength="155" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="amount">Valor:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-dollar-sign fa-fw"></i></div>
                                        </div>
                                        <input name="amount" id="amount" class="form-control" type="text" placeholder="3.33" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Mensagem: 
                            <div class="badge badge-success" id="chars"><span>0</span>/255 caracteres</div></label>
                            <textarea name="message" id="message" class="form-control" maxlength="255" row="2" placeholder="Sua mensagem aqui..."></textarea>
                        </div>
                        <button class="btn btn-lg btn-block btn-success btn-pay"><i class="fas fa-lock fa-fw"></i> Pagar {{ $stream->username }} com PicPay</button>
                    </form>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body text-center">
                    <p><small>Ao clicar em <strong>"Pagar com PicPay"</strong>, um código será exibido. Para pagar, basta escanear o código com seu <img src="{{ url('images/picpay-logo.png') }}" style="height: 18px;">.</small></p>
                </div>
            </div>
            <div class="card mt-1">
                <div class="card-body text-center">
                    <p><small>Seus dados pessoais nunca são armazenados, eles são utilizados somente para a integração com o <img src="{{ url('images/picpay-logo.png') }}" style="height: 18px;">.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="payment" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h4 class="modal-title" id="paymentModal">Pague com <img src="{{ url('images/picpay-logo.png') }}" style="height: 26px;"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="about:blank" frameborder="0" scrolling="no" class="w-100 h-100"></iframe>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    $('#payment').on('hide.bs.modal', function (e) {
        window.location.href = '{{ route("payment-history") }}';
    });
    $(document).ready(function () {
        $('#name, #surname').inputmask({
            casing: 'title'
        });
        $('#cpf').inputmask('999.999.999-99');
        $('#phone').inputmask('+55 99 99999-9999');
        $('#email').inputmask('email');
        $('#amount').inputmask('currency', {
            prefix: 'R$ ',
            rightAlign: false,
            numericInput: true,
            radixPoint: ".",
            groupSeparator: '',
            autoGroup: true,
            autoUnmask: true
        });
        $('#message').keyup(function () {
            $('#chars span').text($(this).val().length);
        });
        $('.btn-pay').click(function (evt) {
            evt.preventDefault();
            let error = false;
            $('input:required').each(function (i, el) {
                if ($(this).val() === '' || $(this).val() === undefined) {
                    error = true;
                    return $(this).focus();
                }
            });
            if ($('#amount').inputmask('unmaskedvalue') > 1000) {
                error = true;
                new Noty({
                    text: 'O valor máximo é de <strong>R$ 1000.00</strong>',
                    type: 'error'
                }).show();
                return $('#amount').focus().val('');
            }
            if ($('#amount').inputmask('unmaskedvalue') < 1) {
                error = true;
                new Noty({
                    text: 'O valor minímo é de <strong>R$ 1.00</strong>',
                    type: 'error'
                }).show();
                return $('#amount').focus().val('');
            }
            if (error) return;
            $.ajax({
                url: '{{ route("make-payment") }}',
                cache: false,
                method: 'POST',
                data: $('#picpay').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (response) {
                if (response.payment_url !== undefined) {
                    $('#payment iframe').attr('src', response.payment_url);
                    $('#payment').modal('toggle');
                }
                $('#picpay')[0].reset();
            });
        });
    });
</script>
@endsection
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mt-5">
            <a class="btn btn-lg text-white my-5 rounded-0" style="background-color: #9146ff;" href="{{ route('login') }}">
                <i class="fab fa-twitch fa-fw"></i> Conectar com Twitch
            </a>
            <p class="mt-5"><small>Pague {{ $stream->username }} com <img src="{{ url('images/picpay-logo.png') }}" style="height: 26px;"></small></p>
        </div>
    </div>
</div>
@endauth
@endsection
