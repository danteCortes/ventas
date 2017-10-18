<script type="text/javascript">
  $(document).ready(function() {

    $("#frmNuevoProducto").keypress(function(event) {
      if (event.which == 13) {
        return false;
      }
    });

    $("#frmEditar").keypress(function(event) {
      if (event.which == 13) {
        return false;
      }
    });

    $(".imprimir").click(function (){
      $("div#imgBarcode").printArea();
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

    /**
     * Lista los productos llamanto por ajax al metodo lista del controlador ProveedorController.
     * Fecha 14/09/2017
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
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<button type='button' class='btn btn-xs btn-warning command-edit' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-edit'></span></button>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>"+
            "<button type='button' class='btn btn-xs btn-success command-barcode' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-barcode'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".imgMostrarProducto").html(data['foto']);
          $(".codigo").html(data['producto']['codigo']);
          $(".linea").html(data['linea'][1]['nombre']);
          $(".familia").html(data['familia'][1]['nombre']);
          $(".marca").html(data['marca'][1]['nombre']);
          $(".descripcion").html(data['producto']['descripcion']);
          $(".vencimiento").html(data['producto']['vencimiento']);
          $(".precio").html(data['producto']['precio']);
          $(".stock").html(data['stock'][0]);
          $("#ver").modal('show');
        });
      }).end().find(".command-edit").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".imgMostrarProducto").html(data['foto']);
          $(".codigo").val(data['producto']['codigo']);
          $(".linea").html(data['linea'][0]);
          $(".familia").html(data['familia'][0]);
          $(".marca").html(data['marca'][0]);
          $(".descripcion").val(data['producto']['descripcion']);
          var vencimiento = "";
          if (data['producto']['vencimiento']) {
            vencimiento = data['producto']['vencimiento'];
            vencimiento = vencimiento.split('/');
            vencimiento = vencimiento[2]+"-"+vencimiento[1]+"-"+vencimiento[0];
          }
          $(".vencimiento").val(vencimiento);
          $(".precio").val(data['producto']['precio']);
          $("#frmEditar").prop('action', "{{url('producto')}}/" + data['producto']['codigo']);
          $("#editar").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".codigo").html(data['producto']['codigo']);
          $("#frmEliminar").prop('action', "{{url('producto')}}/" + data['producto']['codigo']);
          $("#eliminar").modal('show');
        });
      }).end().find(".command-barcode").on('click', function(e) {
        $.post("{{url('imprimir-codigo')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".codigo").html(data['producto']['codigo']);
          $(".imgBarcode").html(data['codigoBarras']);
          $("#barcode").modal("show");
        });
      });;
    });

    /*
     * Genera un código para el producto a partir de la linea, familia y la marca
     * que se le propone antes de generar.
     * Se envian esos datos a la función generarCodigo del contolador ProductoController por un método post.
     * Fecha 13/09/2017
     */
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
        $("#nuevo").modal('hide');
        $("#mensaje").html("<p>DEBE ESCOGER UNA LINEA, FAMILIA Y MARCA PARA EL PRODUCTO.</p>");
        $('#errores').modal('show');
      }
    });

    /*
     * Llama al método create del controlador LineaController por medio del metodo post
     * de ajax para guardar una nueva linea.
     * Fecha 13/09/2017
    */
    $("#btnLinea").click(function() {
      $.post("{{url('linea')}}", {nombre: $("#nombreLinea").val()}, function(data, textStatus, xhr) {
        $("#linea_id").html(data);
      });
      $('#nuevaLinea').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva Linea y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaLinea').on('shown.bs.modal', function () {
      $('#nombreLinea').val("");
      $('#nombreLinea').focus();
    });

    /*
     * Llama al método create del controlador FamiliaaController por medio del metodo post
     * de ajax para guardar una nueva familia.
     * Fecha 13/09/2017
    */
    $("#btnFamilia").click(function() {
      $.post("{{url('familia')}}", {nombre: $("#nombreFamilia").val()}, function(data, textStatus, xhr) {
        $("#familia_id").html(data);
      });
      $('#nuevaFamilia').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva familia y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaFamilia').on('shown.bs.modal', function () {
      $('#nombreFamilia').val("");
      $('#nombreFamilia').focus();
    });

    /*
     * Llama al método create del controlador MarcaController por medio del metodo post
     * de ajax para guardar una nueva marca.
     * Fecha 13/09/2017
    */
    $("#btnMarca").click(function() {
      $.post("{{url('marca')}}", {nombre: $("#nombreMarca").val()}, function(data, textStatus, xhr) {
        $("#marca_id").html(data);
      });
      $('#nuevaMarca').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva marca y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaMarca').on('shown.bs.modal', function () {
      $('#nombreMarca').val("");
      $('#nombreMarca').focus();
    });

    /*
     * Llama al método change del campo codigo para buscar un producto con ese codigo
     * enviandolo por ajax al método buscarProducto del controlador ProductoController.
     * Luego rellena los campor correspondientes si encuentra un producto con ese código.
     * Fecha 13/09/2017
    */
    $("#codigo").change(function(){
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
    });

    /*
     * Define una máscara de moneda para los campos necesarios con valor de moneda.
    */
    $('.moneda').mask("# ##0.00", {reverse: true});

  });
</script>
