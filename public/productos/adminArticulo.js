function loadPage(){
    const general = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",general.switchs);
    }
    const $tablaArticulo = document.querySelector("#tablaArticulo");
    const tablaArticulo = $($tablaArticulo).DataTable({
        ajax: {
            url: 'articulos/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.accion = 'obtener';
            }
        },
        columns: [{
            data: 'articuloId',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'articuloCodigo'
        },
        {
            data: 'articuloNombre'
        }
        ,
        {
            data: 'familaNombre'
        }
        ,
        {
            data: 'familiSubNombre'
        }
        ,{
            data: 'articuloEstado',
            render:function(data){
                return data ? `<span class="badge badge-success">Vigente</span>` : `<span class="badge badge-danger">Descontinuado</span>`
            }
        },
        {
            data: 'articuloId',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-articulo="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-articulo="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const formArticulo = document.querySelector("#formArticulo");
    const btnGuardarForm = document.querySelector("#btnGuardarFrm");
    const checkEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloArticulo");
    const $cbSubfamilia = document.querySelector("#idModalfamiliaSubId");
    let idArticulo = null;
    $tablaArticulo.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("articulos/listar/" +e.target.dataset.articulo,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    idArticulo = response.success.articuloId;
                    modalTitulo.textContent = "Editar artículo";
                    btnGuardarForm.querySelector("span").textContent ="Editar";
                    checkEstado.disabled = false;
                    for (const key in response.success) {
                        if (Object.hasOwnProperty.call(response.success, key)) {
                            const valor = response.success[key];
                            const dom = document.querySelector("#idModal" + key);
                            if(key == "listaFamiliaSub"){
                                general.renderSubfamilias(valor,$cbSubfamilia);
                                $($cbSubfamilia).val(response.success.familiaSubId).trigger("change");
                                continue;
                            }
                            if (key == "articuloEstado"){
                                checkEstado.checked = valor === 1 ? true : false;
                                checkEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
                                continue;
                            }
                            if(!dom){
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $('#agregarArticulo .select2-simple').trigger("change");
                    $('#agregarArticulo').modal("show");
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idArticulo = null;
                console.error(error);
                alertify.error("error al obtener el artículo")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta artículo?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("articulos/eliminar/" + e.target.dataset.articulo,null,"DELETE");
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
                    tablaArticulo.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar la articulo');
                }
            },() => {})
        }
    }
    $('#agregarArticulo').on("hidden.bs.modal",function(e){
        idArticulo = null;
        modalTitulo.textContent = "Agregar Artículo";
        checkEstado.disabled = true;
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = true;
        checkEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formArticulo.reset();
        $cbSubfamilia.innerHTML = "";
    });

    btnGuardarForm.onclick = e => document.querySelector("#btnFrmEnviar").click();
    formArticulo.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idArticulo != null ? "articulos/editar/" + idArticulo : 'articulos/crear';
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
                tablaArticulo.draw();
                formArticulo.reset();
                idArticulo = null;
                $('#agregarArticulo .select2-simple').val("").trigger("change");
                $('#agregarArticulo').modal("hide");
            }
        } catch (error) {
            idArticulo = null;
            console.error(error);
            alertify.error(idArticulo != null ? "error al editar la articulo" : 'error al agregar la articulo')
        }
    }
    $('#idModalfamiliaId').on("select2:select",async function(e){
        try {
            const response = await general.funcfetch("articulos/familia/" + $(this).val(), null, "GET");
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if (response.success) {
                general.renderSubfamilias(response.success,$cbSubfamilia);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener las subfamilias");
        }
    });
    
}
window.addEventListener("DOMContentLoaded",loadPage);