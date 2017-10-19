<script type="text/javascript">
  $(document).ready(function() {

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
      url: "{{url('descuento/listar-todos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-success command-agregar' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-cart-arrow-down'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-agregar").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          // Buscamos los datos del producto y mostramos el código, descripción, y stock en la tabla de detalle.
          $(".codigo").html(data['producto']['codigo']);
          $(".descripcion").html(data['producto']['descripcion']);
          $(".precio").html(data['producto']['precio']);
          $(".precio").val(data['producto']['precio']);
          $(".cantidad").html(data['stock'][1]);
          $("#btnAgregarProducto").prop('disabled', false);
          $(".foto").html(data['foto']);
          $("#producto_codigo").val(data['producto']['codigo']);
          $("#stock").val(data['stock'][1]);
        });
      });
    });
  });
</script>
