class Kardex{
    general = new General();
    txtSinFardos = `<tr><td colspan="100%" class="text-center">No se agregaron detalles</td></tr>`
    async cerrarFardo(datosKardex,txtFardoActivo,tableDetalleKardex){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/cerrar",datosKardex,"POST");
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta);
            }
            txtFardoActivo.textContent = 'Ninguno';
            const brnCerrar = tableDetalleKardex.querySelector('.btn-primary');
            brnCerrar.querySelector('i').classList.replace("fa-door-open","fa-door-closed");
            brnCerrar.classList.replace("btn-primary","btn-success");
            brnCerrar.setAttribute("title","Abrir fardo");
            return alertify.success(response.success);
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
    async agregarFardo(datosKardex,txtProveedor,txtProducto,txtPresentacion,txtCantidad,tablaFardos,txtFardoActivo){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/guardar",datosKardex,"POST");
            $(txtProveedor).select2("destroy");
            $(txtProducto).select2("destroy");
            $(txtPresentacion).select2("destroy");
            const producto = this.generarFilaFardo(response.nroFardo,response.cantidadProducto,txtProveedor,txtProducto,txtPresentacion,txtCantidad,datosKardex.get('proveedor'),datosKardex.get('producto'),datosKardex.get('presentacion'),datosKardex.get('cantidad'),tablaFardos,response.nroFardo);
            const padreFardo = document.querySelector(`#fardoLista${response.nroFardo}${response.cantidadProducto - 1}`);
            if(!padreFardo){
                tablaFardos.append(producto);
            }else{
                padreFardo.insertAdjacentElement('afterend',producto);
            }
            txtFardoActivo.textContent = response.nroFardo;
            for (const cb of producto.querySelectorAll("select")) {
                $(cb).select2(this.configuracion);
            }
            $('.destruir-fardo').select2(this.configuracion);
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar el producto al fardo");
        }
        
    }
    actualizarFardo({fardo,cliente,proveedor,producto,cantidad,unidad}){

    }
    generarFilaFardo(fardo,producto,txtProveedor,txtProducto,txtPresentacion,txtCantidad,valProveedor,valProducto,valPresentacion,valCantidad,tablaFardos,nroFardoActivo){
        const tr = document.createElement("tr");
        const cloneProveedor = txtProveedor.cloneNode(true);
        const cloneProducto = txtProducto.cloneNode(true);
        const cloneCantidad = txtCantidad.cloneNode(true);
        const clonePresentacion = txtPresentacion.cloneNode(true);
        cloneProveedor.setAttribute("id",`idProveedorFardo${fardo}${producto}`);
        cloneProveedor.setAttribute("class","select2-simple");
        cloneProveedor.removeAttribute("tabindex");
        cloneProveedor.removeAttribute("aria-hidden");
        cloneProveedor.querySelector(`option[value="${valProveedor}"]`).setAttribute("selected","selected");
        cloneProducto.setAttribute("id",`idProductoFardo${fardo}${producto}`);
        cloneProducto.setAttribute("class","select2-simple");
        cloneProducto.removeAttribute("tabindex");
        cloneProducto.removeAttribute("aria-hidden");
        cloneProducto.querySelector(`option[value="${valProducto}"]`).setAttribute("selected","selected");
        clonePresentacion.setAttribute("id",`idPresentacionFardo${fardo}${producto}`);
        clonePresentacion.setAttribute("class","select2-simple");
        clonePresentacion.removeAttribute("tabindex");
        clonePresentacion.removeAttribute("aria-hidden");
        clonePresentacion.querySelector(`option[value="${valPresentacion}"]`).setAttribute("selected","selected");
        cloneCantidad.setAttribute("id",`idCantidadFardo${fardo}${producto}`);
        cloneCantidad.setAttribute("value",valCantidad);
        cloneCantidad.setAttribute("class","form-control form-control-sm");
        tr.setAttribute("id",`fardoLista${fardo}${producto}`);
        const trFardo = tablaFardos.querySelector(`#fardoLista${fardo}1`);
        let template = "";
        tr.setAttribute("data-fardo",fardo);
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
        `
        if(producto <= 1){
            template += `
            <td class="wt-cantidad">
                <input type="number" step="0.01" class="form-control form-control-sm">
            </td>
            <td rowspan="1">
                <div class="d-flex" style="gap:3px;">
                    <button type="button" class="btn btn-sm ${fardo == nroFardoActivo ? 'btn-primary' : 'btn-success'}" title="${fardo == nroFardoActivo ? 'Cerrar fardo' : 'Abrir fardo'}">
                        <i class="fas fa-door-${fardo == nroFardoActivo ? 'open' : 'closed'}"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-1" title="Eliminar fardo">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>

            `
        }else if(producto > 1 && trFardo){
            trFardo.children[5].setAttribute("rowspan",producto);
            trFardo.children[6].setAttribute("rowspan",producto);
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
    async obtenerKardexPendiente(idCliente,tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo){
        try {
            const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/" + idCliente,null,"GET");
            if(!response.kardex.listaFardos){
                tableDetalleKardex.innerHTML = this.txtSinFardos;
                return false
            }
            txtFardoActivo.textContent = !response.kardex.nroFardoActivo ? 'Ninguno' : response.kardex.nroFardoActivo;
            tableDetalleKardex.innerHTML = "";
            $(txtProveedor).select2("destroy");
            $(txtProducto).select2("destroy");
            $(txtPresentacion).select2("destroy");
            response.kardex.listaFardos.forEach(fardo => {
                const nroFardo = fardo.nro_fardo;
                fardo.productos.forEach((producto,kproducto) => {
                    tableDetalleKardex.append(this.generarFilaFardo(nroFardo,kproducto + 1,txtProveedor,txtProducto,txtPresentacion,txtCantidad,producto.id_proveedor,producto.id_producto,producto.id_presentacion,producto.cantidad,tableDetalleKardex,response.kardex.nroFardoActivo));
                });
            });
            for (const cb of tableDetalleKardex.querySelectorAll("select")) {
                $(cb).select2(this.configuracion);
            }
            $('.destruir-fardo').select2(this.configuracion);
            
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los fardos para este cliente");
        }
    }
}