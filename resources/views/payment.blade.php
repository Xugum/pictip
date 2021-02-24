@extends('layouts.app')
@section('title', 'Pedido #' . $payment->order_id)
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h1>Obrigado pelo pedido, {{ Auth::user()->username }}!</h1>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <div class="row">
                        <div class="col"><strong>Valor:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</div>
                        <div class="col text-right"><strong>Data:</strong> {{ $payment->created_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <strong>Mensagem:</strong>
                            <blockquote class="blockquote rounded alert-info my-3 p-4">
                              <p>{{ $payment->message ?? '- sem mensagem -' }}</p><hr />
                              <cite><small>&bull; {{ Auth::user()->username }}</small></cite>
                            </blockquote>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <strong>Status:</strong>
                            @switch($payment->status)
                                @case('created')
                                    Aguardando pagamento
                                    @break
                                @case('expired')
                                    Expirado
                                    @break
                                @case('refunded')
                                    Devolvido
                                    @break
                                @case('chargeback')
                                    Chargeback
                                    @break
                                @case('analysis')
                                    Em análise
                                    @break
                                @case('paid')
                                    Pago
                                    @break
                                @case('completed')
                                    Completo
                                    @break
                                @default
                                    -
                            @endswitch
                        </div>
                        @if($payment->status === 'created')
                        <div class="col text-right">
                            <a href="{{ $payment->payment_url }}" target="_blank" class="btn text-success p-0">Pague com <img src="{{ url('images/picpay.png') }}" style="height: 48px;"></a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection