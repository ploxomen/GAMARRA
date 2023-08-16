function loadPage(){
    let general = new General();
    const tablaAduanero = document.querySelector("#tablaAduaneros");
    const txtFechaInicio = document.querySelector("#txtFechaInicio");
    const txtFechaFin = document.querySelector("#txtFechaFin");

    const tablaAduaneroDataTable = $(tablaAduanero).DataTable({
        ajax: {
            url: 'aduaneros/listar',
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
                data: 'nombre_completo'
            },
            {
                data: 'costos',
                render : function(data){
                    return general.resetearMoneda(data);
                }
            }
        ]
    });
    document.querySelector("#btnBuscar").addEventListener("click",function(e){
        e.preventDefault();
        tablaAduaneroDataTable.draw();
    });
    
}
window.addEventListener("DOMContentLoaded",loadPage);