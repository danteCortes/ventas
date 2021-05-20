
  var venta = {}
  var tarjetas = []
$(function() {

  /**
   * Rellena la tabla produtos.
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
        '_token': $("meta[name='csrf-token']").attr('content'),
        id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
      };
    },
    url: "/listar-productos",
    formatters: {
      "commands": function(column, row){
        return `<button type='button' class='btn btn-xs btn-success command-agregar' data-row-codigo='${row.codigo}' style='margin:2px'>
          <span class='fa fa-cart-arrow-down'></span></button>`;
      }
    }
  }).on("loaded.rs.jquery.bootgrid", function(){
    /* poner el focus en el input de busqueda */
    $("#tblProductos-header > div > div > div.search.form-group > div > input").focus();
    /* Se ejecuta despues de cargar y procesar los datos */
    grid.find(".command-agregar").on("click", function(e){
      $.post("/buscar-producto", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
        // Buscamos los datos del producto y mostramos el código, descripción, y stock en la tabla de detalle.
        $(".codigo").html(data['producto']['codigo']);
        $(".descripcion").html(data['familia'][1]['nombre']+" "+data['marca'][1]['nombre']+" "+data['producto']['descripcion']);
        $(".precio").html(data['producto']['precio']);
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
        $("#cantidad_producto").val("1");
        $("#cantidad_producto").focus();
        $(".foto").html(data['foto']);
        $("#producto_codigo").val(data['producto']['codigo']);
        $("#stock").val(data['stock'][1]);
        $("#btnAgregarProducto").prop('disabled', false);
      });
    });
  });

  frmNuevaVenta()

  $("#frmNuevaVenta").on('keypress', function(event) {
      if (event.which == 13) {
          return false
      }
  })

    /**
    * Al hacer clic en el botón btnTipoCambio, se muestra un modal con el tipo de cambio ya configurado
    * o vacio para configurar.
    * Fecha: 22/09/2017
    */
    $("#btnTipoCambio").click(function() {
      // Verificamos si está configurado el tipo de cambio.
      $.post("/tipo-cambio", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (data == 0) {
          $("#msjTipoCambio").html("DEBE CONFIGURAR EL TIPO DE CAMBIO DE DOLARES A SOLES. ESTO SOLO SE RELIAZA UNA VEZ,"+
          "PARA ACTUALIZAR EL TIPO DE CAMBIO PULSE EL BOTÓN \"Tipo Cambio\".");
          $("#txtCambio").val("");
        }else{
          $("#msjTipoCambio").html("EL TIPO DE CAMBIO DE DOLARES A SOLES ESTÁ CONFIGURADO ACTUELMENTE A "+data);
          $("#txtCambio").val(data);
        }
        $("#tipoCambio").modal("show");
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
    $('.numero').mask("#0", {reverse: true});
    $('.oculto').mask("0", {reverse: true});

    $(".oculto").focus(function() {
      $(this).val("");
    });

    $("#btnCambio").click(function() {
      if ($("#txtCambio").val() != "") {
        $.post("/cambio", {cambio: $("#txtCambio").val()}, function(data, textStatus, xhr) {
          $("#tipoCambio").modal("hide");
        });
      }
      $.post("/vuelto", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $("#tarjeta").val()},
      function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
    * Verificamos si el tipo de cambio está configurado y posteriormente el vuelto del cliente.
    * Fecha: 21/09/2017
    */
    $("#dolares").change(function() {
      $.post("/tipo-cambio", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (data == 0) {
          $("#tipoCambio").modal("show");
        }
      });
      $.post("/vuelto", {efectivo: $("#efectivo").val(), dolares: $(this).val(), tarjeta: $("#tarjeta").val()},
      function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
    * Verificamos el estado o características del dinero entregado al cajer, si es mayor
    * al monto total de la venta se muestra el vuelto, de lo contrario, se muestra lo que falta en negativo.
    * Fecha: 21/09/2017
    */
    $("#efectivo").change(function() {
      $.post("/vuelto", {efectivo: $(this).val(), dolares: $("#dolares").val(), tarjeta: $("#tarjeta").val()},
        function(data, textStatus, xhr) {
          if (data == "error") {
            toastr.error("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
            "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.")
          }else{
            $("#vuelto").val(data);
          }
      });
    });

    /**
    * Busca un cliente, ya sea persona o empresa, si el campo queda vacio puede guardarse la venta.
    * Fecha: 21/09/2017
    */
    $("#documento").change(function(){
      // Verificamos si el campo está vacio.
      if ($(this).val() != "") {
        // Verificamos si es un número de DNI, o RUC.
        if($(this).val().length == 8){
          // Buscarmos los datos de la persona que tenga este dni, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("/buscar-persona", {dni: $(this).val()}, function(data, textStatus, xhr) {
            if (data != 0) {
              $("#nombre").prop('readonly', true);
              $("#nombres").prop('readonly', false);
              $("#apellidos").prop('readonly', false);
              $("#direccion").prop('readonly', false);
              $("#nombre").val("");
              $("#nombres").val(data['nombres']);
              $("#apellidos").val(data['apellidos']);
              $("#direccion").val(data['direccion']);
              if (data['puntos']) {
                $(".puntos").html("ESTE CLIENTE TIENE " + data['puntos'] + " PUNTOS ACUMULADOS HASTA EL MOMENTO!");
                if (data['puntos'] > 1000) {
                  $(".grupo-puntos").html("<span class='input-group-addon'>USAR </span>"+
                  "<input type='text' name='puntos' class='form-control oculto' placeholder='PUNTOS' style='text-align:right; required'>"+
                  "<span class='input-group-addon'> 000 PUNTOS</span>");
                }
              }
            }else{
              $("#nombre").prop('readonly', true);
              $("#nombres").prop('readonly', false);
              $("#apellidos").prop('readonly', false);
              $("#direccion").prop('readonly', false);
              $("#nombre").val("");
              $("#nombres").val("");
              $("#apellidos").val("");
              $("#direccion").val("");
              $(".puntos").html("");
              $(".grupo-puntos").html("");
            }
          });
        }else if ($(this).val().length == 11) {
          // Buscarmos los datos de la empresa que tenga este ruc, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("/buscar-empresa", {ruc: $(this).val()}, function(data, textStatus, xhr) {
            $("#nombre").prop('readonly', false);
            $("#nombres").prop('readonly', true);
            $("#apellidos").prop('readonly', true);
            $("#direccion").prop('readonly', false);
            $("#nombre").val(data['nombre']);
            $("#nombres").val("");
            $("#apellidos").val("");
            $("#direccion").val(data['direccion']);
            $(".puntos").empty();
            $(".grupo-puntos").empty();
          });
        }else{
          // Si no tiene 8 ni 11 dítos el documento, se limpia el formulario y se muestra un mensaje de error.
          $("#nombre").prop('readonly', true);
          $("#nombres").prop('readonly', true);
          $("#apellidos").prop('readonly', true);
          $("#direccion").prop('readonly', false);
          $("#nombre").val("");
          $("#nombres").val("");
          $("#apellidos").val("");
          $("#direccion").val("");
          $(".puntos").empty();
          $(".grupo-puntos").empty();
          $("#mensaje").html("EL DOCUMENTO SOLO PUEDE CONTENER 8 U 11 DÍGITOS.");
          $("#errores").modal("show");
        }
      }else{
        // Limpiamos todos los campos y la venta se hará con un cliente común.
        $("#nombre").prop('readonly', true);
        $("#nombres").prop('readonly', true);
        $("#apellidos").prop('readonly', true);
        $("#direccion").prop('readonly', true);
        $("#nombre").val("");
        $("#nombres").val("");
        $("#apellidos").val("");
        $("#direccion").val("");
        $(".puntos").empty();
        $(".grupo-puntos").empty();
      }
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
   /*
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
    */

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

const frmNuevaVenta = async () => {

  try{
    let config = {
      method: 'GET',
      url: `/obtener-detalles`
    }
    let response = await axios(config)

    venta = response.data.venta
    venta = {
      ...venta,
      detalles: response.data.detalles,
      tarjetaVenta: response.data.tarjetaVenta
    }
    if(response.data.tarjetaVenta){
      $("#tarjeta").val(response.data.tarjetaVenta.monto)
      $("#btnRegistrarTarjeta").attr('disabled', true)
    }

    if(response.data.detalles.length > 0){
      llenarDetalles()
      calcularVuelto()
    }else{
      $("#tblDetalles").hide()
      $("#frmVenta").hide()
    }
  }catch(errors){console.log(errors)}
}

const llenarDetalles = () => {

  detalles = ''
  total = 0
  venta.detalles.map(detalle => {
    detalles += `<tr>
      <td class="text-center">
        <button class="btn btn-xs btn-danger" onclick="quitarDetalle(${detalle.id})">Quitar</button>
      </td>
      <td class="text-center">${detalle.cantidad}</td>
      <td>${detalle.codigo}</td>
      <td>${detalle.descripcion}</td>
      <td style="text-align:right">${parseFloat(detalle.precio_unidad).toFixed(2)}</td>
      <td style="text-align:right">${parseFloat(detalle.total).toFixed(2)}</td>
    </tr>`
    total += parseFloat(detalle.total)
  })
  if(venta.tarjetaVenta && venta.tarjetaVenta.comision != 0){
    detalles += `<tr>
      <td class="text-center"><button class="btn btn-xs btn-danger">Quitar</button></td>
      <td class="text-center">1</td>
      <td>TARJ</td>
      <td>${venta.tarjetaVenta.descripcion}</td>
      <td style="text-align:right">${parseFloat(venta.tarjetaVenta.comision)}</td>
      <td style="text-align:right">${parseFloat(venta.tarjetaVenta.comision)}</td>
    </tr>`
    total += parseFloat(venta.tarjetaVenta.comision)
  }
  $("#detalles").html(`
    ${detalles}
    <tr>
      <td colspan="5"><strong class="pull-right">TOTAL: </strong></td>
      <td style="text-align:right">${parseFloat(total).toFixed(2)}</td>
    </tr>`
  )
  $("#tblDetalles").show()
  $("#frmVenta").show()
}

const agregarDetalle = async () => {

  try{
      let config = {
          method: 'POST',
          url: `/detalle`,
          data: {
              producto_codigo: $("#producto_codigo").val(),
              stock: $("#stock").val(),
              tipo: $("#tipo").val(),
              precio_unidad: $("#precio_unidad").val(),
              cantidad: $("#cantidad_producto").val()
          }
      }
      let response = await axios(config)

      venta = response.data.venta
      venta.detalles = response.data.detalles
      venta.tarjetaVenta = response.data.tarjetaVenta

      if(response.data.detalles.length > 0){
        llenarDetalles()
      }else{
        $("#tblDetalles").hide()
        $("#frmVenta").hide()
      }

      $("#tblProductos").bootgrid('reload')
      $("#producto_codigo").val('')
      $("#stock").val('')
      $("#precio_unidad").val('')
      $("#cantidad_producto").val('')
      $(".codigo").html('')
      $(".descripcion").html('')
      $(".precio").html('')
      $("#btnAgregarProducto").prop('disabled', true);
  }catch(errors){
      if(errors.response.status != 422){
          toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema.')
          console.log(errors.response)
      }else{
          errors.response.data.map(error => toastr.error(error))
      }
  }
}

const quitarDetalle = async (id) => {

  try{
    let config = {
      method: 'DELETE',
      url: `/detalle/${id}`
    }
    let response = await axios(config)

    venta = response.data.venta
    venta.detalles = response.data.detalles
    venta.tarjetaVenta = response.data.tarjetaVenta

    if(response.data.detalles.length > 0){
      llenarDetalles()
    }else{
      $("#tblDetalles").hide()
      $("#frmVenta").hide()
    }

    $("#tblProductos").bootgrid('reload')
  }catch(errors){console.log(errors)}
}

const mdlRegistrarPagoTarjeta = async () => {

  if ($("#tarjeta").val() && $("#tarjeta").val() != 0) {
    
    try{

      let config = {
        method: 'GET',
        url: `/mdl-registrar-pago-tarjeta?id=${venta.id}`
      }
      let response = await axios(config)

      tarjetas = response.data.tarjetas

      opciones = '<option value>--SELECCIONAR UNA TARJETA--</option>'
      response.data.tarjetas.map(tarjeta => {
        opciones += `<option value="${tarjeta.id}">${tarjeta.nombre}</option>`
      })
      $("#tarjeta_id").html(opciones)
      if(venta.tarjetaVenta){

        $("#tarjeta_id").val(venta.tarjetaVenta.tarjeta_id)
        $("#operacion").val(venta.tarjetaVenta.operacion)
        $("#hdnMontoTarjeta").val(venta.tarjetaVenta.monto)
      }else{
        
        $("#tarjeta_id").val('')
        $("#operacion").val('')
        $("#hdnMontoTarjeta").val($("#tarjeta").val())
      }
      $("#comision").html('');
      $("#registrarTarjeta").modal("show")

    }catch(errors){console.log(errors)}
  }else{

    toastr.error('Debe ingresar un monto en el campo Tarjeta.')
  }
}

const registrarPagoTarjeta = async () => {
  
  try{
    let config = {
      method: 'POST',
      url: `/tarjeta-venta`,
      data: {
        tarjeta_id: $("#tarjeta_id").val(),
        operacion: $("#operacion").val(),
        monto: $("#hdnMontoTarjeta").val(),
      }
    }
    let response = await axios(config)

    venta = response.data.venta
    venta.detalles = response.data.detalles
    venta.tarjetaVenta = response.data.tarjetaVenta
    
    llenarDetalles()

    $("#btnRegistrarTarjeta").attr('disabled', true)
    toastr.success('El pago con tarjeta fue registrado con éxito.')
    $("#registrarTarjeta").modal("hide")

  }catch(errors){
    if(errors.response.status != 422){
      toastr.error('Hubo un error en el sistema, contacte con el administrador del sistema.')
      console.log(errors)
    }else{
      errors.response.data.map(error => toastr.error(error))
    }
  }
}

const calcularComisionTarjeta = async (value) => {
  
  if (value) {
    tarjeta = await tarjetas.find(t => t.id == value)
    comision_anterior = 0
    if(venta.tarjetaVenta){
      comision_anterior = venta.tarjetaVenta.comision
    }
    comision = ($("#hdnMontoTarjeta").val() - comision_anterior) * (tarjeta.comision / 100)
    $("#comision").html('EL INCREMENTO POR EL USO DE ESTA TARJETA SERÁ DE S/ ' + parseFloat(comision).toFixed(2))
  }else{
    $("#comision").html('');
  }
}

const calcularVuelto = async () => {
  try{

    let config = {
      method: 'POST',
      url: `/vuelto`,
      data: {
        efectivo: $("#efectivo").val(),
        dolares: $("#dolares").val(),
        tarjeta: $("#tarjeta").val()
      }
    }
    let response = await axios(config)
    
    if (response.data == "error") {
      toastr.error("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
      "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.")
    }else{
      $("#vuelto").val(response.data);
    }
    $("#monto_tarjeta").val($("#tarjeta").val());
  }catch(errors){console.log(errors)}
}