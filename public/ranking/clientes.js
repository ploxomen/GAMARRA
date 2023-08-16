function loadPage(){
    let general = new General();
    const tablaCliente = document.querySelector("#tablaClientes");
    const txtFechaInicio = document.querySelector("#txtFechaInicio");
    const txtFechaFin = document.querySelector("#txtFechaFin");

    const tablaClienteDataTable = $(tablaCliente).DataTable({
        ajax: {
            url: 'clientes/listar',
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
                data: 'pais_espanish'
            },
            {
                data: 'nombreCliente'
            },
            {
                data: 'kilajes'
            }
        ]
    });
    document.querySelector("#btnBuscar").addEventListener("click",function(e){
        e.preventDefault();
        tablaClienteDataTable.draw();
    });
    
}
window.addEventListener("DOMContentLoaded",loadPage);