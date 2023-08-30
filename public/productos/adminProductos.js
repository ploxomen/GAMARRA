function loadPage(){
    let gen = new General();
    for (const cambioCantidad of document.querySelectorAll('.cambiar-cantidad')) {
        cambioCantidad.addEventListener("click",gen.aumentarDisminuir);
    }
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaProducto = document.querySelector("#tablaProductos");
    const $cbSubfamilia = document.querySelector("#idModalfamiliaSubId");
    // const $cbArticulos = document.querySelector("#idModalarticulo");
    const tablaProductoDatatable = $(tablaProducto).DataTable({
        ajax: {
            url: 'producto/listar',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [{
            data: 'productoId',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data : 'productoCodigo'
        },
        {
            data: 'productoNombre'
        },
        {
            data: 'familiaNombre'
        },
        {
            data: 'familiaSubNombre'
        },
        // {
        //     data: 'articuloNombre'
        // },
        {
            data: 'precioVenta',
            render : function(data){
                return gen.resetearMoneda(data)
            }
        },
        {
            data : 'productoEstado',
            render : function(data){
                if(data === 1){
                    return '<span class="badge badge-success">Vigente</span>';
                }else if(data === 0){
                    return '<span class="badge badge-danger">Descontinuado</span>';
                }else{
                    return '<span class="text-danget">No establecido</span>';
                }
            }
        },
        {
            data: 'productoId',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-producto="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-producto="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idProducto = null;
    const prevImagen = document.querySelector("#imgPrevio");
    document.querySelector("#customFileLang").onchange = function(e){
        let reader = new FileReader();
        reader.onload = function(){
            prevImagen.src = reader.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formProducto = document.querySelector("#formProducto");
    formProducto.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idProducto ? "producto/editar/" + idProducto : "producto/crear",datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.success(response.success);
            tablaProductoDatatable.draw();
            $('#agregarProducto').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un producto");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const checkEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloProducto");
    $('#agregarProducto').on("hidden.bs.modal",function(e){
        idProducto = null;
        modalTitulo.textContent = "Crear Producto";
        checkEstado.disabled = true;
        checkEstado.checked = true;
        checkEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        document.querySelector("#customFileLang").value = "";
        formProducto.reset();
        $('#agregarProducto .select2-simple').val("").trigger("change");
        $cbSubfamilia.innerHTML = "";
        // $cbArticulos.innerHTML = "";
        prevImagen.src = window.origin + "/img/imgprevproduc.png";
    });
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaProducto.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("producto/listar/" + e.target.dataset.producto,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar Producto";
                idProducto = e.target.dataset.producto;
                for (const key in response.producto) {
                    if (Object.hasOwnProperty.call(response.producto, key)) {
                        const valor = response.producto[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "listaFamiliaSub"){
                            gen.renderSubfamilias(valor,$cbSubfamilia,response.producto.familiaSubId);
                            continue;
                        }
                        // if(key == "listaArticulos"){
                        //     gen.renderArticulos(valor,$cbArticulos,response.producto.articuloId);
                        //     continue;
                        // }
                        if (key == "productoEstado"){
                            checkEstado.checked = valor === 1 ? true : false;
                            checkEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
                            continue;
                        }
                        if((!dom || !valor) && key != 'urlProductos'){
                            continue;
                        }
                        if(key == "urlProductos"){
                            if (valor){
                                prevImagen.src = valor;
                            }
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarProducto .select2-simple').trigger("change");
                checkEstado.disabled = false;
                $('#agregarProducto').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el producto");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este producto?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("producto/eliminar/" + e.target.dataset.producto, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    tablaProductoDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el usuario");
                }
            },()=>{});
            
        }
    })
    const btnExportarDatos = document.querySelectorAll('.exportar-datos');
    for (const btn of btnExportarDatos) {
        btn.addEventListener("click",function (e) {
            const enlace = document.createElement("a");
            enlace.href = gen.url + '/almacen/producto/reportes/' + e.target.dataset.type;
            enlace.target = "_blank";
            document.body.appendChild(enlace);
            enlace.click();
            enlace.remove();             
        })
    }
    $('#idModalfamiliaId').on("select2:select",async function(e){
        try {
            // $cbArticulos.innerHTML = "";
            const response = await gen.funcfetch("producto/familia/" + $(this).val(), null, "GET");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if (response.success) {
                gen.renderSubfamilias(response.success,$cbSubfamilia);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener las subfamilias");
        }
    });
    // $('#idModalfamiliaSubId').on("select2:select",async function(e){
    //     try {
    //         const response = await gen.funcfetch("producto/subfamilia/" + $(this).val(), null, "GET");
    //         if (response.session) {
    //             return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
    //         }
    //         if(response.alerta){
    //             return alertify.alert("Alerta",response.alerta);
    //         }
    //         if (response.success) {
    //             gen.renderArticulos(response.success,$cbArticulos);
    //         }
    //     } catch (error) {
    //         console.error(error);
    //         alertify.error("error al obtener los articulos");
    //     }
    // });
}
window.addEventListener("DOMContentLoaded",loadPage);

