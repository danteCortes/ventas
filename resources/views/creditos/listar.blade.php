@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Creditos
<a href="{{url('credito')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('listar-creditos')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="table-responsive">
        <table class="table table-condensed table-hover table-bordered" id="tblCreditos">
          <thead>
            <tr style="background-color:#385a94; color:#FFF;">
              <th data-column-id="id" data-order="desc" style="text-align:center;">NÚMERO</th>
              <th data-column-id="cliente">CLIENTE</th>
              <th data-column-id="fecha_credito">FECHA CREDITO</th>
              <th data-column-id="fecha_cobro">FECHA COBRO</th>
              <th data-column-id="total">TOTAL</th>
              <th data-column-id="commands" data-formatter="commands" data-sortable="false">OPERACIONES</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('creditos.modales')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
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
    var grid = $("#tblCreditos").bootgrid({
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
      url: "{{url('listar-creditos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<a href='modificar-credito/"+row.id+"' class='btn btn-warning btn-xs' style='margin:2px'>"+
              "<span class='glyphicon glyphicon-edit'></span>"+
            "</a>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-credito')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['credito']['id']);
          $(".cli_dni").html(data['cliente']['dni']);
          $(".cli_nombre").html(data['cliente']['nombres'] + " " + data['cliente']['apellidos']);
          $(".cli_direccion").html(data['cliente']['direccion']);
          $(".caj_dni").html(data['cajero']['dni']);
          $(".caj_nombres_apellidos").html(data['cajero']['nombres'] + " " + data['cajero']['apellidos']);
          $(".caj_direccion").html(data['cajero']['direccion']);
          $(".caj_telefono").html(data['cajero']['telefono']);
          $(".detalles").empty();
          $.each(data['detalles'], function(clave, valor){
            detalle = "<tr><td>"+valor['cantidad']+"</td><td>"+valor['producto']['descripcion']+"</td><td>"+valor['precio_unidad']+"</td><td>"+
              valor['total']+"</td></tr>";
            $(".detalles").append(detalle);
          });
          $(".detalles").append("<tr><th colspan='3' style='text-align:rigth;'>TOTAL</th><th>"+data['credito']['total']+"</th></tr>");
          $(".fecha_credito").html(data['credito']['created_at']);
          $(".fecha_cobrar").html(data['credito']['fecha']);
          $("#ver").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-credito')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".numero").html(data['credito']['id']);
          $("#frmEliminar").prop('action', "{{url('eliminar-credito')}}/" + data['credito']['id']);
          $("#eliminar").modal('show');
        });
      });
    });



  });
</script>
@stop
