function loadPage() {
    let kardex = new Kardex();
    let fardo = 0;
    let cantidadFilas = 0;
    const configuracion = {
        theme: 'bootstrap',
        width: '100%',
        placeholder: !$(this).data("placeholder") ? "Seleccione una opción" : $(this).data("placeholder"),
        tags: !$(this).data("tags") ? false : true
    }
    const tableDetalleKardex = document.querySelector("#tablaDetalle");
    const txtProveedor = document.querySelector("#idProveedor");
    const txtProducto = document.querySelector("#idProducto");
    const txtCantidad = document.querySelector("#idCantidad");

    function generarDetalleKardex() {
        if(!fardo){
            tableDetalleKardex.innerHTML = "";
            fardo++;
        }
        cantidadFilas++;
        const tr = document.createElement("tr");
        $(txtProveedor).select2("destroy");
        $(txtProducto).select2("destroy");
        const cloneProveedor = txtProveedor.cloneNode(true);
        const cloneProducto = txtProducto.cloneNode(true);
        const cloneCantidad = txtCantidad.cloneNode(true);
        cloneProveedor.setAttribute("id",`idProveedorFardo${fardo}${cantidadFilas}`);
        cloneProveedor.setAttribute("class","select2-simple");
        cloneProveedor.removeAttribute("tabindex");
        cloneProveedor.removeAttribute("aria-hidden");
        cloneProveedor.querySelector(`option[value="${txtProveedor.value}"]`).setAttribute("selected","selected");
        cloneProducto.setAttribute("id",`idProductoFardo${fardo}${cantidadFilas}`);
        cloneProducto.setAttribute("class","select2-simple");
        cloneProducto.removeAttribute("tabindex");
        cloneProducto.removeAttribute("aria-hidden");
        cloneProducto.querySelector(`option[value="${txtProducto.value}"]`).setAttribute("selected","selected");
        cloneCantidad.setAttribute("id",`idCantidadFardo${fardo}${cantidadFilas}`);
        cloneCantidad.setAttribute("value",txtCantidad.value);
        cloneCantidad.setAttribute("class","form-control form-control-sm");
        tr.setAttribute("id",`fardoLista${fardo}${cantidadFilas}`);
        const trFardo = document.querySelector(`#tablaDetalle #fardoLista${fardo}1`);
        let template = "";
        if(cantidadFilas <= 1){
            tr.setAttribute("data-fardo",fardo);
            template = `<td rowspan="1">
                ${fardo}
            </td>`;
        }else if(cantidadFilas > 1 && trFardo){
            trFardo.children[0].setAttribute("rowspan",cantidadFilas);
        }
        template += `
        <td>
            ${cloneCantidad.outerHTML}
        </td>
        <td>
            ${cloneProveedor.outerHTML}
        </td>
        <td>
            ${cloneProducto.outerHTML}
        </td>
        <td>UND</td>
        `
        if(cantidadFilas <= 1){
            template += `
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm">
            </td>
            <td rowspan="1">
                <button type="button" class="btn btn-sm btn-danger" title="Eliminar fardo"><i class="fas fa-trash-alt"></i></button>
            </td>

            `
        }else if(cantidadFilas > 1 && trFardo){
            trFardo.children[5].setAttribute("rowspan",cantidadFilas);
            trFardo.children[6].setAttribute("rowspan",cantidadFilas);
        }
        tr.innerHTML = template;
        return tr;
    }
    // document.querySelector("#agregarFardo").onclick = function(){
        
        // kardex.agregarFardo();
        // const tr = generarDetalleKardex();
        // tableDetalleKardex.append(tr);
        // for (const cb of tr.querySelectorAll("select")) {
        //     $(cb).select2(configuracion);
        // }
        // $('.destruir-fardo').select2(configuracion);
        // alertify.success("detalle agregado");
    // }
    $('#idCliente').on("select2:select",function(e){
        kardex.obtenerKardexPendiente($(this).val());
    })
    document.querySelector("#cerrarFardo").onclick = function(){
        if(!fardo){
            return alertify.error("por favor añada al menos un detalle al fardo");
        }
        if(fardo != 0 && !document.querySelector(`tr[data-fardo="${fardo}"]`)){
            return alertify.error("el fardo ya a sido cerrado");
        }
        fardo++;
        cantidadFilas = 0;
        alertify.success("fardo cerrado");
    }
    const frmKardex = document.querySelector("#frmKardex");
    frmKardex.addEventListener("submit",function(e){
        e.preventDefault();
        let datos = new FormData(this);
        kardex.agregarFardo(datos);
        // if(!fardo){
        //     return alertify.error("el kardex debe contener al menos un fardo");
        // }
        // alertify.alert("Mensaje","Kardex generado con éxito",()=>{
        //     window.location.reload();
        // })
    })
    tableDetalleKardex.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar este fardo?",()=>{
                $('#tablaDetalle .select2-simple').select2("destroy");
                const cantidadFilass = +e.target.parentElement.getAttribute("rowspan");
                const fardos = +e.target.parentElement.parentElement.getAttribute("data-fardo");
                for (let index = 1; index <= cantidadFilass; index++) {
                    document.querySelector(`tr#fardoLista${fardos}${index}`).remove();
                }
                fardo--;
                alertify.success("fardo eliminado");
                if(fardo<=0 || !tableDetalleKardex.children.length){
                    cantidadFilas = 0;
                    fardo = 0;
                    tableDetalleKardex.innerHTML = `
                    <tr>
                        <td colspan="100%" class="text-center">No se agregaron detalles</td>
                    </tr>
                    `
                    return
                }
                let nroFila = 0;
                let nroFardo = 0;
                for (const tr of tableDetalleKardex.children) {
                    if(nroFardo > fardo){
                        break;
                    }
                    nroFila++;
                    if(tr.dataset.fardo){
                        nroFardo++;
                        nroFila = 1;
                        tr.dataset.fardo = nroFardo;
                        tr.children[0].textContent = nroFardo;
                    }
                    tr.setAttribute("id",`fardoLista${nroFardo}${nroFila}`);
                    tr.querySelectorAll(".select2-simple")[0].setAttribute("id",`idProveedorFardo${nroFardo}${nroFila}`);
                    tr.querySelectorAll(".select2-simple")[1].setAttribute("id",`idProductoFardo${nroFardo}${nroFila}`);
                    tr.querySelector("input").setAttribute("id",`idCantidadFardo${nroFardo}${nroFila}`);
                }
                $('#tablaDetalle .select2-simple').select2(configuracion);
                const fardoAnterior = document.querySelector(`tr#fardoLista${fardo}1`);
                if(fardoAnterior && cantidadFilas > 0){
                    cantidadFilas = +fardoAnterior.children[0].getAttribute("rowspan");
                }
            },()=>{})
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);