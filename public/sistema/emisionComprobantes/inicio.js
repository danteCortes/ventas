$(function(){

    listarComprobantes()
    
    $('#mdlMostrarComprobante').on('hidden.bs.modal', function (e) {
      $("#ifr-mostrar-comprobante").attr('src', '')
    })
})

var detalles = []
var source = null

const mdlNuevoComprobante = () => {
    detalles = []
    $("#txt-documento").val('')
    $("#txt-nombres").val('')
    $("#txt-apellidos").val('')
    $("#txt-razon-social").val('')
    $("#txt-direccion").val('')
    $("#body-detalles").html('')
    $("#mdlNuevoComprobante").modal('show')
}

const handleBuscarCliente = async () => {
    
    if($("#txt-documento").val().length == 8){
        source = null
        handleBuscarPersona()
    }else if($("#txt-documento").val().length == 11){
        source = null
        handleBuscarEmpresa()
    }else{
        source = axios.CancelToken.source()
        source.cancel('axios cancelado')
        console.log('cancelar axios')
    }
}

const handleBuscarPersona = async () => {
    try{
        let config = {
            method: 'GET',
            url: `/emision-comprobantes/buscar-persona?dni=${$("#txt-documento").val()}`,
            cancelToken: source ? source.token : source
        }
        let response = await axios(config)

        $(".datos-persona").show()
        $(".datos-empresa").hide()
        if(response.data){
            $("#txt-nombres").val(response.data.nombres)
            $("#txt-apellidos").val(response.data.apellidos)
            $("#txt-direccion").val(response.data.direccion)
        }else{
            $("#txt-nombres").val('')
            $("#txt-apellidos").val('')
            $("#txt-direccion").val('')
        }
    }catch(errors){
        if (axios.isCancel(errors)) {
          console.log('Request canceled', errors.message);
        } else {
          // handle error
        }
    }
}

const handleBuscarEmpresa = async () => {
    try{
        let config = {
            method: 'GET',
            url: `/emision-comprobantes/buscar-empresa?ruc=${$("#txt-documento").val()}`,
            cancelToken: source ? source.token : source
        }
        let response = await axios(config)

        $(".datos-persona").hide()
        $(".datos-empresa").show()
        if(response.data){
            $("#txt-razon-social").val(response.data.nombre)
            $("#txt-direccion").val(response.data.direccion)
        }else{
            $("#txt-razon-social").val('')
            $("#txt-direccion").val('')
        }
    }catch(errors){
        if (axios.isCancel(errors)) {
          console.log('Request canceled', errors.message);
        } else {
          // handle error
        }
    }
}

const calcularImporteTotal = () => {
    if(!isNaN($("#txt-detalle-cantidad").val()) && !isNaN($("#txt-detalle-valor-unitario").val())){
        
        $("#importe-total-detalle").text(parseFloat($("#txt-detalle-cantidad").val() * $("#txt-detalle-valor-unitario").val()).toFixed(2))
    }else{
        
        $("#importe-total-detalle").text('0.00')
    }
}

const agregarDetalle = () => {
    if($("#txt-detalle-descripcion").val() != '' && $("#importe-total-detalle").text() != 0){
        detalles.push({
            cantidad: $("#txt-detalle-cantidad").val(),
            descripcion: $("#txt-detalle-descripcion").val(),
            valor_unitario: $("#txt-detalle-valor-unitario").val(),
            importe_total_detalle: $("#importe-total-detalle").text()
        })
        $("#txt-detalle-cantidad").val(1)
        $("#txt-detalle-descripcion").val('')
        $("#txt-detalle-valor-unitario").val('0.00')
        $("#importe-total-detalle").text('0.00')
        llenarDetalles()
    }
}

const llenarDetalles = () => {
    html = ''
    total = 0
    detalles.map(detalle => {
        html += `<tr>
            <td class="text-center">${parseFloat(detalle.cantidad)}</td>
            <td class="text-left">${detalle.descripcion.toUpperCase()}</td>
            <td class="text-right">${parseFloat(detalle.valor_unitario).toFixed(2)}</td>
            <td class="text-right">${parseFloat(detalle.importe_total_detalle).toFixed(2)}</td>
            <td class="text-center" style="padding: 0px;">
                <button class="btn btn-sm btn-danger" onclick="quitarDetalle(${detalles.indexOf(detalle)})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`
        total += parseFloat(detalle.importe_total_detalle)
    })
    html += `<tr>
        <td colspan="3" class="text-right">TOTAL </td>
        <td class="text-right">${parseFloat(total).toFixed(2)}</td>
        <td></td>
    </tr>`
    $("#body-detalles").html(html)
}

const quitarDetalle = (index) => {    

    detalles.splice(index, 1)
    llenarDetalles()
}

const guardarComprobante = async () => {
    $("#busyNuevoComprobante").busyLoad("show", {
        background: "#4569ab",
        spinner: "cube",
        animation: "slide",
        text: "Guardando Venta de Administrador..."
    })
    try{
        let config = {
            method: 'POST',
            url: '/emision-comprobantes/guardar-comprobante',
            data: {
                cliente: {
                    documento: $("#txt-documento").val(),
                    nombres: $("#txt-nombres").val(),
                    apellidos: $("#txt-apellidos").val(),
                    razon_social: $("#txt-razon-social").val(),
                    direccion: $("#txt-direccion").val()
                },
                detalles
            }
        }
        let response = await axios(config)

        toastr.success('La venta se registró con éxito')
        $("#busyNuevoComprobante").busyLoad("hide")
        $("#mdlNuevoComprobante").modal('hide')
        $("#tblComprobantes").bootgrid('reload')
        mostrarComprobante(response.data.id)
    }catch(errors){
        $("#busyNuevoComprobante").busyLoad("hide")
        if(errors.response.status == 422){
            errors.response.data.map(error => toastr.error(error))
        }else{
            toastr.error('Hubo un error en el sistema, contacte con el administrador del sistema.')
        }
    }
}

const mostrarComprobante = async (id) => {
  
    $("#ifr-mostrar-comprobante").attr('src', `/emision-comprobantes/mostrar-comprobante?id=${id}`)
    $("#mdlMostrarComprobante").modal("show")
}

const listarComprobantes = async () => {
    
    $("#tblComprobantes").bootgrid({
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
                '_token': $("meta[name='csrf-token']").attr('content'),
                id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
            }
        },
        url: "emision-comprobantes/listar-comprobantes",
        formatters: {
            "commands": function(column, row){
                return `<button type='button' class='btn btn-xs btn-info' style='margin:2px' onclick="mostrarComprobante(${row.id})">
                    <span class='fa fa-file'></span>
                </button>
                <button type='button' class='btn btn-xs btn-danger' style='margin:2px' onclick="mdlAnularComprobante(${row.id})">
                    <span class='fa fa-trash'></span>
                </button>`
            }
        }
    })
}

const mdlAnularComprobante = async (id) => {
    try{
        let config = {
            method: 'GET',
            url: `emision-comprobantes/mdl-anular-comprobante?id=${id}`
        }
        let response = await axios(config)
        
        $("#numeracion-anular").text(response.data.numeracion)
        $("#tipo-anulacion").text(response.data.tipo_anulacion)
        $("#id-comprobante-anular").val(response.data.id)
        $("#mdlAnularComprobante").modal("show")
    }catch(errors){
        if(errors.response.status == 422){
            errors.response.data.map(error => toastr.error(error))
        }else{
            toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema.')
        }
    }
}

const anularComprobante = async () => {
    $("#busyAnularComprobante").busyLoad("show", {
        background: "#4569ab",
        spinner: "cube",
        animation: "slide",
        text: "Anulando Comprobante..."
    })
    try{
        let config = {
            method: 'POST',
            url: 'emision-comprobantes/anular-comprobante',
            data: {
                id: $("#id-comprobante-anular").val()
            }
        }
        let response = await axios(config)

        toastr.info('El comprobante fue anulado con éxito.')
        $("#tblComprobantes").bootgrid('reload')
        $("#mdlAnularComprobante").modal("hide")
        $("#busyAnularComprobante").busyLoad("hide")
    }catch(errors){
        if(errors.response.status == 422){
            errors.response.data.map(error => toastr.error(error))
        }else{
            toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema.')
        }
        $("#busyAnularComprobante").busyLoad("hide")
    }
}