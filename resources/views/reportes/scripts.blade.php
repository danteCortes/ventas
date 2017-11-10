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
            $(".codigo").html(data['producto']['codigo']);
            $(".descripcion").html(data['producto']['descripcion']);
            $(".linea").html(data['producto']['linea']['nombre']);
            $(".familia").html(data['producto']['familia']['nombre']);
            $(".marca").html(data['producto']['marca']['nombre']);
            $(".tienda").html(data['tienda']['nombre']);
            $("#fichaKardex").removeClass('oculto');
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
            $("#frmKardex").modal('hide');
        });
      }
    });

    $(".imprimir").click(function (){
      $("#imprimirKardex").printArea();
    });

  });
</script>
