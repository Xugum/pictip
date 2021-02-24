@extends('layouts.app')
@section('title', 'Histórico de recebimentos')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mt-5">
                <div class="card-header bg-white">
                    Histórico de recebimentos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-bordered table-hover border border-dark" style="width:100%" id="table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Mensagem</th>
                                    <th>De</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                    @switch($payment->status)
                                        @case('created')
                                            Aguardando
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
                                    </td>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td><strong>R$ {{ number_format($payment->amount, 2, ',', '.') }}</strong></td>
                                    <td>{{ $payment->message ?? '- sem mensagem -' }}</td>
                                    <td>{{ $payment->user->username }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-euro.js" defer></script>
<script>
    $(document).ready(function () {
        $('#table').DataTable({
            createdRow: function(row, data, dataIndex) {
                let alertClass;
                switch (data[0]) {
                    case 'Aguardando':
                        alertClass = 'alert-secondary';
                        break;
                    case 'Expirado':
                    case 'Devolvido':
                    case 'Chargeback':
                        alertClass = 'alert-danger';
                        break;
                    case 'Em análise':
                        alertClass = 'alert-warning';
                        break;
                    case 'Pago':
                    case 'Completo':
                        alertClass = 'alert-success';
                        break;
                }
                return $(row).addClass(alertClass);
            },
            columnDefs: [
                {
                    type: 'date-euro',
                    targets: 1,
                    class: 'text-center align-middle'
                },
                {
                    targets: [0, 2, -1],
                    class: 'text-center text-nowrap align-middle'
                },
                {
                    targets: '_all',
                    class: 'align-middle'
                }
            ],
            order: [[1, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json'
            }
        });
    });
</script>
@endsection