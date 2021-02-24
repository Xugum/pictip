@extends('layouts.app')
@section('content')
@auth
@section('title', 'Dashboard')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body text-center">
                    <p><a href="{{ route('streamer-register') }}" class="btn btn-light">Faço streams e quero receber com <img src="{{ url('images/picpay-logo.png') }}" style="height: 18px;"></a>
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
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mt-5">
            <a class="btn btn-lg text-white my-5 rounded-0" style="background-color: #9146ff;" href="{{ route('login') }}">
                <i class="fab fa-twitch fa-fw"></i> Conectar com Twitch
            </a>
            <p class="mt-5"><small>Pague ou receba com <img src="{{ url('images/picpay-logo.png') }}" style="height: 26px;"></small></p>
        </div>
    </div>
</div>
@endauth
@endsection
