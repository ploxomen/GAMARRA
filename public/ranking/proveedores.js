function loadPage(){
    let general = new General();
    const tablaProveedores = document.querySelector("#tablaProveedores");
    const txtFechaInicio = document.querySelector("#txtFechaInicio");
    const txtFechaFin = document.querySelector("#txtFechaFin");

    const tablaProveedoresDataTable = $(tablaProveedores).DataTable({
        ajax: {
            url: 'proveedores/listar',
            method: 'POST',
            headers: general.requestJson,
            data : function(d){
                d.fechaInicio = txtFechaInicio.value;
                d.fechaFin = txtFechaFin.value;
            }
        },
        order: [
            [3, 'desc']
        ],
        columns: [
            {
                data: 'id',
                render: function(data,type,row, meta){
                    return meta.row + 1;
                }
            },
            {
                data: 'nombre_proveedor'
            },
            {
                data: 'nombreProducto'
            },
            {
                data: 'cantidades'
            }
        ]
    });
    document.querySelector("#btnBuscar").addEventListener("click",function(e){
        e.preventDefault();
        tablaProveedoresDataTable.draw();
    });   
}
window.addEventListener("DOMContentLoaded",loadPage);