@extends('plantillas.administrador')

@section('estilos')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.css"
        integrity="sha512-WBBdLBZSQGm9JN1Yut45Y9ijfFANbcOX3G+/A5+oO8W2ZWASp3NkPrG8mgr8QvGviyLoAz8y09l7SJ1dt0as7g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"
    />
@stop

@section('contenido')
    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-sm btn-primary" onclick="mdlNuevoComprobante()">
                <i class="fa fa-plus"> Nuevo Comprobante</i>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-bordered" id="tblComprobantes">
                    <thead>
                        <tr class="info">
                            <th data-column-id="serie_numero">SERIE Y NUMERO</th>
                            <th data-column-id="fecha_emision" data-order="desc">FECHA EMISION</th>
                            <th data-column-id="documento_cliente">DOCUMENTO CLIENTE</th>
                            <th data-column-id="cliente">DENOMINACION CLIENTE</th>
                            <th data-column-id="total" data-align="right">TOTAL</th>
                            <th data-column-id="estado">ESTADO</th>
                            <th data-column-id="commands" data-align="center" data-formatter="commands" data-sortable="false">Operaciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('emisionComprobantes.modals.mdlNuevoComprobante')
    @include('emisionComprobantes.modals.mdlMostrarComprobante')
    @include('emisionComprobantes.modals.mdlAnularComprobante')
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bootgrid/1.3.1/jquery.bootgrid.min.js" 
        integrity="sha512-ut+jq2MDKjyWKK7rpEbyHjJ2kDBDO58DLFw4xJobqvS2kUgx4DJbj3OLjwk4F0pKtcxUoUIRS6esQVhh4fmWNA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
    <script src="/sistema/emisionComprobantes/inicio.js"></script>
@stop