<script type="text/javascript">
  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var grid = $("#tblProductos").bootgrid({
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
      url: "{{url('listar-productos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
            "<span class='fa fa-check'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $(".codigo").empty();
        $(".descripcion").empty();
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".codigo").html(data['producto']['codigo']);
          $(".descripcion").html(data['producto']['descripcion']);
          $("#producto_codigo_kardex").val(data['producto']['codigo']);
          $("#btnBuscarKardex").prop('disabled', false);
        });
      });
    });

    $("#btnBuscarKardex").click(function() {
      if ($("#inicio_kardex").val() != "" && $("#fin_kardex").val() != "" && $("#tienda_kardex").val() != "") {

        $.post("{{url('reporte/kardex')}}",
          {
            inicio: $("#inicio_kardex").val(),
            fin: $("#fin_kardex").val(),
            producto_codigo: $("#producto_codigo_kardex").val(),
            tienda_id: $("#tienda_kardex").val()
          },
          function(data, textStatus, xhr) {

        });
      }
    });

  });
</script>
