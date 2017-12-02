<script type="text/javascript">
  $(document).ready(function() {

    /**
     * Lista los productos mostrando su stocl y un boton para agregar a la venta.
    */
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
          $(".descripcion").html(data['producto']['descripcion']);
          $(".precio").html(data['producto']['precio']);
          $(".precio").val(data['producto']['precio']);
          $(".cantidad").html(data['stock'][1]);
          $("#btnAgregarProducto").prop('disabled', false);
          if (data['descuento']) {
            var descuento = Math.round(data['producto']['precio']*data['descuento']['porcentaje']/10)/10;
            descuento = data['producto']['precio'] - descuento;
            $(".precio").val(descuento.toFixed(2));
            $(".descuento").html("PRODUCTO CON DESCUENTO DEL " + data['descuento']['porcentaje'] + " %!");
          }else {
            $(".precio").val(data['producto']['precio']);
            $(".descuento").empty();
          }
          $("#cantidad").val("1");
          $("#cantidad").focus();
          $(".foto").html(data['foto']);
          $("#producto_codigo").val(data['producto']['codigo']);
          $("#stock").val(data['stock'][1]);
          $("#btnAgregarProducto").prop('disabled', false);
        });
      });
    });

    /**
    * Al hacer clic en el botón btnRegistrarTarjeta, se muestra un modal con un formulario para registrar
    * la venta con tarjeta.
    * Fecha: 24/09/2017
    */
    $("#btnRegistrarTarjeta").click(function() {
      // verificamos si hay un valor en el campo tarjeta.
      if ($("#tarjeta").val()) {
        $("#hdnMontoTarjeta").val($("#tarjeta").val());
        $("#registrarTarjeta").modal("show");
      }
    });

    /**
    * Al cambiar de opcion el tipo de tarjeta que se va a utilizar se va a mostrar el incremento en la venta.
    * Fecha: 23/09/2017
    */
    $("#tarjeta_id").change(function() {
      if ($(this).val()) {

        $.post("{{url('comision-cambio')}}", {tarjeta_id: $(this).val(), monto: $("#hdnMontoTarjeta").val()}, function(data, textStatus, xhr) {
          $("#comision").html('EL INCREMENTO POR EL USO DE ESTA TARJETA SERÁ DE ' + data);
        });
      }else{
        $("#comision").html('');
      }
    });

    /**
    * Al hacer clic en el botón btnTipoCambio, se muestra un modal con el tipo de cambio ya configurado
    * o vacio para configurar.
    * Fecha: 22/09/2017
    */
    $("#btnTipoCambio").click(function() {
      // Verificamos si está configurado el tipo de cambio.
      $.post("{{url('tipo-cambio-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (data == 0) {
          $("#msjTipoCambio").html("DEBE CONFIGURAR EL TIPO DE CAMBIO DE DOLARES A SOLES. ESTO SOLO SE RELIAZA UNA VEZ,"+
          "PARA ACTUALIZAR EL TIPO DE CAMBIO PULSE EL BOTÓN \"Tipo Cambio\".");
          $("#txtCambio").val("");
        }else{
          $("#msjTipoCambio").html("EL TIPO DE CAMBIO DE DOLARES A SOLES ESTÁ CONFIGURADO ACTUALMENTE A "+data);
          $("#txtCambio").val(data);
        }
        $("#tipoCambio").modal("show");
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
    $('.numero').mask("#0", {reverse: true});

    /**
    * Al cambiar el valor de la tarjeta, se tiene que registrar la venta por tarjeta con el sistema.
    * Para esto, mostramos el modal para que el usuario registre esa venta.
    * Fecha: 22/09/2017
    */
    $("#tarjeta").change(function() {
      // Calculamos el vuelto con los valores ingresados.
      $.post("{{url('vuelto-cambio')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $(this).val()},
        function(data, textStatus, xhr) {
        if (data == "error") {
          $("#mensaje").html("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
            "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.");
          $("#errores").modal("show");
          $("#vuelto").val("");
        }else{
          $("#vuelto").val(data);
        }
      });
      $("#monto_tarjeta").val($(this).val());
    });

    $("#btnCambio").click(function() {
      if ($("#txtCambio").val() != "") {
        $.post("{{url('cambio')}}", {cambio: $("#txtCambio").val()}, function(data, textStatus, xhr) {
          $("#tipoCambio").modal("hide");
        });
      }
      $.post("{{url('vuelto-cambio')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $("#tarjeta").val()},
      function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
    * Verificamos el vuelto del cliente.
    * Fecha: 21/09/2017
    */
    $("#dolares").change(function() {
      $.post("{{url('vuelto-cambio')}}", {efectivo: $("#efectivo").val(), dolares: $(this).val(), tarjeta: $("#tarjeta").val()},
      function(data, textStatus, xhr) {
        if(data == "error-cambio"){
          $("#tipoCambio").modal("show");
        }else{
          $("#vuelto").val(data);
        }
      });
    });

    /**
    * Verificamos el estado o características del dinero entregado al cajer, si es mayor
    * al monto total de la venta se muestra el vuelto, de lo contrario, se muestra lo que falta en negativo.
    * Fecha: 21/09/2017
    */
    $("#efectivo").change(function() {
      $.post("{{url('vuelto-cambio')}}",
        {
          efectivo: $(this).val(),
          dolares: $("#dolares").val(),
          tarjeta: $("#tarjeta").val(),
          venta_id: $("#venta_id").val()
        },
        function(data, textStatus, xhr) {
          if (data == "error") {
            $("#mensaje").html("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
              "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.");
            $("#errores").modal("show");
          }else{
            $("#vuelto").val(data);
          }
      });
    });

    /*
    * Token necesario para hacer consultas por ajax.
    * Fecha 18/09/2017
    */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
    * se llama al método enter del input txtCodigo para buscar el producto que tenga ese código.
    * Fecha: 18/09/2017
    */
    $("#txtCodigo").change(function() {
      if ($(this).val() != "") {
        $.post("{{url('buscar-producto')}}", {codigo: $(this).val(), tienda_id: $("#tienda_id").val()}, function(data, textStatus, xhr) {
          if (data['producto'] != 0) {
            $(".codigo").html(data['producto']['codigo']);
            $("#producto_codigo").val(data['producto']['codigo']);
            $(".descripcion").html(data['producto']['descripcion']);
            $(".precio").html(data['producto']['precio']);
            $(".precio").val(data['producto']['precio']);
            $(".cantidad").html(data['stock'][1]);
            $("#stock").val(data['stock'][1]);
            $(".foto").html(data['foto']);
          }else{
            $(".codigo").html("");
            $("#producto_codigo").val("");
            $(".descripcion").html("");
            $(".precio").html("");
            $(".precio").val("");
            $(".cantidad").html("");
            $("#stock").val(null);
            $(".foto").html("<img src='"+"{{url('storage/productos').'/producto.png'}}"+"' style='width:100px;'>");
          }
        });
      }
    });


    $('#panelBuscar').on('hidden.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelBuscar').on('shown.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-minus'></span>")
    });
    $('#panelAgregar').on('hidden.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelAgregar').on('shown.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-minus'></span>")
    });
  });
</script>
