<script type="text/javascript">
  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var grid = $("#tblGastos").bootgrid({
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
      url: "{{url('movimiento/listar-gastos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-warning command-edit' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-edit'></span></button>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-edit").on("click", function(e){
        $.post("{{url('movimiento/buscar-gasto')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $("#descripcion_gasto").val(data['descripcion']);
          $("#monto_gasto").val(data['total'].toFixed(2));
          $("#frmEditarGasto").prop('action', "{{url('movimiento/gasto')}}/" + data['id']);
          $("#editarGasto").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('movimiento/buscar-gasto')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $("#frmEliminarGasto").prop('action', "{{url('movimiento/gasto')}}/" + data['id']);
          $("#eliminarGasto").modal('show');
        });
      });
    });

    $(".moneda").mask('#  ##0.00', {reverse: true});
  });

</script>
