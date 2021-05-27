<div class="fade modal" id="mdlSubirCertificadoDigital">
    <div class="modal-dialog" id="busySubirCertificadoDigital">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Subir Certificado Digital de la Tienda <strong class="text-primary" id="nombre-tienda"></strong>
                </h4>
            </div>
            <div class="modal-body">
                <p class="text-justify">El certificado digital deber tener la extensión .pfx</p>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="txt-nombre-certificado" class="col-sm-3 control-label">Cert. Digital:</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" onclick="$('#fle-certificado-digital').trigger('click')">
                                        <i class="fa fa-search"> Buscar</i>
                                    </button>
                                </span>
                                <input type="text" class="form-control" id="txt-nombre-certificado" disabled
                                    placeholder="Buscar Certificado Digital">
                            </div>
                            <input type="file" id="fle-certificado-digital" accept=".pfx" onchange="mostrarNombre()"
                                style="display: none;">
                            <input type="hidden" id="hdn-id-tienda">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txt-password-certificado" class="col-sm-3 control-label">Pass. Cert. Digital:</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control input-sm" id="txt-password-certificado" 
                                placeholder="Password de Certificado Digital" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txt-fecha-vencimiento" class="col-sm-3 control-label">Fech. de Venc.:</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control input-sm" id="txt-fecha-vencimiento" 
                                placeholder="Fecha de Vencimiento">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txt-usuario-sunat" class="col-sm-3 control-label">Usuario SUNAT:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control input-sm" id="txt-usuario-sunat" placeholder="Usuario SUNAT"
                                style="text-transform: uppercase;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txt-clave-sunat" class="col-sm-3 control-label">Clave SUNAT:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control input-sm" id="txt-clave-sunat" placeholder="Clave SUNAT">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal">
                    <i class="fa fa-ban"> Cerrar</i>
                </button>
                <button class="btn btn-sm btn-primary" onclick="subirCertificadoDigital()">
                    <i class="fa fa-upload"> Subir</i>
                </button>
            </div>
        </div>
    </div>
</div>