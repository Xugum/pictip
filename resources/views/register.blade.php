@extends('layouts.app')
@section('title', 'Configuração')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body text-center">
                    <p><small>Para ativar os alertas, preencha todos os campos abaixo.</small></p>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <form id="streamer">
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="picpay_token">x-picpay-token:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-key fa-fw"></i></div>
                                        </div>
                                        <input value="{{ $stream->picpay_token ?? '' }}" name="picpay_token" id="picpay_token" type="text" class="form-control" maxlength="50" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="seller_token">x-seller-token:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-key fa-fw"></i></div>
                                        </div>
                                        <input value="{{ $stream->seller_token ?? '' }}" name="seller_token" id="seller_token" class="form-control" type="text" maxlength="50" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="se_jwt">
                                        <a href="https://streamelements.com/dashboard/account/channels" target="_blank" class="text-success">StreamElements</a> JWT Token:</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-id-card fa-fw"></i></div>
                                        </div>
                                        <textarea name="se_jwt" id="se_jwt" class="form-control" rows="5">{{ $stream->se_jwt ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-lg btn-block btn-warning btn-test"><i class="fas fa-vial fa-fw"></i> Testar Alerta</button>
                        <button class="btn btn-lg btn-block btn-success btn-save"><i class="fas fa-save fa-fw"></i> Salvar</button>
                    </form>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body text-center">
                    <p><small>O <strong>x-picpay-token</strong> e <strong>x-seller-token</strong> são tokens fornecidos pelo PicPay. Se ainda não tem, registre-se em <a href="https://ecommerce.picpay.com/register/" target="_blank">https://ecommerce.picpay.com/register/</a>.</small></p>
                </div>
            </div>
            <div class="card mt-1">
                <div class="card-body text-center">
                    <p><small>O <strong>JWT Token</strong> do StreamElements pode ser localizado na página <a href="https://streamelements.com/dashboard/account/channels" target="_blank">https://streamelements.com/dashboard/account/channels</a>, clicando em <i>Show secrets/Mostrar segredos</i>.</small></p>
                </div>
            </div>
            <div class="card mt-1">
                <div class="card-body text-center">
                    <p><small>Qualquer duvida, sinta-se livre para me procurar no Twitter: <a href="https://twitter.com/RonisXogum" target="_blank">@RonisXogum</a>, ou pelo e-mail, <i>ronis@xogum.tv</i>.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        $('.btn-test').click(function (evt) {
            evt.preventDefault();
            if (!$('#se_jwt')) return;
            $.ajax({
                url: '{{ route("test-alert") }}',
                cache: false,
                method: 'POST',
                data: {
                    se_jwt: $('#se_jwt').val().trim()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (response) {
                console.log(response);
            });
        });
        $('.btn-save').click(function (evt) {
            evt.preventDefault();
            let error = false;
            $('input:required').each(function (i, el) {
                if ($(this).val() === '' || $(this).val() === undefined) {
                    error = true;
                    return $(this).focus();
                }
            });
            if (error) return;
            $.ajax({
                url: '{{ route("new-streamer") }}',
                cache: false,
                method: 'POST',
                data: $('#streamer').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (response) {
                window.location.href = '{{ route("streamer-page", Auth::user()->username) }}';
                new Noty({
                    text: 'Sua página foi ativada/atualizada.',
                    type: 'success'
                }).show();
            });
        });
    });
</script>
@endsection
