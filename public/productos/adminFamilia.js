function loadPage(){
    const general = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",general.switchs);
    }
    const $tablaFamilia = document.querySelector("#tablaFamilia");
    const tablaFamilia = $($tablaFamilia).DataTable({
        ajax: {
            url: 'familias/listar',
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
        },
        {
            data: 'codigo'
        },
        {
            data: 'nombre'
        }
        ,
        {
            data: 'sub_famila',
            render:function(data){
                return data.length;
            }
        }
        ,{
            data: 'estado',
            render:function(data){
                return data ? `<span class="badge badge-success">Vigenta</span>` : `<span class="badge badge-danger">Descontinuado</span>`
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-familia="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-familia="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const formFamilia = document.querySelector("#formFamilia");
    const btnGuardarForm = document.querySelector("#btnGuardarFrm");
    const checkEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloFamilia");
    let idFamilia = null;
    const listaSubfamilia = document.querySelector("#listaSubfamilia");
    const txtSinSubfamilia = document.querySelector("#txtSinSubfamilias");
    document.querySelector("#btnAgregarSubfamilia").onclick = e =>{
        listaSubfamilia.append(agregarSubfamilia(null,"",""));
        if(listaSubfamilia.children.length){
            txtSinSubfamilia.hidden = true;
        }
    }
    function agregarSubfamilia(idSubfamilia,nombre,codigo) {
        const lista = document.createElement("li");
        lista.dataset.tipo = idSubfamilia ? 'old' : 'new';
        lista.dataset.subfamilia = idSubfamilia;
        let $idSubfamilia = idSubfamilia ? `<input type="hidden" value="${idSubfamilia}" name="idSubfamilia[]">` : "";
        lista.innerHTML = 
        `<div class="form-row">
            ${$idSubfamilia}
            <div class="col-12 col-md-4 form-group">
                <input type="text" name="subfamiliaCodigo[]" required class="form-control form-control-sm" value="${codigo}" placeholder="Código">
            </div>
            <div class="col-12 col-md-7 form-group">
                <input type="text" name="subfamiliaNombre[]" required class="form-control form-control-sm" value="${nombre}" placeholder="Nombre">
            </div>
            <div class="col-12 text-rigth col-md-1 form-group">
                <button type="button" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        `
        return lista;
    }
    $tablaFamilia.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("familias/listar/" +e.target.dataset.familia,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    idFamilia = response.success.id;
                    modalTitulo.textContent = "Editar familia";
                    btnGuardarForm.querySelector("span").textContent ="Editar";
                    checkEstado.disabled = false;
                    for (const key in response.success) {
                        if (Object.hasOwnProperty.call(response.success, key)) {
                            const valor = response.success[key];
                            const dom = document.querySelector("#idModal" + key);
                            if (key == "sub_famila"){
                                valor.forEach(c => {
                                    listaSubfamilia.append(agregarSubfamilia(c.id,c.nombre,c.codigo));
                                });
                                if(listaSubfamilia.children.length){
                                    txtSinSubfamilia.hidden = true;
                                }
                            }
                            if (key == "estado"){
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
                    $('#agregarFamilias').modal("show");
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idFamilia = null;
                console.error(error);
                alertify.error("error al obtener la área")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta familia?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("familias/eliminar/" + e.target.dataset.familia,null,"DELETE");
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
                    tablaFamilia.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar la familia');
                }
            },() => {})
        }
    }
    $('#agregarFamilias').on("hidden.bs.modal",function(e){
        idFamilia = null;
        modalTitulo.textContent = "Crear familia";
        checkEstado.disabled = true;
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = true;
        checkEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formFamilia.reset();
        listaSubfamilia.innerHTML = "";
        if(!listaSubfamilia.children.length){
            txtSinSubfamilia.hidden = false;
        }
    });
    listaSubfamilia.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            const li = e.target.parentElement.parentElement.parentElement;
            if(li.dataset.tipo == "new"){
                li.remove();
                alertify.success("subfamilia eliminada");
            }else if(li.dataset.tipo == "old"){
                alertify.confirm("Mensaje","¿Estas seguro de eliminar esta subfamilia de forma permanente?",async () => {
                    try {
                        general.cargandoPeticion(e.target, general.claseSpinner, true);
                        const response = await general.funcfetch("familias/subfamilia/eliminar/" + li.dataset.subfamilia,null,"GET");
                        if (response.session) {
                            return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                        }
                        li.remove();
                        alertify.success(response.success);
                        if(!listaSubfamilia.children.length){
                            txtSinSubfamilia.hidden = false;
                        }
                        tablaFamilia.draw();
                    }catch(error){
                        console.error(error);
                        alertify.error("error al eliminar el subcontacto ")
                    }finally{
                        general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    }
                },()=>{})
            }
            if(!listaSubfamilia.children.length){
                txtSinSubfamilia.hidden = false;
            }
        }
    }
    btnGuardarForm.onclick = e => document.querySelector("#btnFrmEnviar").click();
    formFamilia.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idFamilia != null ? "familias/editar/" + idFamilia : 'familias/crear';
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
                tablaFamilia.draw();
                formFamilia.reset();
                idFamilia = null;
                $('#agregarFamilias').modal("hide");
            }
        } catch (error) {
            idFamilia = null;
            console.error(error);
            alertify.error(idFamilia != null ? "error al editar la familia" : 'error al agregar la familia')
        }

    }
}
window.addEventListener("DOMContentLoaded",loadPage);