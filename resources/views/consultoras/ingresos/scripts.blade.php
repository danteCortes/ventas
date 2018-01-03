<script type="text/javascript">
  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var grid = $("#tblIngresos").bootgrid({
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
      url: "{{url('movimiento/listar-ingresos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-warning command-editar' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-edit'></span></button>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-editar").on("click", function(e){
        $.post("{{url('movimiento/buscar-ingreso')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $("#descripcion_ingreso").val(data['descripcion']);
          $("#monto_ingreso").val(data['total'].toFixed(2));
          $("#frmEditarIngreso").prop('action', "{{url('movimiento/ingreso')}}/" + data['id']);
          $("#editarIngreso").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('movimiento/buscar-ingreso')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $("#frmEliminarIngreso").prop('action', "{{url('movimiento/ingreso')}}/" + data['id']);
          $("#eliminarIngreso").modal('show');
        });
      });
    });

    $(".moneda").mask('#  ##0.00', {reverse: true});
  });

</script>
