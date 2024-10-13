@extends('layouts.layout_base')

@section('content')
<div class="container mb-5">
    <div class="row">
        <div class="col">
            <h1 class="fs-3 mb-3">Upload</h1>
            <div class="bg-white p-3 radius95">
                
                <div id="uploadON" class="mb-3">
                    <label for="inputFile" class="form-label fw-bold ps-2">Carregue o arquivo CSV</label>
                    <input class="form-control" type="file" id="inputFile">
                </div>

                <div id="uploadOFF" class="d-none">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3">
                            <a href="{{ route('upload.index') }}" class="btn btn-lg btn-outline-success me-1"><i class="fa-solid fa-angle-left"></i></a>
                            <button data-type="verify_table" type="button" class="btn btn-lg btn-outline-success px-4 mb-1 me-1 btn_action">Validar dados <i class="fa-solid fa-table"></i></button>
                            {{-- <button data-type="verify_database" type="button" class="btn btn-lg btn-outline-success px-4 mb-1 btn_action">Verificar banco de dados <i class="fa-solid fa-database"></i></button> --}}
                        </div>
                        <div class="col-md-6 text-center text-md-end mb-3">
                            <button id="btn_submit" data-type="insert" type="button" class="btn btn-lg btn-success px-4 mb-1 btn_action">Salvar <i class="fa-solid fa-check"></i></button>
                        </div>
                    </div>

                    <form id="form_data">
                        <div id="table_csv" class="table-responsive"></div>
                        <input id="input_type" name="type" type="hidden" readonly="readonly">
                    </form>

                    <p id="total" class="text-center fw-bold"></p>

                </div>

            </div>

            <div class="mt-4 fs-7">
                <strong>Informações</strong>
                <p class="m-0">
                    A validação é feita em 2 etapas
                    <br>1° Comparação entre as linhas da <i class="fa-solid fa-table"></i> tabela.
                    <br>2° Verificação com <i class="fa-solid fa-database"></i> banco de dados.
                    <br>Carregue o arquivo para iniciar.
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('files/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('files/vendor/jquery-validation/messages_pt_BR.js') }}"></script>
<script src="{{ asset('files/vendor/jquery.inputmask.min.js') }}"></script>
<script>
$(document).ready(function()
{
    $('#inputFile').on('change', function(event)
    {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e){
                var csvData = e.target.result;
                createTable(csvData);
            };
            reader.readAsText(file);
        }
    });

    function createTable(csvData)
    {
        var id_auto = 0;
        var lines = csvData.split("\n");
        var headers = lines[0].split(",");
        var index_header = [];
        var tableHtml = '';

        tableHtml+= ''
        + '<table class="table table-striped">'
            + '<thead class="text-uppercase">'
                + '<tr>';
                    headers.forEach(function(header) {
                        index_header.push(header.trim());
                        switch (header.trim()) {
                            case 'nome':
                                title = 'Paciente';
                                break;
                            case 'nascimento':
                                title = 'Nascimento';
                                break;
                            case 'codigo':
                                title = 'Cód beneficiário';
                                break;
                            case 'guia':
                                title = 'Cód Guia';
                                break;
                            case 'entrada':
                                title = 'Data entrada';
                                break;
                            case 'saida':
                                title = 'Data saída';
                                break;
                        }
                        tableHtml+= '<th class="text-center">' + title + '</th>';
                    });
                    tableHtml+= '<th width="450">Status</th>';
                    tableHtml+= ''
                + '</tr>'
            + '</thead>'
            + '<tbody>';

        var html = ""

        lines.slice(1).forEach(function(line) {
            if (line.trim() !== "") {

                id_auto++;
                var values = line.split(",");
                var dataItem = "";

                tableHtml+= ''
                + '<tr id="tr_' + id_auto + '" class="tr_class">'
                    
                    values.forEach(function(value, index) {
                        var type = index_header[index];
                        tableHtml+= '<td><input name="input[' + type + '][' + id_auto + ']" type="text" class="form-control form-control-sm form-control-custom" value="' + value.trim() + '"></td>';
                    });

                    tableHtml+= ''
                    + '<td>'
                        // + '<span class="badge text-bg-warning">Pendente</span>'
                        + '<ul id="ul_table_errors_' + id_auto + '" class="ul_errors text-danger"></ul>'
                        + '<ul id="ul_database_errors_' + id_auto + '" class="ul_errors text-danger"></ul>'
                        +'<div class="d-none">'
                            + '<input name="id_auto[]" type="hidden" readonly="readonly" class="form-control form-control-sm form-control-custom" value="' + id_auto + '">'
                            + '<input type="checkbox" value="1" name="input[is_valid][' + id_auto + ']" class="checkbox_valid">'
                        + '</div>'
                    + '<td>'
                + '</tr>';
            }
        });

        tableHtml+= ''
            + '</tbody>'
        + '</table>';

        $('#table_csv').html(tableHtml);
        $("#uploadON").addClass('d-none');
        $("#uploadOFF").removeClass('d-none');
        $("#total").html('<span class="text-warning">Total de ' + id_auto + ' registros pendentes</span>');
    }

    $(".btn_action").click(function()
    {
        var type = $(this).data('type');
        $("#input_type").val(type);

        if (type == 'insert' && $(".checkbox_valid:checked").length <= 0)
            return alert('Não há linhas válidas para inserir.');

        $("#form_data").submit();
    });

    $("#form_data").submit(function(e){
        e.preventDefault();
        
        $('.ul_errors').html('');
        $('.table tbody tr').removeClass();
        $('#total').html('');

        $.ajax({
            headers:{'X-CSRF-TOKEN':$('meta[name="csrftoken"]').attr('content')},
            method:'POST',
            url:"{{ route('upload.post') }}",
            cache:false,
            timeout:15000,
            data:$(this).serialize(),
            beforeSend:function(){loadON()},
            complete:function(){loadOFF()},
            success:function(response){

                if (response['total'] && response['total'] > 0)
                    return window.location.href = '/';

                if (response['table']['validate'])
                {
                    if (response['table']['result_success'])
                    {
                        $.each(response['table']['result_success'], function(k, id_auto){
                            $("#tr_" + id_auto).addClass('table-success');
                            $("input[name='input[is_valid][" + id_auto + "]").prop("checked", true);
                        });
                    }
                    
                    if (response['table']['result_error'])
                    {
                        $.each(response['table']['result_error'], function(k1, v1){
                            $("#tr_" + v1['id_auto']).addClass('table-danger');
                            $("input[name='input[is_valid][" + v1['id_auto'] + "]").prop("checked", false);
                            $.each(v1['errors'], function(k2, v2){
                                $("#ul_table_errors_" + v1['id_auto']).append('<li data-bs-toggle="tooltip" data-bs-title="Erro na verificação da tabela"><i class="fa-solid fa-table"></i> ' + v2 + '</li>');
                            });
                        });
                    }
                    $('#total').append('<span class="text-success">Válidos: ' + response['table']['result_success_total'] + ' </span> <i class="fa-solid fa-table"></i> <span class="text-danger">Inválidos: ' + response['table']['result_error_total'] + ' </span>');
                }

                if (response['database']['validate'])
                {
                    if (response['database']['result_success'])
                    {
                        $.each(response['database']['result_success'], function(k, id_auto){
                            $("#tr_" + id_auto).addClass('table-success');
                            $("input[name='input[is_valid][" + id_auto + "]").prop("checked", true);
                        });
                    }
                    
                    if (response['database']['result_error'])
                    {
                        $.each(response['database']['result_error'], function(k1, v1){
                            $("#tr_" + v1['id_auto']).addClass('table-danger');
                            $("input[name='input[is_valid][" + v1['id_auto'] + "]").prop("checked", false);
                            $.each(v1['errors'], function(k2, v2){
                                $("#ul_table_errors_" + v1['id_auto']).append('<li data-bs-toggle="tooltip" data-bs-title="Erro na verificação com banco de dados"><i class="fa-solid fa-database"></i> ' + v2 + '</li>');
                            });
                        });
                    }
                    $('#total').append('<hr><span class="text-success">Válidos: ' + response['database']['result_success_total'] + ' </span> <i class="fa-solid fa-database"></i> <span class="text-danger">Inválidos: ' + response['database']['result_error_total'] + ' </span>');
                }
                activeTooltip();
            }
        });
    });
    
});
</script>
@endpush