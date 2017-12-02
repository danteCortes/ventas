<script type="text/javascript">
  $(document).ready(function() {

    $("#frmProducto").keypress(function(event) {
      if (event.which == 13) {
        return false;
      }
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#btnLinea").click(function() {
      $.post("{{url('linea')}}", {nombre: $("#nombreLinea").val()}, function(data, textStatus, xhr) {
        $("#linea_id").html(data);
      });
      $('#nuevaLinea').modal('hide');
    });

    $('#nuevaLinea').on('shown.bs.modal', function () {
      $('#nombreLinea').val("");
      $('#nombreLinea').focus();
    });

    $("#btnFamilia").click(function() {
      $.post("{{url('familia')}}", {nombre: $("#nombreFamilia").val()}, function(data, textStatus, xhr) {
        $("#familia_id").html(data);
      });
      $('#nuevaFamilia').modal('hide');
    });

    $('#nuevaFamilia').on('shown.bs.modal', function () {
      $('#nombreFamilia').val("");
      $('#nombreFamilia').focus();
    });

    $("#btnMarca").click(function() {
      $.post("{{url('marca')}}", {nombre: $("#nombreMarca").val()}, function(data, textStatus, xhr) {
        $("#marca_id").html(data);
      });
      $('#nuevaMarca').modal('hide');
    });

    $('#nuevaMarca').on('shown.bs.modal', function () {
      $('#nombreMarca').val("");
      $('#nombreMarca').focus();
    });

    $("#btnGenerarCodigo").click(function() {
      if ($("#linea_id").val() && $("#familia_id").val() && $("#marca_id").val()) {

        $.post("{{url('generar-codigo')}}",
          {
            linea_id: $("#linea_id").val(),
            familia_id: $("#familia_id").val(),
            marca_id: $("#marca_id").val()
          }, function(data, textStatus, xhr) {

          $("#codigo").val(data);
        });
      }else{
        $("#mensaje").html("<p>DEBE ESCOGER UNA LINEA, FAMILIA Y MARCA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
      }
    });

    $("#costoUnitario").keyup(function() {
      if ($("#cantidad").val()) {

        $("#costoTotal").val($(this).val()*$("#cantidad").val());
      }else {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD DEBE CONTENER UN VALOR NUMÉRICO.</p>");
        $('#errorCantidad').modal('show');
        $(this).val("");
      }
    });

    $("#costoTotal").keyup(function() {
      if ($("#cantidad").val()) {

        $("#costoUnitario").val(Math.round($(this).val()/$("#cantidad").val()));
      }else {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD DEBE CONTENER UN VALOR NUMÉRICO.</p>");
        $('#errorCantidad').modal('show');
        $(this).val("");
      }
    });

    $("#cantidad").change(function() {
      $("#costoTotal").val("");
      $("#costoUnitario").val("");
    });

    $("#codigo").keypress(function(event){
      if (event.which == 13) {
        $.post(
          "{{url('buscar-producto')}}",
          {
            codigo: $("#codigo").val()
          },
          function(data, textStatus, xhr){
            if (data['producto'] != 0) {

              $("#descripcion").val(data['producto']['descripcion']);
              var vencimiento = "";
              if (data['producto']['vencimiento']) {
                vencimiento = data['producto']['vencimiento'];
                vencimiento = vencimiento.split('/');
                vencimiento = vencimiento[2]+"-"+vencimiento[1]+"-"+vencimiento[0];
              }
              $("#vencimiento").val(vencimiento);
              $("#precio").val(data['producto']['precio']);
              $("#linea_id").html(data['linea']);
              $("#familia_id").html(data['familia']);
              $("#marca_id").html(data['marca']);
              $("#imgProducto").html(data['foto']);
            }else{

              $("#descripcion").val("");
              $("#vencimiento").val("");
              $("#precio").val("");
              $("#linea_id").html(data['linea']);
              $("#familia_id").html(data['familia']);
              $("#marca_id").html(data['marca']);
              $("#imgProducto").html(data['foto']);
            }
          }
        );
      }
    });

    $("#btnAgregarDetalle").click(function(){
      if (!$("#codigo").val()) {
        $("#mensaje").html("<p>EL CAMPO CÓDIGO ES REQUERIDO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#linea_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA LINEA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#familia_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA FAMILIA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#marca_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA MARCA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#descripcion").val()) {
        $("#mensaje").html("<p>EL CAMPO DESCRIPCION NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#precio").val()) {
        $("#mensaje").html("<p>EL CAMPO PRECIO NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#cantidad").val()) {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#costoUnitario").val()) {
        $("#mensaje").html("<p>EL CAMPO COSTO UNITARIO NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }
    });

    $("#ruc").change(function() {

      $.post("{{url('buscar-proveedor')}}", {ruc: $(this).val()}, function(data, textStatus, xhr) {
        $("#razonSocial").val(data['empresa']['nombre']);
        $("#direccion").val(data['empresa']['direccion']);
        $("#telefono").val(data['proveedor']['telefono']);
        $("#representante").val(data['proveedor']['representante']);
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
    $('.numero').mask("###", {reverse: true});
    $('.ruc').mask("99999999999", {reverse: true});
    $('.telefono').mask("999999999", {reverse: true});

  });
</script>
