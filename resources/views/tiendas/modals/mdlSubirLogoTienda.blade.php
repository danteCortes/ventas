<div class="modal fade" id="mdlSubirLogoTienda">
    <div class="modal-dialog" id="busySubirLogoTienda">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Subir Logo de Tienda <strong id="nombre-tienda-subir-logo"></strong></h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <div class="col-sm-2">
                            <div id="imagen-default" style="border: 1px solid; border-radius: 5px; width: 100px; height: 100px;"></div>
                            <img class="img-responsive" id="mostrar-imagen" alt="Responsive image" style="display: none;">
                        </div>
                        <div class="col-sm-9 col-sm-offset-1">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-info" onclick="$('#fle-logo').trigger('click')"
                                        style="height: 33px; padding: 0px 5px;">
                                        <i class="fa fa-search"> Buscar Logo</i>
                                    </button>
                                </span>
                                <input type="text" class="form-control" id="txt-nombre-logo" disabled placeholder="Buscar Logo..."
                                    style="height: 33px !important;">
                            </div>
                            <input type="file" class="form-control" id="fle-logo" onchange="mostrarNombreLogo()"
                                accept=".png, .jpg, .jpeg" style="display: none;">
                            <input type="hidden" id="txt-id-tienda">
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal">
                    <i class="fa fa-ban"> Cerrar</i>
                </button>
                <button class="btn btn-sm btn-primary" onclick="subirLogoTienda()">
                    <i class="fa fa-upload"> Subir</i>
                </button>
            </div>
        </div>
    </div>
</div>