function loadPage() {
    let gen = new General();
    const tablaKardex = document.querySelector("#tablaKardex");
    const tablaKardexDatatable = $(tablaKardex).DataTable({
        ajax: {
            url: 'todos/listar',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [
        {
            data: 'nroKardex'
        },
        {
            data: 'fechaKardex'
        },
        {
            data: 'cantidad'
        },
        {
            data : 'kilaje'
        },
        {
            data : 'estado',
            render : function(data){
                let estado = "";
                switch (data) {
                    case 1:
                        estado = '<span class="badge badge-danger">En curso</span>';
                    break;
                    case 2:
                        estado = '<span class="badge badge-success">Generado</span>';
                    break;
                }
                return estado;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-info p-1" data-kardex-proveedor="${data}">
                    <small>
                        <i class="fas fa-pencil-alt"></i>
                        Editar
                    </small>
                </button>
                <button class="btn btn-sm reporte-clientes btn-outline-danger p-1" data-kardex="${data}">
                    <small>
                        <i class="fas fa-eye"></i>
                        Clientes
                    </small>
                </button>
                <a href="reportes/facturacion/${data}" target="_blank" class="btn btn-sm btn-outline-success p-1">
                    <small>
                        <i class="far fa-file-excel"></i>                        
                        Pre factura cliente
                    </small>
                </a>
                <a href="reportes/packing/${data}" target="_blank" class="btn btn-sm btn-outline-success p-1">
                    <small>
                        <i class="far fa-file-excel"></i>                        
                        Packing List
                    </small>
                </a>
                </div>`
            }
        },
        ]
    });
    const tablaInfoClientes = document.querySelector("#contenidoClienteKardex");
    tablaKardex.addEventListener("click",async function(e){
        if(e.target.classList.contains("reporte-clientes")){
            try {
                const response = await gen.funcfetch("cliente/reporte/" + e.target.dataset.kardex,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                let template = "";
                response.success.forEach((cliente,key) => {
                    template += `<tr>
                        <td>${key + 1}</td>
                        <td>${cliente.nombreCliente}</td>
                        <td class="text-center  ">
                            <a href="cliente/reporte/kardex/${e.target.dataset.kardex}/${cliente.id_cliente}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="far fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>`
                });
                tablaInfoClientes.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se encontraron clientes</td></tr>` : template;
                $('#verClientesKardex').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los clientes del kardex");
            }
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);