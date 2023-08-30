function loadPage(){
    let general = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",general.switchs);
    }
    const tablaProveedor = document.querySelector("#tablaProveedores");
    const tablaProveedorDataTable = $(tablaProveedor).DataTable({
        ajax: {
            url: 'proveedores/listar',
            method: 'POST',
            headers: general.requestJson
        },
        columns: [
        {
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'tipo_documento.documento'
        },
        {
            data: 'nro_documento'
        },
        {
            data: 'nombre_proveedor'
        },
        {
            data: 'telefono'
        },
        {
            data: 'celular'
        },
        {
            data: 'correo'
        },
        {
            data: 'estado',
            render:function(data){
                return data ? `<span class="badge badge-success">Vigente</span>` : `<span class="badge badge-danger">Descontinuado</span>`
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-proveedor="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-proveedor="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        }
        ]
    });
    const listaContactos = document.querySelector("#listaContactosProveedor");
    let idProveedorModificar = null;
    document.querySelector("#btnGuardarFrm").onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formProveedor = document.querySelector("#formProveedor");
    formProveedor.addEventListener("submit",function(e){
        e.preventDefault();
        $('#agregarProveedor').modal("hide");
        setTimeout(function(){
            $('#agregarProveedorContacto').modal("show");
        },500);
    });
    const switchEstado = document.querySelector("#idModalestado");
    const formlarioContacto = document.querySelector("#formProveedorContactos");
    document.querySelector("#btnAbrirNuevoProveedor").onclick = e => {
        formProveedor.reset();
        switchEstado.checked = true;
        idProveedorModificar = null;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        listaContactos.innerHTML = "";
    }
    tablaProveedor.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            general.cargandoPeticion(e.target, general.claseSpinner, true);
            try{
                const response = await general.funcfetch("proveedores/listar/"+e.target.dataset.proveedor,null,"GET");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                idProveedorModificar = e.target.dataset.proveedor;
                listaContactos.innerHTML = "";
                for (const key in response.proveedor) {
                    if (Object.hasOwnProperty.call(response.proveedor, key)) {
                        const element = response.proveedor[key];
                        const dom = document.querySelector("#idModal"+key);
                        if(key == "estado"){
                            switchEstado.checked = element;
                            switchEstado.parentElement.querySelector("label").textContent = element ? "VIGENTE" : "DESCONTINUADO";
                            continue;
                        }
                        if(key == "contactos"){
                            element.forEach(c => {
                                renderListaContactos(c.id,c.nombres_completos,c.correo,c.celular,c.telefono);
                            });
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = element;
                    }
                }
                $('#agregarProveedor').modal("show");

            }catch(error){
                alertify.alert("Error","Ocurrió un error al mostrar el proveedor, por favor intentelo más tarde");
                console.error(error);
            }finally{
                general.cargandoPeticion(e.target, "fas fa-pencil-alt", false);
            }
        }
        if(e.target.classList.contains("btn-outline-danger")){
            alertify.confirm("Alerta","¿Deseas eliminar este proveedor?",async ()=>{
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                try {
                    const response = await general.funcfetch("proveedores/eliminar/"+e.target.dataset.proveedor,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    tablaProveedorDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    alertify.alert("Error","Ocurrió un error al eliminar al proveedor, por favor intentelo más tarde");
                    console.error(error);
                }finally{
                    general.cargandoPeticion(e.target, "fas fa-trash-alt", false);
                }
            },()=>{})
        }
    })
    formlarioContacto.addEventListener("submit",async function(e){
        e.preventDefault();
        let formData = new FormData(formProveedor);
        if(idProveedorModificar){
            formData.append("idProveedor",idProveedorModificar);
        }
        let formDataContactos = new FormData(this);
        for (const pair of formDataContactos.entries()) {
            formData.append(pair[0],pair[1]);
        }
        try {
            const response = await general.funcfetch("proveedores/crear",formData,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            alertify.success(response.success);
            tablaProveedorDataTable.draw();
            $('#agregarProveedorContacto').modal("hide");
            formProveedor.reset();
            listaContactos.innerHTML = "";
        } catch (error) {
            console.error(error);
            alertify.alert("Error","Ocurrió un error al agregar un proveedor, por favor vuelva a intentar más tarde");
        }
    });
    document.querySelector("#btnGuardarFrmContactos").onclick = e => document.querySelector("#btnFrmEnviarContactos").click();
    listaContactos.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-danger")){
            const liParent = e.target.parentElement;
            if(liParent.dataset.tipo == "actualizar"){
                try {
                    alertify.confirm("Alerta","¿Estás seguro de eliminar a este contacto?",async ()=>{
                        let datos = new FormData();
                        datos.append("proveedor",idProveedorModificar);
                        datos.append("contacto",liParent.dataset.contacto);
                        const response = await general.funcfetch("proveedores/contacto/eliminar",datos,"POST");
                        if(response.session){
                            return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                        }
                        alertify.success(response.success);
                        liParent.remove();
                    },()=>{})
                } catch (error) {
                    console.error(error);
                    return alertify.alert("Error","Ocurrió un error al intentar eliminar a este contacto, por favor vuelva a intentar más tarde");
                }
            }else{
                liParent.remove();
            }
        }
    })
    let contadorContactoAgregar = 1;
    $('#btnAtrasFrmContacto').on('click',function(e){
        $('#agregarProveedorContacto').modal("hide");
        setTimeout(function(){
            $('#agregarProveedor').modal("show");
        },500);
    });
    $('#agregarProveedor').on('hidden.bs.modal',function(e){
        // idProveedorModificar = null;
    });
    const btnContacto = document.querySelector("#agregarContacto");
    btnContacto.onclick = e => renderListaContactos(null,"","","","");
    function renderListaContactos(id,nombres,correo,celular,telefono) {
        const lista = document.createElement("li");
        lista.dataset.tipo = !id ? "agregar" : "actualizar";
        if(id){
            lista.dataset.contacto = id;
        }
        let valores = [
            {label: 'Nombres',tipo:'text',name:"nombres_c[]",id: !id ? "contacto_agregar_nombre_" + contadorContactoAgregar : "contacto_agregar_nombre_" + id,required:true,valor:nombres,col:"col-12"},
            {label: 'Correo',tipo:'email',name:"correo_c[]",id: !id ? "contacto_agregar_correo_" + contadorContactoAgregar : "contacto_agregar_correo_" + id,required:false,valor:correo,col:"col-12 col-md-6"},
            {label: 'Celular',tipo:'tel',name:"celular_c[]",id: !id ? "contacto_agregar_celular_"+contadorContactoAgregar: "contacto_agregar_celular_" + id,required:false,valor:celular,col:"col-12 col-md-3"},
            {label: 'Teléfono',tipo:'tel',name:"telefono_c[]",id: !id ? "contacto_agregar_telefono_"+contadorContactoAgregar : "contacto_agregar_telefono_" + id,required:false,valor:telefono,col:"col-12 col-md-3"},
        ]
        let contenidoRow = document.createElement("div");
        contenidoRow.className = "form-row";
        valores.forEach(v => {
           const box = document.createElement("div");
           box.className = "form-group " + v.col;
           const label = document.createElement("label");
           label.setAttribute("for",v.id);
           label.textContent = v.label;
           const input =  document.createElement("input");
           input.className = "form-control form-control-sm";
           input.id = v.id;
           input.type = v.tipo;
           input.name = v.name;
           input.required = v.required;
           input.value = v.valor;
           box.append(label,input);
           contenidoRow.append(box);
        });
        const inputId = document.createElement("input");
        inputId.type = "hidden";
        inputId.value = id;
        inputId.name = "id_c[]";
        const btnEliminar = document.createElement("button");
        btnEliminar.type = "button";
        btnEliminar.className = "btn btn-sm btn-outline-danger";
        btnEliminar.innerHTML = `<i class="fas fa-trash"></i>`;
        lista.append(inputId,contenidoRow,btnEliminar);
        listaContactos.append(lista);
        contadorContactoAgregar++;
    }
    const btnExportarDatos = document.querySelectorAll('.exportar-datos');
    for (const btn of btnExportarDatos) {
        btn.addEventListener("click",function (e) {
            const enlace = document.createElement("a");
            enlace.href = general.url + '/compras/proveedores/reportes/' + e.target.dataset.type;
            enlace.target = "_blank";
            document.body.appendChild(enlace);
            enlace.click();
            enlace.remove();             
        })
    }
}
window.addEventListener("DOMContentLoaded",loadPage);