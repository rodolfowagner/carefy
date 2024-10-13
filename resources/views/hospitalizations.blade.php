@extends('layouts.layout_base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="fs-3 mb-3"><i class="fa-solid fa-book-medical"></i> Internações</h1>
            <div class="bg-white p-3 radius95">
                <div class="col-12 mb-3">
                    @if (count($hospitalizations))
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Paciente</th>
                                    <th scope="col">Entrada</th>
                                    <th scope="col">Saída</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hospitalizations as $hospitalization)
                                    <tr>
                                        <th scope="row">{{ $hospitalization->code }}</th>
                                        <td>{{ $hospitalization->patient->name }}</td>
                                        <td>{{ FormatHelper::invertDate($hospitalization->date_in) }}</td>
                                        <td>{{ FormatHelper::invertDate($hospitalization->date_out) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-primary mt-2 mb-2" role="alert">
                            <i class="fa fa-bell-o" aria-hidden="true"></i> <strong>NÃO HÁ REGISTROS</strong>
                        </div>
                    @endif
                    {{ $hospitalizations->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

})
</script>
@endpush