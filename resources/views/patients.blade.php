@extends('layouts.layout_base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="fs-3 mb-3"><i class="fa-solid fa-users"></i> Pacientes</h1>
            <div class="bg-white p-3 radius95">
                <div class="col-12 mb-3">
                    @if (count($patients))
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Nascimento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($patients as $patient)
                                    <tr>
                                        <th scope="row">{{ $patient->code }}</th>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ FormatHelper::invertDate($patient->birth) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-primary mt-2 mb-2" role="alert">
                            <i class="fa fa-bell-o" aria-hidden="true"></i> <strong>NÃO HÁ REGISTROS</strong>
                        </div>
                    @endif
                    {{ $patients->links('pagination::bootstrap-5') }}
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