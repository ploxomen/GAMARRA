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
            data: 'importe',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'factura_sunat'
        },
        {
            data : 'guia_remision_sunat'
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
                        estado = '<span class="badge badge-info">Generado</span>';
                    break;
                    case 3:
                        estado = '<span class="badge badge-success">Facturado</span>';
                    break;
                    case 4:
                        estado = '<span class="badge badge-primary">Facturado con Guía R.</span>';
                    break;
                }
                return estado;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                const btnFactura = row.estado >= 2 ? `
                <a class="dropdown-item facturar-cliente" href="javascript:void(0)" data-kardex="${data}">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Generar Factura</span>
                </a>` 
                : "";
                const btnGuiaRemision = row.estado >= 2 ? `
                <a class="dropdown-item guia-remision-cliente" href="javascript:void(0)" data-kardex="${data}">
                    <i class="fas fa-car"></i>
                    <span>Generar Guía Remision</span>
                </a>` 
                : "";
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm reporte-clientes btn-danger p-1" data-kardex="${data}">
                    <i class="fas fa-eye"></i>
                    <span>Clientes</span>
                </button>
                <a href="reportes/packing/${data}" target="_blank" class="btn btn-sm btn-success p-1">
                    <i class="far fa-file-excel"></i>                        
                    <span>Packing List</span>
                </a>
                <div class="dropdown lista-noicons">
                    <a class="btn btn-light btn-sm dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item editar-kardex" href="javascript:void(0)" data-kardex="${data}" data-tasa="${row.tasa_extranjera}" data-aduanero="${row.id_aduanero}">
                            <i class="fas fa-pencil-alt"></i>
                            <span>Editar Kardex</span>
                        </a>
                        <a href="reportes/facturacion/${data}" target="_blank" class="dropdown-item">
                            <i class="far fa-file-excel"></i>
                            <span>Pre Factura</span>
                        </a>
                        ${btnFactura}
                        ${btnGuiaRemision}
                        <a class="dropdown-item eliminar-kardex" href="javascript:void(0)" data-kardex="${data}">
                            <i class="fas fa-trash"></i>
                            <span>Eliminar Kardex</span>
                        </a>
                    </div>
                </div>
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
    const tablaDetalleGuiaRemitente = document.querySelector("#generarGuiaRemision #tablaProductos");
    const txtTotalCantidadGuiaRemitente = document.querySelector("#generarGuiaRemision #modalCantidadTotal");
    const bloqueCredito = document.querySelector("#generarFactura #bloqueCredito");

    const frmKardex = document.querySelector("#frmDatosKardex");
    const txtTasaExtranjera = document.querySelector("#idModaltasa_extranjera");
    let idKardex = null;
    const tablaDetalleProducto = document.querySelector("#generarFactura #tablaProductos");
    let validoFacturar = true;
    let totalFacturar = 0;
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
        if(e.target.classList.contains("eliminar-kardex")){
            alertify.confirm("Alerta",'¿Desea eliminar el kardex?',async ()=>{
                try {
                    const datos = new FormData();
                    const response = await gen.funcfetch("eliminar/" + e.target.dataset.kardex,datos,"PUT");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    if (response.success) {
                        alertify.success(response.success);
                        tablaKardexDatatable.draw();
                    }
                } catch (error) {
                    console.error(error);
                    alertify.error("no se pudo eliminar el kardex, por favor intentelo nuevamente dentro de unos minutos");
                }
            },()=>{})
        }
        if(e.target.classList.contains("facturar-cliente")){
            try {
                const response = await gen.funcfetch("facturar/" + e.target.dataset.kardex,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.informacionFactura) {
                    idKardex = e.target.dataset.kardex;
                    for (const key in response.informacionFactura) {
                        if (Object.hasOwnProperty.call(response.informacionFactura, key)) {
                            const valor = response.informacionFactura[key];
                            const dom = document.querySelector("#generarFactura #modal" + key);
                            if(key == "listaProductos"){
                                let template = "";
                                let total = 0;
                                valor.forEach((producto,k) => {
                                    const subTotal = producto.precio * producto.totalCantidades;
                                    template += `
                                    <tr>
                                        <td>${k + 1}</td>
                                        <td>${producto.nombreProducto}</td>
                                        <td>${producto.totalCantidades}</td>
                                        <td>${gen.resetearMoneda(producto.precio)}</td>
                                        <td>${gen.resetearMoneda(subTotal)}</td>
                                    <tr>
                                    `
                                    total += subTotal;
                                });
                                tablaDetalleProducto.innerHTML = template;
                                totalFacturar = Number.parseFloat(total).toFixed(2);
                                if(Number.parseFloat(total).toFixed(2) !== Number.parseFloat(response.informacionFactura.importe).toFixed(2)){
                                    alertify.alert("Alerta","El monto total generado no coincide con el importe acomulado de los detalles del producto");
                                    validoFacturar = false;
                                };
                                continue;
                            }
                            if(key === "totalLetras" || key === "importe"){
                                dom.textContent = key === "importe" ? gen.resetearMoneda(valor) : valor;
                                continue;
                            }
                            if(!dom){
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $("#generarFactura .select2-simple").trigger("change");
                    $("#generarFactura").modal("show");
                }
            } catch (error) {
                console.error(error);
                alertify.error("no se pudo eliminar el kardex, por favor intentelo nuevamente dentro de unos minutos");
            }
        }
        if(e.target.classList.contains("guia-remision-cliente")){
            try {
                const response = await gen.funcfetch("facturar/guia-remision/" + e.target.dataset.kardex,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.informacionFactura) {
                    idKardex = e.target.dataset.kardex;
                    for (const key in response.informacionFactura) {
                        if (Object.hasOwnProperty.call(response.informacionFactura, key)) {
                            const valor = response.informacionFactura[key];
                            const dom = document.querySelector("#generarGuiaRemision #modal" + key);
                            if(key == "listaProductos"){
                                let template = "";
                                let cantidadTotal = 0;
                                valor.forEach((producto,k) => {
                                    template += `
                                    <tr>
                                        <td>${k + 1}</td>
                                        <td>${producto.nombreProducto}</td>
                                        <td>${producto.id_presentacion}</td>
                                        <td>${producto.totalCantidades}</td>
                                    <tr>
                                    `
                                    cantidadTotal += parseFloat(producto.totalCantidades);
                                });
                                tablaDetalleGuiaRemitente.innerHTML = template;
                                txtTotalCantidadGuiaRemitente.textContent = cantidadTotal;
                                continue;
                            }
                            if(!dom){
                                continue;
                            }
                            dom.value = valor;
                        }
                    }
                    $("#generarGuiaRemision").modal("show");
                }
            } catch (error) {
                console.error(error);
                alertify.error("no se pudo eliminar el kardex, por favor intentelo nuevamente dentro de unos minutos");
            }
        }
        if(e.target.classList.contains("editar-kardex")){
            idKardex = e.target.dataset.kardex;
            $('#idModaladuanero').val(e.target.dataset.aduanero).trigger("change");
            $('#editarKardex').modal("show");
            txtTasaExtranjera.value = e.target.dataset.tasa;
            return
        }
    });
    const $txtFechaEmision = document.querySelector("#generarFactura #modalFechaEmision");
    const $tablaCreditosFactura = document.querySelector("#generarFactura #tablaCreditos");
    const $sinCuotas = `
    <tr>
        <td colspan="100%" class="text-center">No se asignaron cuotas</td>
    </tr>
    `
    let numeroCuotasFactura = 0;
    document.querySelector("#generarFactura #btnFacturar").onclick = e => document.querySelector("#generarFactura #inputFacturar").click();
    document.querySelector("#generarGuiaRemision #btnFacturar").onclick = e => document.querySelector("#generarGuiaRemision #inputFacturar").click();
    const formFacturar = document.querySelector("#generarFactura #formFacturar");
    const formGuiaRemitente = document.querySelector("#generarGuiaRemision #formGuiaRemitente");
    formGuiaRemitente.addEventListener("submit",async function(e){
        e.preventDefault();
        alertify.confirm("Alerta","Estas apunto de generar una Guía de Remision Remitente<br>¿Deseas continuar de todas formas?",async ()=>{
            try {
                gen.banerLoader.hidden = false;
                let datos = new FormData(formGuiaRemitente);
                datos.append("kardex",idKardex);
                const response = await gen.funcfetch("facturar/guia-remision",datos,"POST");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.error) {
                    return alertify.alert("Alerta",response.error);
                }
                if(response.urlPdf){
                    const pdf = document.createElement("a");
                    pdf.href = response.urlPdf;
                    pdf.target = "_blank";
                    document.body.append(pdf);
                    pdf.click();
                    document.body.removeChild(pdf);
                }
                $('#generarGuiaRemision').modal("hide");
                tablaKardexDatatable.draw();
                return alertify.alert("Mensaje",response.success);
            } catch (error) {
                alertify.alert("Alerta","Ocurrió un error al generar la factura, por favor intentelo nuevamente más tarde");
                console.error(error);
            }finally{
                gen.banerLoader.hidden = true;
            }
        },()=>{})
    });
    formFacturar.addEventListener("submit",async function(e){
        e.preventDefault();
        if(!validoFacturar){
            return alertify.alert("Alerta","El monto total generado no coincide con el importe acomulado de los detalles del producto");
        }
        if(document.querySelector("#generarFactura #tipoACredito:checked")){
            if(numeroCuotasFactura <= 0 ){
                return alertify.error("debe haber al menos un detalle de credito");
            }
            let montoCredito = 0;
            Array.from($tablaCreditosFactura.children).forEach((tr,key) => {
                if(!tr.querySelector("input[type='date']").value){
                    return alertify.error("debe establecer la fecha limite de la fila " + (key + 1));
                }
                for (let i = (key + 1); i < $tablaCreditosFactura.children.length; i++) {
                    if(tr.querySelector("input[type='date']").value === $tablaCreditosFactura.children[i].querySelector("input[type='date']").value){
                        return alertify.error("las fechas de los creditos no deben ser iguales cambie la fecha de la fila " + (key + 1) + " o de la fila " + (i + 1));
                    }
                }
                if(!tr.querySelector("input[type='number']").value.trim()){
                    return alertify.error("debe establecer el monto de la fila " + (key + 1));
                }
                montoCredito += Number.parseFloat(tr.querySelector("input[type='number']").value);
            });
            if(montoCredito.toFixed(2) !== totalFacturar){
                return alertify.error("el monto acumulado de los creditos que actualmente es "+ gen.resetearMoneda(montoCredito.toFixed(2)) + " debe ser igual a " + gen.resetearMoneda(totalFacturar));
            }
        }
        alertify.confirm("Alerta","Estas apunto de facturar <strong>" + gen.resetearMoneda(totalFacturar) + "</strong><br>¿Deseas continuar de todas formas?",async ()=>{
            try {
                gen.banerLoader.hidden = false;
                let datos = new FormData(formFacturar);
                datos.append("kardex",idKardex);
                const response = await gen.funcfetch("facturar",datos,"POST");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.error) {
                    return alertify.alert("Alerta",response.error);
                }
                if(response.urlPdf){
                    const pdf = document.createElement("a");
                    pdf.href = response.urlPdf;
                    pdf.target = "_blank";
                    document.body.append(pdf);
                    pdf.click();
                    document.body.removeChild(pdf);
                }
                $('#generarFactura').modal("hide");
                tablaKardexDatatable.draw();
                return alertify.alert("Mensaje",response.success);
            } catch (error) {
                alertify.alert("Alerta","Ocurrió un error al generar la factura, por favor intentelo nuevamente más tarde");
                console.error(error);
            }finally{
                gen.banerLoader.hidden = true;
            }
        },()=>{})
    });
    for (const tipoFactura of document.querySelectorAll(".cambio-tipo-factura")) {
        tipoFactura.addEventListener("change",function(e){
            if(e.target.value === "Contado"){
                $tablaCreditosFactura.innerHTML = $sinCuotas;
                bloqueCredito.hidden = true;
                numeroCuotasFactura = 0;
                return false;
            }
            bloqueCredito.hidden = false;
        })
    }
    $tablaCreditosFactura.addEventListener("click",function(e){
        if(e.target.classList.contains("btn-danger")){
            numeroCuotasFactura--;
            e.target.parentElement.parentElement.remove();
            Array.from($tablaCreditosFactura.children).forEach((tr,key) => {
                tr.children[0].textContent = key + 1;
            });
            alertify.success("cuota eliminada correctamente");
        }
    })
    function agregarCuotaFactura() {
        if(numeroCuotasFactura <= 0){
            $tablaCreditosFactura.innerHTML = "";
        }
        numeroCuotasFactura++;
        const $fechaLimite = document.createElement("input");
        const $monto = document.createElement("input");
        const $btnEliminar = document.createElement("button");
        const $tr = document.createElement("tr");
        $fechaLimite.type = "date";
        $monto.type = "number";
        $fechaLimite.setAttribute("required","required");
        $monto.setAttribute("required","required");
        $fechaLimite.name = "cuotasFacturaFecha[]";
        $monto.name = "cuotasFacturaMonto[]";
        $fechaLimite.className = 'form-control form-control-sm';
        $monto.className = 'form-control form-control-sm';
        $fechaLimite.min = $txtFechaEmision.value;
        $monto.step = "0.01";
        $monto.min = "0";
        $btnEliminar.className = 'btn btn-sm btn-danger';
        $btnEliminar.innerHTML = `<i class="fas fa-trash-alt"></i>`
        $tr.innerHTML = `
        <td>${numeroCuotasFactura}</td>
        <td>${$fechaLimite.outerHTML}</td>
        <td>${$monto.outerHTML}</td>
        <td class="text-center">${$btnEliminar.outerHTML}</td>
        `
        return $tr;
    }
    $('#generarFactura').on("hidden.bs.modal",function(e){
        formFacturar.reset();
        numeroCuotasFactura = 0;
        validoFacturar = true;
        idKardex = null;
        bloqueCredito.hidden = false;
        $tablaCreditosFactura.innerHTML = $sinCuotas;
    });
    document.querySelector("#generarFactura #btnAgregarCuotaFactura").addEventListener("click",()=>{
        $tablaCreditosFactura.append(agregarCuotaFactura());
    })
    const frmTasas = document.querySelector("#formTasasKardex");
    $('#idCliente').on("select2:select",function(e){
        document.querySelector("#idModaltasa").value = "";
        kardex.obtenerKardexPendiente($(this).val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo,idKardex);
    })
    $('#editarKardex').on("hidden.bs.modal",function(e){
        frmTasas.reset()
        idKardex = null;
        frmKardex.reset();
        $('#idModaladuanero').val("").trigger("change");
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
    // $('#idModaladuanero').on("select2:selecting",async function(e){
    //     try {
    //         let datos = new FormData();
    //         datos.append("idKardex",idKardex);
    //         datos.append("aduanero",$(this).val());
    //         const response = await gen.funcfetch("actualizar/aduanero",datos,"POST");
    //         if(response.session){
    //             return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
    //         }
    //         if(response.alerta){
    //             return alertify.alert("Alerta",response.alerta);
    //         }
    //         alertify.success(response.success);
    //     } catch (error) {
    //         console.error(error);
    //         alertify.error("error al actualizar el agente de aduanas");
    //     }
    // });
    frmTasas.addEventListener("submit",async function(e){
        e.preventDefault();
        // if($('#idCliente').val() == ""){
        //     return alertify.error("por favor seleccione un cliente");
        // }
        try {
            let datos = new FormData(this);
            datos.append("idKardex",idKardex);
            datos.append("cliente",$('#idCliente').val());
            const response = await gen.funcfetch("actualizar/tasa",datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            alertify.success(response.success);
            tablaKardexDatatable.draw();
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar las tasas");
        }
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