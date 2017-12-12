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
          return "<button type='button' class='btn btn-xs btn-success command-agregar' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-cart-arrow-down'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* poner el focus en el input de busqueda */
      $("#tblProductos-header > div > div > div.search.form-group > div > input").focus();
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-agregar").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          // Buscamos los datos del producto y mostramos el código, descripción, y stock en la tabla de detalle.
          $(".codigo").html(data['producto']['codigo']);
          $(".descripcion").html(data['familia'][1]['nombre']+" "+data['marca'][1]['nombre']+" "+data['producto']['descripcion']);
          $(".precio").html(data['producto']['precio']);
          $(".precio").val(data['producto']['precio']);
          $(".cantidad").html(data['stock'][1]);
          $("#cantidad").val("1");
          $("#cantidad").focus();
          $("#btnAgregarProducto").prop('disabled', false);
          $(".foto").html(data['foto']);
          $("#producto_codigo").val(data['producto']['codigo']);
          $("#stock").val(data['stock'][1]);
          $("#guardar").prop('disabled', false);
          var separacion = Math.round(parseFloat(data['producto']['precio']) * 3) / 10;
          $("#monto_separacion").val(separacion.toFixed(2));
        });
      });
    });

    /**
     * Calcula el 30% del precio de venta que se ingresa al precio por unidad del producto.
    */
    $("#precio_venta").change(function(){
      if ($(this).val() != "") {
        var separacion = Math.round(parseFloat($(this).val()) * 3) / 10;
        $("#precio_separacion").val(separacion.toFixed(2));
      }
    });

    /**
     * buscamos uns persona con el numero de dni.
    */
    $("#documento").change(function() {
      if ($(this).val() != "") {
        if ($(this).val().length == 8) {
          $.post("{{url('buscar-persona')}}", {dni: $(this).val()}, function(data, textStatus, xhr) {
            if (data != 0) {
              $("#nombres").val(data['nombres']);
              $("#apellidos").val(data['apellidos']);
              $("#direccion").val(data['direccion']);
              $("#nombres").prop('readonly', false);
              $("#apellidos").prop('readonly', false);
              $("#direccion").prop('readonly', false);
            }else{
              $("#nombres").val("");
              $("#apellidos").val("");
              $("#direccion").val("");
              $("#nombres").prop('readonly', false);
              $("#apellidos").prop('readonly', false);
              $("#direccion").prop('readonly', false);
            }
          });
        }else {
          $("#nombres").val("");
          $("#apellidos").val("");
          $("#direccion").val("");
          $("#nombres").prop('readonly', true);
          $("#apellidos").prop('readonly', true);
          $("#direccion").prop('readonly', true);
        }
      }else{
        $("#nombres").val("");
        $("#apellidos").val("");
        $("#direccion").val("");
        $("#nombres").prop('readonly', true);
        $("#apellidos").prop('readonly', true);
        $("#direccion").prop('readonly', true);
      }
    });

    /**
     * Anulamos el submit con la tecla enter
    */
    $("#frmSeparacion").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
  });
</script>
