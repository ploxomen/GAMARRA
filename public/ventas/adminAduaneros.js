function loadPage(){
    const general = new General();
    const $tablaAduanero = document.querySelector("#tablaAduanero");
    const tablaAduanero = $($tablaAduanero).DataTable({
        ajax: {
            url: 'aduaneros/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.accion = 'obtener';
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },{
            data: 'documento'
        },{
            data: 'nro_documento'
        },
        {
            data: 'nombre_completo'
        },
        {
            data: 'pais_espanish'
        },
        {
            data: 'tasa',
            render:function(data){
                return general.resetearMoneda(data);
            }
        },
        {
            data: 'principal',
            render:function(data){
                return data ? `<span class="badge badge-success">Principal</span>` : `<span class="badge badge-danger">Secundario</span>`
            }
        },
        {
            data: 'estado',
            render:function(data){
                return data ? `<span class="badge badge-success">Vigenta</span>` : `<span class="badge badge-danger">Descontinuado</span>`
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-aduanero="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-aduanero="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const formAduanero = document.querySelector("#formAduanero");
    const btnGuardarForm = document.querySelector("#btnGuardarFrm");
    const checkEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloArticulo");
    const checkPrincipal = document.querySelector("#idModalprincipal")
    let idAduanero = null;
    $tablaAduanero.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("aduaneros/listar/" +e.target.dataset.aduanero,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    idAduanero = response.success.id;
                    modalTitulo.textContent = "Editar agente de aduanas";
                    btnGuardarForm.querySelector("span").textContent ="Editar";
                    checkEstado.disabled = false;
                    for (const key in response.success) {
                        if (Object.hasOwnProperty.call(response.success, key)) {
                            const valor = response.success[key];
                            const dom = document.querySelector("#idModal" + key);
                            if (key == "principal"){
                                checkPrincipal.checked = valor === 1 ? true : false;
                                continue;
                            }
                            if (key == "estado"){
                                checkEstado.checked = valor === 1 ? true : false;
                                continue;
                            }
                            if(!dom){
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $('#agregarAduanero .select2-simple').trigger("change");
                    $('#agregarAduanero').modal("show");
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idAduanero = null;
                console.error(error);
                alertify.error("error al obtener el agente de aduanas")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta agente de aduanas?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("aduaneros/eliminar/" + e.target.dataset.aduanero,null,"DELETE");
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    if (response.error) {
                        return alertify.alert("Alerta", response.error);
                    }
                    tablaAduanero.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar el agente de aduanas');
                }
            },() => {})
        }
    }
    $('#agregarAduanero').on("hidden.bs.modal",function(e){
        idAduanero = null;
        modalTitulo.textContent = "Agregar agente de aduanas";
        checkEstado.disabled = true;
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = true;
        checkPrincipal.checked = false;
        formAduanero.reset();
    });

    btnGuardarForm.onclick = e => document.querySelector("#btnFrmEnviar").click();
    formAduanero.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idAduanero != null ? "aduaneros/editar/" + idAduanero : 'aduaneros/crear';
        try {
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            const response = await general.funcfetch(url, datos, "POST");
            general.cargandoPeticion(btnGuardarForm, 'fas fa-save', false);
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if (response.success) {
                alertify.success(response.success);
                tablaAduanero.draw();
                formAduanero.reset();
                idAduanero = null;
                $('#agregarAduanero .select2-simple').val("").trigger("change");
                $('#agregarAduanero').modal("hide");
            }
        } catch (error) {
            idAduanero = null;
            console.error(error);
            alertify.error(idAduanero != null ? "error al editar el agente de aduanas" : 'error al agregar el agente de aduanas')
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);