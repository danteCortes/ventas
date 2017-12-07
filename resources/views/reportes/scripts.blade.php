<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
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
      /* poner el focus en el input de busqueda */
      $("#tblProductos-header > div > div > div.search.form-group > div > input").focus();
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $(".codigo").empty();
        $(".descripcion").empty();
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".codigo").html(data['producto']['codigo']);
          $(".descripcion").html(data['familia'][1]['nombre']+" "+data['marca'][1]['nombre']+" "+data['producto']['descripcion']);
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
            $(".codigo").html(data['producto']['codigo']);
            $(".descripcion").html(data['producto']['descripcion']);
            $(".linea").html(data['producto']['linea']['nombre']);
            $(".familia").html(data['producto']['familia']['nombre']);
            $(".marca").html(data['producto']['marca']['nombre']);
            $(".tienda").html(data['tienda']['nombre']);
            $("#detalles-kardex").empty();
            detalles = "<tr class='info'><td style='width:10%;'>"+data['inicio']+"</td><td style='width:39%;'>SALDO ANTERIOR</td>"+
              "<td style='width:7%;'></td><td style='width:7%;'></td><td style='width:7%;'></td>"+
              "<td style='width:7%;'></td><td style='width:7%;'></td><td style='width:7%;'></td>"+
              "<td style='text-align:right; width:7%;'>"+data['saldo_anterior']+"</td><td style='text-align:right; width:7%;'>"+
              data['producto']['precio']+
              "</td><td style='text-align:right; width:7%;'>"+(data['producto']['precio']*data['saldo_anterior']).toFixed(2)+"</td></tr>";
            $("#detalles-kardex").append(detalles);
            cantidad = data['saldo_anterior'];
            $.each(data['detalles'], function(clave, valor) {
              unitario = valor['unitario'];
              if (unitario == null) {
                unitario = 0;
              }
              detalles = "<tr><td>"+moment(valor['fecha']).format('DD/MM/YYYY')+"</td><td>"+valor['detalle']+"</td>";
              if (valor['tipo'] == 1) {
                cantidad = cantidad + valor['cantidad'];
                detalles = detalles + "<td style='text-align:right'>"+valor['cantidad']+"</td><td style='text-align:right'>"+
                unitario.toFixed(2)+"</td><td style='text-align:right'>"+(valor['cantidad']*unitario).toFixed(2)+"</td>"+
                "<td></td><td></td><td></td>";
              }else {
                cantidad = cantidad - valor['cantidad'];
                detalles = detalles + "<td></td><td></td><td></td>"+
                "<td style='text-align:right'>"+valor['cantidad']+"</td><td style='text-align:right'>"+
                unitario.toFixed(2)+"</td><td style='text-align:right'>"+(valor['cantidad']*unitario).toFixed(2)+"</td>";
              }
              detalles = detalles + "<td style='text-align:right'>"+cantidad+"</td><td style='text-align:right'>"+data['producto']['precio']+
                "</td><td style='text-align:right'>"+(data['producto']['precio']*cantidad).toFixed(2)+"</td></tr>";
              $("#detalles-kardex").append(detalles);
            });
            detalles = "<tr class='success'><td></td><td>INVENTARIO FINAL</td>"+
              "<td></td><td></td><td></td>"+
              "<td></td><td></td><td></td>"+
              "<td style='text-align:right'>"+cantidad+"</td><td style='text-align:right'>"+data['producto']['precio']+
              "</td><td style='text-align:right'>"+(data['producto']['precio']*cantidad).toFixed(2)+"</td></tr>";
            $("#detalles-kardex").append(detalles);
            $("#inventario").addClass('oculto');
            $("#fichaVentas").addClass('oculto');
            $("#fichaKardex").removeClass('oculto');
            $("#frmKardex").modal('hide');
        });
      }
    });

    $("#btnBuscarVentas").click(function() {
      if ($("#inicio_ventas").val() != "" && $("#fin_ventas").val() != "" && $("#tienda_ventas").val() != "") {

        $.post("{{url('reporte/ventas')}}",
          {
            inicio: $("#inicio_ventas").val(),
            fin: $("#fin_ventas").val(),
            tienda_id: $("#tienda_ventas").val()
          },
          function(data, textStatus, xhr) {
            $("#resumen-ventas").html(data);
            $("#inventario").addClass('oculto');
            $("#fichaKardex").addClass('oculto');
            $("#fichaVentas").removeClass('oculto');
            $("#frmVentas").modal('hide');
        });
      }
    });

    $("#btnBuscarInventario").click(function(){
      if ($("#tienda_inventario").val() != "") {
        $.post("{{url('reporte/inventario')}}", {tienda_id: $("#tienda_inventario").val()},
          function(data, textStatus, xhr) {
            // Llenamos el encabezado.
            $(".tienda").html(data['tienda']['nombre']);
            $(".ruc").html(data['tienda']['ruc']);
            $(".direccion").html(data['tienda']['direccion']);
            // Agregamos los datos de los productos.
            $("#detalles-inventario").empty();
            $.each(data['productos'], function(clave, valor) {
              detalles = "<tr>"+
                "<td>"+valor['codigo']+"</td>"+
                "<td>"+valor['descripcion']+"</td>"+
                "<td style='text-align:right;'>"+valor['cantidad']+"</td>"+
                "<td style='text-align:right;'>"+valor['precio'].toFixed(2)+"</td>"+
                "<td style='text-align:right;'>"+(valor['precio']*valor['cantidad']).toFixed(2)+"</td>"+
              "</tr>";
              $("#detalles-inventario").append(detalles);
            });
            $("#fichaKardex").addClass('oculto');
            $("#fichaVentas").addClass('oculto');
            $("#inventario").removeClass('oculto');
            $("#frmInventario").modal('hide');
          }
        );
      }
    });

    $("#btnBuscarCierre").click(function(){
      if ($("#tienda_cierre").val() != ""  && $("#fecha_cierre").val() != "") {

        $.post("{{url('reporte/cierre')}}", {tienda_id: $("#tienda_cierre").val(), fecha: $("#fecha_cierre").val()},
          function(data, textStatus, xhr) {
            // Llenamos el encabezado.
            $("#resumen-cierres").empty();
            $("#resumen-cierres").html(data);
            $("#frmCierres").modal("hide");

          }
        );

      }
    });

    $("#imprimir-inventario").click(function (){
      $("#imprimirInventario").printArea();
    });

    $("#imprimir-kardex").click(function (){
      $("#imprimirKardex").printArea();
    });

    $("#imprimir-ventas").click(function (){
      $("#imprimirVentas").printArea();
    });

  });
</script>
