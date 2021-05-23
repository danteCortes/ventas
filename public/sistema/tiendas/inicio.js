const mdlSubirCertificadoDigital = async (id) => {
    try{
        let config = {
            method: 'GET',
            url: `/mdl-subir-certificado-digital?id=${id}`
        }
        let response = await axios(config)

        $("#nombre-tienda").html(response.data.nombre)
        $("#txt-nombre-certificado").val('')
        $("#hdn-id-tienda").val(response.data.id)
        $("#fle-certificado-digital").val('')
        $("#txt-password-certificado").val('')
        $("#txt-usuario-sunat").val('')
        $("#txt-clave-sunat").val('')

        $("#mdlSubirCertificadoDigital").modal('show')
    }catch(errors){console.log(errors)}
}

const mostrarNombre = () => {
    let certificado = $("#fle-certificado-digital")[0].files
    if(certificado.length > 0){

        let nombre = certificado[0].name
        let extension = nombre.split('.')
        extension = extension[extension.length - 1]

        if(extension == 'pfx'){
            
            $("#txt-password-certificado").val('')
            $("#txt-nombre-certificado").val(nombre)
        }else{

            $("#txt-nombre-certificado").val('')
            $("#txt-password-certificado").val('')
            $("#fle-certificado-digital").val('')
            toastr.error('El tipo de archivo no es el correcto.')
        }
    }else{
        $("#txt-nombre-certificado").val('')
        $("#fle-certificado-digital").val('')
    }
}

const subirCertificadoDigital = async () => {
    $("#busySubirCertificadoDigital").busyLoad("show", {
        background: "#4569ab",
        spinner: "cube",
        animation: "slide",
        text: "Subiendo Certificado Digital..."
    })
    try{
        let certificado = $("#fle-certificado-digital")[0].files
        if(certificado.length > 0){

            let data = new FormData
            data.append('id', $("#hdn-id-tienda").val())
            data.append('certificado_digital', certificado[0])
            data.append('password_certificado', $("#txt-password-certificado").val())
            data.append('usuario_sunat', $("#txt-usuario-sunat").val())
            data.append('clave_sunat', $("#txt-clave-sunat").val())
            let config = {
                method: 'POST',
                url: `/subir-certificado-digital`,
                responseType: 'blob',
                data
            }
            let response = await axios(config)
    
            $("#mdlSubirCertificadoDigital").modal('hide')
            $("#busySubirCertificadoDigital").busyLoad("hide")
            toastr.success('El Certificado Digital fue guardado con éxito.')
                
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            const f = new Date();
            link.setAttribute('download', 'certificado_sunat.cer');
            document.body.appendChild(link);
            link.click();
        }else{
            toastr.error('Debe escoger un Certificado Digital')
        }

    }catch(errors){
        if(errors.response.status != 422){
            toastr.error('Hubo un error en el sistema, comuníquese con el administrador del sistema')
        }else{
            errors.response.data.map(error => toastr.error(error))
        }
    }
}
