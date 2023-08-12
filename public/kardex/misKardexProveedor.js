function loadPage() {
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
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-info p-1" data-detalle="${data}" data-kardex-cliente="${row.idCliente}" data-kardex-proveedor="${row.idProveedor}" data-kardex="${row.idKardex}">
                    <small>
                        <i class="fas fa-pencil-alt"></i>
                        Editar
                    </small>
                </button>
                <a href="proveedores/reporte/${row.idKardex}/${row.idProveedor}/${row.idCliente}" target="_blank" class="btn btn-sm btn-outline-danger p-1" data-kardex-proveedor="${data}">
                    <small>
                        <i class="far fa-file-pdf"></i>                        
                        Guía
                    </small>
                </a></div>`
            }
        },
        ]
    });
    let kardexProveedor = null;
    let tablaDetalleProveedor = document.querySelector("#tablaDetalleProveedor");
    tablaProveedores.onclick = async function (e) {
        if (e.target.classList.contains("btn-outline-info")) {
            try {
                const response = await gen.funcfetch("proveedores/listar/" + e.target.dataset.kardex + '/' + e.target.dataset.kardexProveedor + '/' + e.target.dataset.kardexCliente, null, "GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.alerta) {
                    return alertify.alert("Mensaje",response.alerta);
                }
                kardexProveedor = e.target.dataset.detalle;
                tablaDetalleProveedor.innerHTML = "";
                for (const key in response.success) {
                    if (Object.hasOwnProperty.call(response.success, key)) {
                        const valor = response.success[key];
                        const $dom = document.querySelector("#idModal" + key);
                        if (key == "lista") {
                            let template = "";
                            valor.forEach(detalleProducto => {
                                template += `
                                <tr>
                                    <td><input type="hidden" name="idDetalle[]" value="${detalleProducto.id}">${detalleProducto.cantidad}</td>
                                    <td>${detalleProducto.presentacion}</td>
                                    <td>${detalleProducto.nombreProducto}</td>
                                    <td style="width:50px;"><input type="number" step="0.01" class="form-control form-control-sm" value="${detalleProducto.importe}" name="importe[]"></td>
                                </tr>
                                `
                            });
                            tablaDetalleProveedor.innerHTML = !template ? `<tr>
                            <td colspan="100%" class="text-center">No se encontraron detalles</td>
                            </tr>` : template;
                            continue;
                        }
                        if (!$dom) {
                            continue;
                        }
                        $dom.value = valor;
                    }
                }
                $("#editarKardexProveedor").modal("show");
            } catch (error) {
                console.error(error);
            }
        }
    }
    document.querySelector("#btnGuardar").onclick = e => document.querySelector("#btnEnviar").click();
    const frmKardexProvee = document.querySelector("#frmKardexProveedor");
    frmKardexProvee.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("proveedor",kardexProveedor);
        try {
            const response = await gen.funcfetch("proveedores/actualizar", datos, "POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            }
            if (response.alerta) {
                return alertify.alert("Mensaje",response.alerta);
            }
            frmKardexProvee.reset();
            $("#editarKardexProveedor").modal("hide");
            return alertify.success(response.success);
        } catch (error) {
            alertify.error("error al actualizar los datos del kardex del proveedor");
            console.error(error);
        }
    })


}
window.addEventListener("DOMContentLoaded",loadPage);