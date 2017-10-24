<script type="text/javascript">
  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(".porcentaje").mask("99", {reverse: true});

    var grid = $("#tblDescuentos").bootgrid({
      labels: {
        all: "todos",
        infos: "",
        loading: "Cargando datos...",
        noResults: "Ningun resultado encontrado",
        refresh: "Actualizar",
        search: "Buscar"
      },
      ajax: true,
      post: function (){
        return {
          '_token': '{{ csrf_token() }}',
          id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
        };
      },
      url: "{{url('descuento/listar-vencidos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-editar' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-edit'></span></button>" +
            "<button type='button' class='btn btn-xs btn-danger command-borrar' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-editar").on("click", function(e){
        $(".linea_id").empty();
        $(".familia_id").empty();
        $(".marca_id").empty();
        $.post("{{url('descuento/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          // Buscamos los datos del desuento para mostrarlos en el formulario de edición.
          $(".tienda").html(data['descuento']['id'] + " PARA " +data['descuento']['tienda']['nombre']);
          $("#frmEditarDescuento").prop('action', 'modificar/'+data['descuento']['id']);
          if (data['descuento']['linea_id']) {
            $(".linea_id").html("<option value='"+data['descuento']['linea_id']+"'>"+data['descuento']['linea']['nombre']+"</option>");
          }
          $(".linea_id").append("<option value=''>SELECCIONE LINEA</option>");
          $.each(data['lineas'], function(clave, valor) {
            if (valor['id'] != data['descuento']['linea_id']) {
              $(".linea_id").append("<option value='"+valor['id']+"'>"+valor['nombre']+"</option>");
            }
          });
          if (data['descuento']['familia_id']) {
            $(".familia_id").html("<option value='"+data['descuento']['familia_id']+"'>"+data['descuento']['familia']['nombre']+"</option>");
          }
          $(".familia_id").append("<option value=''>SELECCIONE FAMILIA</option>");
          $.each(data['familias'], function(clave, valor) {
            if (valor['id'] != data['descuento']['familia_id']) {
              $(".familia_id").append("<option value='"+valor['id']+"'>"+valor['nombre']+"</option>");
            }
          });
          if (data['descuento']['marca_id']) {
            $(".marca_id").html("<option value='"+data['descuento']['marca_id']+"'>"+data['descuento']['marca']['nombre']+"</option>");
          }
          $(".marca_id").append("<option value=''>SELECCIONE MARCA</option>");
          $.each(data['marcas'], function(clave, valor) {
            if (valor['id'] != data['descuento']['marca_id']) {
              $(".marca_id").append("<option value='"+valor['id']+"'>"+valor['nombre']+"</option>");
            }
          });
          $(".porcentaje").val(data['descuento']['porcentaje']);
          $(".fecha_fin").val(data['descuento']['fecha_fin']);
          $("#editar").modal('show');
        });
      }).end().find(".command-borrar").on('click', function(e) {
        $.post("{{url('descuento/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          // Buscamos los datos del desuento para mostrarlos en el formulario de edición.
          $(".tienda").html(data['descuento']['id'] + " PARA " +data['descuento']['tienda']['nombre']);
          $("#frmEliminar").prop('action', 'eliminar/'+data['descuento']['id']);
          $(".numero").html(data['descuento']['id']);

          $("#eliminar").modal('show');
        });
      });
    });
  });
</script>
