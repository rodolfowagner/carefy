@extends('layouts.layout_base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="fs-3 mb-3">Bem vindo</h1>

            @if (Session::has('success'))
                <div class="alert alert-success mt-2 mb-2" role="alert">
                    <i class="fa fa-bell-o" aria-hidden="true"></i> <strong><i class="fa-solid fa-check"></i> {{ Session::get('success') }}</strong>
                </div>
            @endif
            
            @if (Session::has('danger'))
                <div class="alert alert-danger mt-2 mb-2" role="alert">
                    <i class="fa fa-bell-o" aria-hidden="true"></i> <strong><i class="fa-solid fa-triangle-exclamation"></i> {{ Session::get('danger') }}</strong>
                </div>
            @endif

            {{-- <div class="bg-white p-3 radius95">
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="p-4 shadow text-center">
                            <h2 class="m-0">Pacientes</h2>
                            <span class="fs-1">{{ $totalPatients }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-4 shadow text-center">
                            <h2 class="m-0">Internações</h2>
                            <span class="fs-1">{{ $totalHospitalizations }}</span>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush