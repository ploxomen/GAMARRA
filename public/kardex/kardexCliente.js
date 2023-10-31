function loadPage() {
    let gen = new General();
    const tablaKardex = document.querySelector("#tablaClientes");
    const tablaKardexDatatable = $(tablaKardex).DataTable({
        ajax: {
            url: 'clientes/listar',
            method: 'GET',
            headers: gen.requestJson,
            data: function (d) {
                d.cliente = $("#cbClientes").val();
                d.fechaInicio = $("#txtFechaInicio").val();
                d.fechaFin = $("#txtFechaFin").val();
            }
        },
        columns: [
            {
                data: 'nro_kardex'
            },
            {
                data: 'fecha_kardex'
            },
            {
                data: 'nombreCliente'
            },
            {
                data: 'nro_fardo'
            },
            
            {
                data : 'cantidad'
            },
            {
                data : 'nombreProducto'
            }
        ]
    });
    document.querySelector("#btnBuscar").addEventListener("click",function(e){
        e.preventDefault();
        tablaKardexDatatable.draw();
    });
    $('#cbClientes').on('select2:select',function(e){
        tablaKardexDatatable.draw();
    });
    for (const btnExportar of document.querySelectorAll('.btn-exportar')) {
        btnExportar.addEventListener("click",exportar);
    }
    function exportar(e) {
        e.preventDefault();
        if($('#cbClientes').val() == ""){
            return alertify.alert("Alerta","Por favor seleccione un cliente");
        }
        window.open(window.origin + '/intranet/almacen/kardex/general/clientes/reporte/' + e.target.dataset.accion + "?fechaInicio=" + $("#txtFechaInicio").val() + "&fechaFin=" + $("#txtFechaFin").val() + "&cliente=" + $('#cbClientes').val());
    }
   
}
window.addEventListener("DOMContentLoaded",loadPage);