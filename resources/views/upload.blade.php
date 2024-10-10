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
                            <a href="{{ route('upload.index') }}" class="btn btn-lg btn-outline-success me-2"><i class="fa-solid fa-angle-left"></i></a>
                            <button id="btn_validate" type="button" class="btn btn-lg btn-outline-success px-5"><i class="fa-solid fa-magnifying-glass"></i> Validar dados</button>
                        </div>
                        <div class="col-md-6 text-center text-md-end mb-3">
                            <button id="btn_save" type="button" class="btn btn-lg btn-success px-5">Salvar <i class="fa-solid fa-check"></i></button>
                        </div>
                    </div>

                    <div id="div_cards" class="row mb-3"></div>
                    
                    <p id="total" class="mt-3 text-center fw-bold"></p>

                </div>

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
                loadItems(csvData);
            };
            reader.readAsText(file);
        }
    });

    function loadItems(csvData)
    {
        var total = 0;
        var lines = csvData.split("\n");
        var headers = lines[0].split(",");
        var index_header = [];
        var html = '';

        headers.forEach(function(header) {
            index_header.push(header.trim());
        });
        
        lines.slice(1).forEach(function(line) {
            if (line.trim() !== "") // Ignorar linhas em branco
            {
                total ++;
                var values = line.split(",");
                var uuid = self.crypto.randomUUID();

                html+= '<div class="col-md-6 col-md-4 col-lg-3 mb-3">';
                html+= '<div class="card border-warning cardItem" id="card_' + uuid + '" data-uuid="' + uuid + '">';
                html+= '<div class="card-header bg-warning"><i class="fa-solid fa-triangle-exclamation"></i> Pendente</div>';
                html+= '<div class="card-body">';
                
                var item = new Object();

                values.forEach(function(value, index) {
                    var type = index_header[index];

                    item.uuid = uuid;

                    switch (type)
                    {
                        case 'nome':
                            html+= '<h5 class="card-title mb-0" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Paciente"><i class="fa-solid fa-user"></i> ' + value.trim() + '</h5>';
                            item.nome = value.trim();
                            break;
                        case 'nascimento':
                            html+= '<div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Data de nascimento"><i class="fa-solid fa-cake-candles"></i> ' + value.trim() + '</div>';
                            item.nascimento = value.trim();
                            break;
                        case 'codigo':
                            html+= '<div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Código do Paciente"><i class="fa-solid fa-fingerprint"></i> ' + value.trim() + '</div>';
                            item.codigo = value.trim();
                            break;
                        case 'guia':
                            html+= '<div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Código da Guia de Internação"><i class="fa-solid fa-file-pen"></i> ' + value.trim() + '</div>';
                            item.guia = value.trim();
                            break;
                        case 'entrada':
                            html+= '<div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Data de admissão (entrada) da internação"><i class="fa-solid fa-person-arrow-down-to-line"></i> ' + value.trim() + '</div>';
                            item.entrada = value.trim();
                            break;
                        case 'saida':
                            html+= '<div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Data de alta (saída) da internação"><i class="fa-solid fa-person-arrow-up-from-line"></i> ' + value.trim() + '</div>';
                            item.saida = value.trim();
                            break;
                        default: 
                            html+= "---";
                    }
                });

                var itemJson = JSON.stringify(item);

                html += '<ul class="ul_errors p-2 mt-1 text-danger"></ul>';
                
                html += '<div class="d-none">'
                html += '<input class="form-check-input" type="checkbox" value="1" name="item[is_valid][' + uuid + ']">';
                html += "<input type='text' name='item[data][" + uuid + "]' value='"+ itemJson +"'>";
                html += '</div>';
                
                html += '</div>';
                html += '</div>';
                html += '</div>';
                
                // console.log(item);
            }
        });

        $("#div_cards").html(html);
        $("#uploadON").addClass('d-none');
        $("#uploadOFF").removeClass('d-none');
        $("#total").html('<span class="text-warning">Total de ' + total + ' registros pendentes</span>');
        activeTooltip();
    }

    $("#btn_validate").click(function() {
        validateCards()
    });

    function validateCards()
    {
        var items = [];
        $('.ul_errors').html('');

        $('#div_cards .cardItem').each(function(index) {
            var uuid = $(this).data('uuid');
            var data = $(this).find("[name='item[data][" + uuid + "]']").val().trim();
            // var is_valid = $(this).find("[name='item[is_valid][" + uuid + "]']").prop('checked') ? true : false;
            items.push(data);
        });

        $.ajax({
            headers:{'X-CSRF-TOKEN':$('meta[name="csrftoken"]').attr('content')},
            method:'POST',
            url:"{{ route('upload.validate') }}",
            cache:false,
            data:{'action':'validate',"items":items},
            success:function(response)
            {
                // valid_items
                if (response['valid_items']) {
                    $.each(response['valid_items'], function(k1, v1){
                        var uuid = v1['uuid'];

                        $("input[name='item[is_valid][" + uuid + "]").prop("checked", true);

                        $("#card_" + uuid).removeClass('border-warning')
                                            .addClass('border-success');
                        
                        $("#card_" + uuid).find('.card-header')
                                            .removeClass('bg-warning')
                                            .addClass('bg-success text-white').html('<i class="fa-solid fa-check"></i> Válido');
                    });
                }

                // invalid_items
                if (response['invalid_items']) {
                    $.each(response['invalid_items'], function(k1, v1){
                        var uuid = v1['uuid'];

                        $("input[name='item[is_valid][" + uuid + "]").prop("checked", false);

                        $("#card_" + uuid).removeClass('border-warning')
                                            .addClass('border-danger');

                        $("#card_" + uuid).find('.card-header')
                                            .removeClass('bg-warning')
                                            .addClass('bg-danger text-white').html('<i class="fa-solid fa-xmark"></i> Inválido');

                        $.each(v1, function(k2, v2){
                            if (k2 != "uuid")
                                $("#card_" + uuid).find('.ul_errors').append('<li>' + v2 + '</li>')
                        });
                    });
                }

                // total
                $('#total').html('<span class="text-success">Total de válidos ' + response['valid_total'] + ' </span><br><span class="text-danger">Total de inválidos ' + response['invalid_total'] + ' </span>');

            }, error:function() {
                console.log('Erro ao validar!');
            }
        });

    }

});
</script>
@endpush