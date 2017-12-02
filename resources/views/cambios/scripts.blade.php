<script type="text/javascript">
  $(document).ready(function() {

    /**
     * Esta funcion llena la tabla de ventas.
    */
    var grid = $("#tblVentas").bootgrid({
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
      url: "{{url('listar-ventas')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-codigo='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<a href='venta/"+row.id+"/edit' class='btn btn-xs btn-warning command-edit' data-row-codigo='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-edit'></span></a>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-codigo='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* poner el focus en el input de busqueda */
      $("#tblVentas-header > div > div > div.search.form-group > div > input").focus();
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-venta')}}", {id: $(this).data('row-codigo')}, function(data, textStatus, xhr) {
          $("#impTicket").html(data['ticket']);
          $(".numeracion").html(data['recibo']['numeracion']);
          $("#verTicket").modal("show");
        });
      }).end().find(".command-edit").on("click", function(e){
        //
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-venta')}}", {id: $(this).data('row-codigo')}, function(data, textStatus, xhr) {
          $(".numeracion").html(data['recibo']['numeracion']);
          $("#frmEliminar").prop('action', "{{url('venta')}}/" + data['venta']['id']);
          $("#eliminar").modal("show");
        });
      });
    });

    $(".imprimir").click(function (){
      $("div#impTicket").printArea();
    });

    /*
     * Token necesario para hacer consultas por ajax.
     * Fecha 13/09/2017
     */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

  });
</script>
