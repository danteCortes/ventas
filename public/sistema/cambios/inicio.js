$(function() {
    
    var grid = $("#tblVentas").bootgrid({
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
        url: "listar-ventas",
        formatters: {
            "commands": function(column, row){
                return `<button type='button' class='btn btn-xs btn-info' title='imprimir Recibo' onclick='mdlImprimirRecibo(${row.id})'
                    style='margin:2px'>
                    <span class='fa fa-eye'></span>
                </button>
                <button type='button' class='btn btn-xs btn-danger' title='Anular Venta' onclick='mdlAnularVenta(${row.id})'
                    style='margin:2px'>
                    <span class='fa fa-trash'></span>
                </button>`
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function(){
        /* poner el focus en el input de busqueda */
    });

    $('#verTicket').on('hidden.bs.modal', function (e) {
        $("#ifr-recibo").attr('src', '')
    })
})

const mdlImprimirRecibo = (id) => {

    $("#ifr-recibo").attr('src', `/imprimir-recibo/${id}`)
    $("#verTicket").modal("show");
}

const mdlAnularVenta = async (id) => {
    try{
        let config = {
            method: 'GET',
            url: `mdl-anular-venta?id=${id}`
        }
        let response = await axios(config)
        
        $("#numeracion-anular").text(response.data.numeracion)
        $("#tipo-anulacion").text(response.data.tipo_anulacion)
        $("#id-venta-anular").val(response.data.id)
        $("#mdlAnularVenta").modal("show")
    }catch(errors){
        if(errors.response.status == 422){
            errors.response.data.map(error => toastr.error(error))
        }else{
            toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema.')
        }
    }
}

const anularVenta = async () => {
    try{
        let config = {
            method: 'POST',
            url: '/anular-venta',
            data: {
                id: $("#id-venta-anular").val()
            }
        }
        let response = await axios(config)

        toastr.info('La venta fue anulada con éxito.')
        $("#mdlAnularVenta").modal("hide")
        $("#tblVentas").bootgrid('reload')
    }catch(errors){
        if(errors.response.status == 422){
            errors.response.data.map(error => toastr.error(error))
        }else{
            toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema.')
        }
    }
}