@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Ingresar Productos
@include('consultoras.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    @include('consultoras.tblProductos')
    @include('consultoras.ingresos.nuevo.frmAgregarProducto')
  </div>
  @include('consultoras.ingresos.nuevo.tblDetalles')
  @include('consultoras.ingresos.nuevo.frmIngreso')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  <script type="text/javascript">
    $(document).ready(function() {
      $(".moneda").mask('#  ##0.00', {reverse: true});

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
            $(".descripcion").html(data['producto']['descripcion']);
            $(".precio").html(data['producto']['precio']);
            $(".precio").val(data['producto']['precio']);
            $(".cantidad").html(data['stock'][1]);
            $("#btnAgregarProducto").prop('disabled', false);
            $(".foto").html(data['foto']);
            $("#producto_codigo").val(data['producto']['codigo']);
            $("#stock").val(data['stock'][1]);
            $("#cantidad").val("1");
            $("#cantidad").focus();
          });
        });
      });
    });
  </script>
@stop
