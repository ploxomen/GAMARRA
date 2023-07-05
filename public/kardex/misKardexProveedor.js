function loadPage(params) {
    let gen = new General();
    const tablaProveedores = document.querySelector("#tablaProveedores");
    const tablaProveedoresDatatable = $(tablaProveedores).DataTable({
        ajax: {
            url: 'proveedores/listar',
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
            data : 'nroKardexProveedor'
        },
        {
            data: 'nombre_proveedor'
        },
        {
            data: 'nombreCliente'
        },
        {
            data : 'estado',
            render : function(data){
                let estado = "";
                switch (data) {
                    case 1:
                        estado = '<span class="badge badge-success">Generado</span>';
                    break;
                    case 2:
                        estado = '<span class="badge badge-dager">Facturado</span>';
                    break;
                }
                return estado;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-producto="${data}">
                    <small>
                        <i class="fas fa-pencil-alt"></i>
                        Editar
                    </small>
                </button>
                <a href="proveedores/reporte/${row.idKardex}/${row.idProveedor}" target="_blank" class="btn btn-sm btn-outline-danger p-1" data-producto="${data}">
                    <small>
                        <i class="far fa-file-pdf"></i>                        
                        Guía
                    </small>
                </a></div>`
            }
        },
        ]
    });
}
window.addEventListener("DOMContentLoaded",loadPage);