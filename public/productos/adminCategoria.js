function loadPage(){
    const general = new General();
    const tablaMarca = document.querySelector("#tablaMarca");
    const tablaMarcaDataTable = $(tablaMarca).DataTable({
        ajax: {
            url: 'categorias/listar',
            method: 'GET',
            headers: general.requestJson,
            data: function (d) {
                // d.accion = 'obtener';
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'nombreCategoria'
        },
        {
            data: 'tasaCategoria'
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
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-categoria="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-categoria="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const txtnombreCategoria = document.querySelector("#txtnombreCategoria");
    const txttasaCategoria = document.querySelector("#txttasaCategoria");

    const formCategoria = document.querySelector("#formCategoria");
    const btnGuardarForm = document.querySelector("#btnGuardarForm");
    const checkEstado = document.querySelector("#customSwitch1");
    let idCategoria = null;
    tablaMarca.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("categorias/listar/" +e.target.dataset.categoria,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    alertify.success("pendiente para editar");
                    const categoria = response.success;
                    txtnombreCategoria.value = categoria.nombreMarca;
                    txttasaCategoria.value = categoria.tasaCategoria;
                    idCategoria = categoria.id;
                    checkEstado.checked = categoria.estado;
                    btnGuardarForm.querySelector("span").textContent = "Editar";
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idCategoria = null;
                console.error(error);
                alertify.error("error al obtener la categoria")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta marca?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("categorias/eliminar/" + e.target.dataset.categoria,null,"DELETE");
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
                    tablaMarcaDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar la marca');
                }
            },() => {})
        }
    }
    formCategoria.onreset = function(e){
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = 0;
        idCategoria = null;
    }
    formCategoria.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idCategoria != null ? "categorias/editar/" + idCategoria : 'categorias/crear';
        try {
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            const response = await general.funcfetch(url, datos, "POST");
            general.cargandoPeticion(btnGuardarForm, 'fas fa-save', false);
            if (response.session) {
                return alertify.alert([...alertaSesion], () => { window.location.reload() });
            }
            if (response.success) {
                alertify.success(response.success);
                tablaMarcaDataTable.draw();
                formCategoria.reset();
                idCategoria = null;
            }
        } catch (error) {
            idCategoria = null;
            console.error(error);
            alertify.error(idCategoria != null ? "error al editar la marca" : 'error al agregar la marca')
        }

    }
}
window.addEventListener("DOMContentLoaded",loadPage);