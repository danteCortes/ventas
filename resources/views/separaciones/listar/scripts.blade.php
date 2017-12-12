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
    var grid = $("#tblSeparaciones").bootgrid({
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
      url: "{{url('separacion/listar')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<a href='modificar/"+row.id+"' class='btn btn-warning btn-xs' style='margin:2px'>"+
              "<span class='glyphicon glyphicon-edit'></span>"+
            "</a>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>"+
            "<button type='button' class='btn btn-xs btn-success command-pagar' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-money'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* poner el focus en el input de busqueda */
      $("#tblSeparaciones-header > div > div > div.search.form-group > div > input").focus();
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('separacion/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['id']);
          $(".cli_dni").html(data['persona']['dni']);
          $(".cli_nombre").html(data['persona']['nombres'] + " " + data['persona']['apellidos']);
          $(".cli_direccion").html(data['persona']['direccion']);
          $(".caj_dni").html(data['usuario']['persona']['dni']);
          $(".caj_nombres_apellidos").html(data['usuario']['persona']['nombres'] + " " + data['usuario']['persona']['apellidos']);
          $(".caj_direccion").html(data['usuario']['persona']['direccion']);
          $(".caj_telefono").html(data['usuario']['persona']['telefono']);
          $(".detalles").empty();
          $.each(data['detalles'], function(clave, valor){
            detalle = "<tr><td>"+valor['cantidad']+"</td><td>"+valor['producto']['descripcion']+"</td><td>"+valor['precio_unidad']+"</td><td>"+
              valor['total']+"</td><td>"+valor['monto_separacion'].toFixed(2)+"</td></tr>";
            $(".detalles").append(detalle);
          });
          $(".detalles").append("<tr><th colspan='3' style='text-align:rigth;'>TOTAL</th><th>"+data['total'].toFixed(2)+"</th><th>"+data['separacion_total'].toFixed(2)+"</th></tr>");
          $(".fecha_separacion").html(data['created_at']);
          $("#ver").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('separacion/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['id']);
          $("#frmEliminar").prop('action', "{{url('separacion/eliminar')}}/" + data['id']);
          $("#eliminar").modal('show');
        });
      }).end().find(".command-pagar").on('click', function(e) {
        $.post("{{url('separacion/buscar')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['id']);
          $("#frmPagar").prop('action', "{{url('separacion/pagar')}}/" + data['id']);
          $("#tblPagos").empty();
          var total_pagos = parseFloat(data['separacion_total']);
          $("#tblPagos").append("<tr><td style='border: 1px solid #398439;'>"+data['created_at']+
          "</td><td style='border: 1px solid #398439'>"+total_pagos.toFixed(2)+"</td></tr>");
          $.each(data['pagos'], function(clave, valor) {
            total_pagos += parseFloat(valor['monto']);
            pago = "<tr><td style='border: 1px solid #398439'>"+valor['created_at']+"</td><td style='border: 1px solid #398439'>"+
              valor['monto']+"</td></tr>";
            $("#tblPagos").append(pago);
          });
          var saldo = parseFloat(data['total']) - parseFloat(total_pagos);
          $(".saldo").html("S/ " + saldo.toFixed(2));
          $("#tblPagos").append("<tr><th style='text-align:right; border: 1px solid #398439;'>TOTAL</th><th style='border: 1px solid #398439'>"+
            total_pagos.toFixed(2)+"</th></tr>");
          if (saldo == 0) {
            $("#txtMonto").prop('readonly', true);
            $("#btnGuardarPago").prop('disabled', true);
          }else {
            $("#txtMonto").prop('readonly', false);
            $("#btnGuardarPago").prop('disabled', false);
          }
          $("#pagos").modal('show');
        });
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});

  });
</script>
