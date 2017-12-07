<script type="text/javascript">
  $(document).ready(function() {
    /*
     * Token necesario para hacer consultas por ajax.
     * Fecha 15/09/2017
     */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
     * Lista las compras llamanto por ajax al metodo lista del controlador CompraController.
     * Fecha 15/09/2017
    */
    var grid = $("#tblPrestamos").bootgrid({
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
      url: "{{url('prestamo/listar-devolver')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<a href='editar/"+row.id+"' class='btn btn-warning btn-xs' style='margin:2px'>"+
              "<span class='glyphicon glyphicon-edit'></span>"+
            "</a>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>"+
            "<button type='button' class='btn btn-xs btn-success command-devolver' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* poner el focus en el input de busqueda */
      $("#tblPrestamos-header > div > div > div.search.form-group > div > input").focus();
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('prestamo/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['id']);
          $(".socio").html(data['socio']);
          $(".caj_dni").html(data['usuario']['persona']['dni']);
          $(".caj_nombres_apellidos").html(data['usuario']['persona']['nombres'] + " " + data['usuario']['persona']['apellidos']);
          $(".caj_direccion").html(data['usuario']['persona']['direccion']);
          $(".caj_telefono").html(data['usuario']['persona']['telefono']);
          $(".detalles").empty();
          $.each(data['detalles'], function(clave, valor){
            detalle = "<tr><td>"+valor['cantidad']+"</td><td>"+valor['producto']['descripcion']+"</td></tr>";
            $(".detalles").append(detalle);
          });
          $(".fecha_prestamo").html(data['created_at']);
          $(".fecha_devolucion").html(data['fecha']);
          $("#ver").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('prestamo/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['id']);
          $("#frmEliminar").prop('action', "{{url('prestamo/eliminar')}}/" + data['id']);
          $("#eliminar").modal('show');
        });
      }).end().find(".command-devolver").on('click', function(e) {
        $.post("{{url('prestamo/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          if (data['direccion'][0] == 1) {
            $(".direccion").html("PRESTAMO POR RECOGER DE " + data['socio']);
          }else{
            $(".direccion").html("PRESTAMO POR DEVOLVER A " + data['socio']);
          }
          $(".detalles").empty();
          $.each(data['detalles'], function(clave, valor){
            detalle = "<tr><td>"+valor['cantidad']+"</td><td>"+valor['producto']['descripcion']+"</td></tr>";
            $(".detalles").append(detalle);
          });
          if (data['devuelto'] == 1) {
            $(".devuelto").html("ESTE PRÉSTAMO YA FUE DEVUELTO EL " + data['updated_at'] + "!");
            $("#btnDevolver").prop('disabled', true);
          }else {
            $(".devuelto").empty();
            $("#btnDevolver").prop('disabled', false);
          }

          $("#frmDevolver").prop('action', "{{url('prestamo/devolver')}}/" + data['id']);
          $("#devolver").modal('show');
        });
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});

  });
</script>
