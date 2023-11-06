class Kardex{
    general = new General();
    txtSinFardos = `<tr><td colspan="100%" class="text-center">No se agregaron detalles</td></tr>`
    async cerrarFardo(datosKardex,txtFardoActivo,tableDetalleKardex,mensaje = true){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/cerrar",datosKardex,"POST");
            if(response.alerta){
                // alertify.alert("Mensaje",response.alerta);
            }
            if(response.success || response.fardo){
                txtFardoActivo.textContent = 'Ninguno';
                const brnCerrar = tableDetalleKardex.querySelector('.btn-primary');
                if(brnCerrar){
                    brnCerrar.querySelector('i').classList.replace("fa-door-open","fa-door-closed");
                    brnCerrar.classList.replace("btn-primary","btn-success");
                    brnCerrar.setAttribute("title","Abrir fardo");
                }
                if(response.success && mensaje){
                    alertify.success(response.success);
                }
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al cerrar el fardo");
        }
    }
    async eliminarFardo(datosKardex){
        try {
            return await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/eliminar",datosKardex,"POST");
        } catch (error) {
            alertify.error("error al cerrar el fardo");
            return {error : 'error al eliminar el fardo'};
        }
    }
    async eliminarDetalleFardo(datosKardex){
        try {
            return await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/producto/eliminar",datosKardex,"POST");
        } catch (error) {
            alertify.error("error al cerrar el fardo");
            return {error : 'error al eliminar el fardo'};
        }
    }
    async abrirFardo(datosKardex,txtFardoActivo,tableDetalleKardex,btnAbrirFardo){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/abrir",datosKardex,"POST");
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta);
            }
            txtFardoActivo.textContent = response.nroFardo;
            const brnCerrar = tableDetalleKardex.querySelector('.btn-primary');
            if(brnCerrar){
                brnCerrar.querySelector('i').classList.replace("fa-door-open","fa-door-closed");
                brnCerrar.classList.replace("btn-primary","btn-success");
                brnCerrar.setAttribute("title","Abrir fardo");
            }
            btnAbrirFardo.querySelector('i').classList.replace("fa-door-closed","fa-door-open");
            btnAbrirFardo.classList.replace("btn-success","btn-primary");
            btnAbrirFardo.setAttribute("title","Cerrar fardo");
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al cerrar el fardo");
        }
    }
    async generarKardex(datosKardex){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/generar",datosKardex,"POST");
            if(response.success){
                alertify.alert("Mensaje",response.success,()=>{window.location.reload()})
            }
        }catch(error){
            console.log(error);
            alertify.error("error al generar el kardex");
        }
    }
    vacioTablaDetalle = false;
    async agregarFardo(datosKardex,txtProveedor,txtProducto,txtPresentacion,txtCantidad,tablaFardos,txtFardoActivo){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/guardar",datosKardex,"POST");
            if(response.session){
                return alertify.alert([...this.general.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta)
            }
            $(txtProveedor).select2("destroy");
            $(txtProducto).select2("destroy");
            $(txtPresentacion).select2("destroy");
            if(this.vacioTablaDetalle){
                this.vacioTablaDetalle = false;
                tablaFardos.innerHTML = "";
            }
            const producto = this.generarFilaFardo(response.nroFardo,response.cantidadProducto,txtProveedor,txtProducto,txtPresentacion,txtCantidad,datosKardex.get('proveedor'),datosKardex.get('producto'),datosKardex.get('presentacion'),datosKardex.get('cantidad'),tablaFardos,response.nroFardo,response.idDetalle,response.kilaje,response.precioProducto,response.tipo);
            const padreFardo = document.querySelector(`#fardoLista${response.nroFardo}${response.cantidadProducto - 1}`);
            if(!padreFardo){
                tablaFardos.append(producto);
            }else{
                padreFardo.insertAdjacentElement('afterend',producto);
            }
            txtFardoActivo.textContent = response.nroFardo;
            for (const cb of producto.querySelectorAll("select")) {
                $(cb).select2(this.configuracion).on("select2:select",e => this.cambiarValorKardex(e,datosKardex.has('idKardex') ? datosKardex.get('idKardex') : null));
            }
            for (const input of producto.querySelectorAll("input")) {
                input.onchange = e => this.cambiarValorKardex(e,datosKardex.has('idKardex') ? datosKardex.get('idKardex') : null);
            }
            $('.destruir-fardo').select2(this.configuracion);
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar el producto al fardo");
        }
        
    }
    generarFilaFardo(fardo,producto,txtProveedor,txtProducto,txtPresentacion,txtCantidad,valProveedor,valProducto,valPresentacion,valCantidad,tablaFardos,nroFardoActivo,idDetalle,valKilaje,valCostoProd,total = false){
        const tr = document.createElement("tr");
        const cloneProveedor = txtProveedor.cloneNode(true);
        const cloneProducto = txtProducto.cloneNode(true);
        const cloneCantidad = txtCantidad.cloneNode(true);
        const clonePresentacion = txtPresentacion.cloneNode(true);
        let costoProducto = "";
        if(total){
            costoProducto = document.createElement("input");
            costoProducto.type = "number";
            costoProducto.step = "0.01";
            costoProducto.min = "0";
            costoProducto.className = "form-control form-control-sm";
            costoProducto.setAttribute("data-valor","costo");
            costoProducto.setAttribute("value",valCostoProd);
            costoProducto = "<td>" + costoProducto.outerHTML + "</td>";
        }
        cloneProveedor.setAttribute("id",`idProveedorFardo${idDetalle}`);
        cloneProveedor.setAttribute("class","select2-simple");
        cloneProveedor.setAttribute("data-valor","proveedor");
        cloneProveedor.removeAttribute("tabindex");
        cloneProveedor.removeAttribute("aria-hidden");
        cloneProveedor.querySelector(`option[value="${valProveedor}"]`).setAttribute("selected","selected");
        cloneProducto.setAttribute("id",`idProductoFardo${idDetalle}`);
        cloneProducto.setAttribute("class","select2-simple");
        cloneProducto.setAttribute("data-valor","producto");
        cloneProducto.removeAttribute("tabindex");
        cloneProducto.removeAttribute("aria-hidden");
        cloneProducto.querySelector(`option[value="${valProducto}"]`).setAttribute("selected","selected");
        clonePresentacion.setAttribute("id",`idPresentacionFardo${idDetalle}`);
        clonePresentacion.setAttribute("class","select2-simple");
        clonePresentacion.setAttribute("data-valor","presentacion");
        clonePresentacion.removeAttribute("tabindex");
        clonePresentacion.removeAttribute("aria-hidden");
        clonePresentacion.querySelector(`option[value="${valPresentacion}"]`).setAttribute("selected","selected");
        cloneCantidad.setAttribute("id",`idCantidadFardo${idDetalle}`);
        cloneCantidad.setAttribute("value",valCantidad);
        cloneCantidad.setAttribute("class","form-control form-control-sm");
        cloneCantidad.setAttribute("data-valor","cantidad");
        tr.setAttribute("id",`fardoLista${fardo}${producto}`);
        const trFardo = tablaFardos.querySelector(`#fardoLista${fardo}1`);
        let template = "";
        tr.setAttribute("data-fardo",fardo);
        tr.setAttribute("data-detalle",idDetalle);
        if(producto <= 1){
            template = `<td rowspan="1">
                ${fardo}
            </td>`;
        }else if(producto > 1 && trFardo){
            trFardo.children[0].setAttribute("rowspan",producto);
        }
        template += `
        <td>
            ${clonePresentacion.outerHTML}
        </td>
        <td class="wt-cantidad">
            ${cloneCantidad.outerHTML}
        </td>
        <td>
            ${cloneProveedor.outerHTML}
        </td>
        <td>
            ${cloneProducto.outerHTML}
        </td>
        ${costoProducto}
        <td>
            <button type="button" class="btn eliminar-producto btn-sm btn-danger mr-1" title="Eliminar producto">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
        `
        if(producto <= 1){
            template += `
            <td class="wt-cantidad" style="min-width:100px;">
                <input type="number" data-valor="kilaje" step="0.01" class="form-control form-control-sm" value="${valKilaje}">
            </td>
            <td rowspan="1">
                <div class="d-flex" style="gap:3px;">
                    <button type="button" class="btn btn-sm ${fardo == nroFardoActivo ? 'btn-primary' : 'btn-success'}" title="${fardo == nroFardoActivo ? 'Cerrar fardo' : 'Abrir fardo'}">
                        <i class="fas fa-door-${fardo == nroFardoActivo ? 'open' : 'closed'}"></i>
                    </button>
                    <button type="button" class="btn btn-sm eliminar-fardo btn-danger mr-1" title="Eliminar fardo">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>

            `
        }else if(producto > 1 && trFardo){
            let inicio = !total ? 6 : 7;
            let fin = !total ? 7 : 8;
            for (inicio; inicio <= fin; inicio++) {
                trFardo.children[inicio].setAttribute("rowspan",producto);                
            }
        }
        tr.innerHTML = template;
        return tr;
    }
    configuracion = {
        theme: 'bootstrap',
        width: '100%',
        placeholder: !$(this).data("placeholder") ? "Seleccione una opción" : $(this).data("placeholder"),
        tags: !$(this).data("tags") ? false : true
    }
    
    async obtenerKardexPendiente(idCliente,tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo,idKardex = null){
        try {
            const url = !idKardex ? this.general.url + "/almacen/kardex/pendiente/" + idCliente : this.general.url + "/almacen/kardex/pendiente/editar/" + idCliente +"/" + idKardex;
            const response = await this.general.funcfetch(url,null,"GET");
            if((response.kardex && !response.kardex.listaFardos.length) || !response.kardex ){
                txtFardoActivo.textContent = 'Ninguno';
                tableDetalleKardex.innerHTML = this.txtSinFardos;
                alertify.success("no se encontraron fardos");
                this.vacioTablaDetalle = true;
                return false
            }
            if(idKardex && response.tasas){
                let templateTasas = "";
                response.tasas.forEach(tasas => {
                    templateTasas += this.contenidoTasasEditar(tasas).outerHTML;
                });
                document.querySelector("#contenidoTasasProductos").innerHTML = !templateTasas ? `<div class="w-100 form-group text-center"><span>No se encontraron tasas para este cliente</span></div>` : templateTasas
            }
            this.vacioTablaDetalle = false;
            txtFardoActivo.textContent = !response.kardex.nroFardoActivo ? 'Ninguno' : response.kardex.nroFardoActivo;
            tableDetalleKardex.innerHTML = "";
            if(response.kardex && !response.kardex.listaFardos.length){
                tableDetalleKardex.innerHTML = this.txtSinFardos;
                return;
            }
            $(txtProveedor).select2("destroy");
            $(txtProducto).select2("destroy");
            $(txtPresentacion).select2("destroy");
            
            response.kardex.listaFardos.forEach(fardo => {
                const nroFardo = fardo.nro_fardo;
                fardo.productos.forEach((producto,kproducto) => {
                    tableDetalleKardex.append(this.generarFilaFardo(nroFardo,kproducto + 1,txtProveedor,txtProducto,txtPresentacion,txtCantidad,producto.id_proveedor,producto.id_producto,producto.id_presentacion,producto.cantidad,tableDetalleKardex,response.kardex.nroFardoActivo,producto.id,fardo.kilaje,producto.precio,!idKardex ? false : true));
                });
            });
            for (const cb of tableDetalleKardex.querySelectorAll("select")) {
                $(cb).select2(this.configuracion).on("select2:select",e => this.cambiarValorKardex(e,idKardex));
            }
            for (const input of tableDetalleKardex.querySelectorAll("input")) {
                input.onchange = e => this.cambiarValorKardex(e,idKardex);
            }
            $('.destruir-fardo').select2(this.configuracion);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los fardos para este cliente");
        }
    }
    contenidoTasasEditar({id,nombreCategoria,tasa}) {
        const div = document.createElement("div");
        div.innerHTML = `
        <input type="hidden" value="${id}" name="idCategoria[]">
        <div class="form-group">
            <label>Tasa de ${nombreCategoria}</label>
            <input name="tasaCategoria[]" required step="0.01" min="0" class="form-control form-control" value="${tasa}">
        </div>
        `
        return div;
    }
    async cambiarValorKardex(e,idKardex){
        let datos = new FormData();
        datos.append("campo",e.target.dataset.valor);
        datos.append("valor",e.target.value);
        datos.append("fardo",e.target.parentElement.parentElement.dataset.fardo);
        datos.append("idDetalle",e.target.parentElement.parentElement.dataset.detalle);
        datos.append("cliente",$('#idCliente').val());
        if(idKardex){
            datos.append("idKardex",idKardex);
        }
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/actualizar/fardos",datos,"POST");
            if(response.session){
                return alertify.alert([...this.general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta);
            }
            alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el campo " + e.target.dataset.valor);
        }
    }
}