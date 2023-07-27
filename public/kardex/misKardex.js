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
                <button class="btn btn-sm btn-outline-info editar-kardex p-1" data-kardex="${data}">
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
    let kardex = new Kardex();
    const tablaInfoClientes = document.querySelector("#contenidoClienteKardex");
    const tableDetalleKardex = document.querySelector("#tablaDetalle");
    const txtProveedor = document.querySelector("#idProveedor");
    const txtProducto = document.querySelector("#idProducto");
    const txtCantidad = document.querySelector("#idCantidad");
    const txtPresentacion = document.querySelector("#idPresentacion");
    const txtFardoActivo = document.querySelector("#txtFardoActivo");
    const frmKardex = document.querySelector("#frmDatosKardex");
    let idKardex = null;
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
        if(e.target.classList.contains("editar-kardex")){
            idKardex = e.target.dataset.kardex;
            $('#editarKardex').modal("show");
            return
        }
    })
    $('#idCliente').on("select2:select",function(e){
        kardex.obtenerKardexPendiente($(this).val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo,idKardex);
    })
    $('#editarKardex').on("hidden.bs.modal",function(e){
        idKardex = null;
        frmKardex.reset();
        $('#editarKardex .select2-simple').val("").trigger("change");
        txtFardoActivo.textContent = "Ninguno";
        tableDetalleKardex.innerHTML = `<tr>
            <td colspan="100%" class="text-center">No se agregaron detalles</td>
        </tr>`
    });
    frmKardex.addEventListener("submit",function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("idKardex",idKardex);
        kardex.agregarFardo(datos,txtProveedor,txtProducto,txtPresentacion,txtCantidad,tableDetalleKardex,txtFardoActivo);
    })
    document.querySelector("#cerrarFardo").onclick = function(){
        let datos = new FormData();
        datos.append('cliente',$('#idCliente').val());
        datos.append('idKardex',idKardex);
        kardex.cerrarFardo(datos,txtFardoActivo,tableDetalleKardex);
    }
    tableDetalleKardex.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar este fardo?",async ()=>{
                $('#tablaDetalle .select2-simple').select2("destroy");
                let datos = new FormData();
                datos.append('cliente',$('#idCliente').val())
                datos.append('idKardex',idKardex);
                datos.append('fardo',e.target.parentElement.parentElement.parentElement.dataset.fardo);
                const response = await kardex.eliminarFardo(datos,txtFardoActivo,tableDetalleKardex);
                if(response.success){
                    alertify.success(response.success);
                    kardex.obtenerKardexPendiente($('#idCliente').val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo,idKardex);
                    return false
                }
                return alertify.alert("Mensaje",response.alerta);
            },()=>{})
        }
        if(e.target.classList.contains("btn-primary")){
            let datos = new FormData();
            datos.append('cliente',$('#idCliente').val());
            datos.append('idKardex',idKardex);
            kardex.cerrarFardo(datos,txtFardoActivo,tableDetalleKardex);
        }
        if(e.target.classList.contains("btn-success")){
            let datos = new FormData();
            datos.append('cliente',$('#idCliente').val())
            datos.append('idKardex',idKardex);
            datos.append('fardo',e.target.parentElement.parentElement.parentElement.dataset.fardo);
            kardex.abrirFardo(datos,txtFardoActivo,tableDetalleKardex,e.target);
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);