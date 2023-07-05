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
    const txtPresentacion = document.querySelector("#idPresentacion");

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
    const txtFardoActivo = document.querySelector("#txtFardoActivo");
    $('#idCliente').on("select2:select",function(e){
        kardex.obtenerKardexPendiente($(this).val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo);
    })
    
    document.querySelector("#cerrarFardo").onclick = function(){
        let datos = new FormData();
        datos.append('cliente',$('#idCliente').val())
        kardex.cerrarFardo(datos,txtFardoActivo,tableDetalleKardex);
    }
    const frmKardex = document.querySelector("#frmKardex");
    frmKardex.addEventListener("submit",function(e){
        e.preventDefault();
        let datos = new FormData(this);
        kardex.agregarFardo(datos,txtProveedor,txtProducto,txtPresentacion,txtCantidad,tableDetalleKardex,txtFardoActivo);
    })
    document.querySelector("#btnGenerarKardex").onclick = function(e){
        alertify.confirm("Mensaje","¿Estas seguro de generar el kardex?",()=>{
            let datos = new FormData();
            datos.append('cliente',$('#idCliente').val())
            kardex.generarKardex(datos);
        },()=>{})
    }
    tableDetalleKardex.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar este fardo?",async ()=>{
                $('#tablaDetalle .select2-simple').select2("destroy");
                let datos = new FormData();
                datos.append('cliente',$('#idCliente').val())
                datos.append('fardo',e.target.parentElement.parentElement.parentElement.dataset.fardo);
                const response = await kardex.eliminarFardo(datos,txtFardoActivo,tableDetalleKardex);
                if(response.success){
                    alertify.success(response.success);
                    kardex.obtenerKardexPendiente($('#idCliente').val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo);
                    return false
                }
                return alertify.alert("Mensaje",response.alerta);
            },()=>{})
        }
        if(e.target.classList.contains("btn-primary")){
            let datos = new FormData();
            datos.append('cliente',$('#idCliente').val())
            kardex.cerrarFardo(datos,txtFardoActivo,tableDetalleKardex);
        }
        if(e.target.classList.contains("btn-success")){
            let datos = new FormData();
            datos.append('cliente',$('#idCliente').val())
            datos.append('fardo',e.target.parentElement.parentElement.parentElement.dataset.fardo);
            kardex.abrirFardo(datos,txtFardoActivo,tableDetalleKardex,e.target);
        }
        
    }
}
window.addEventListener("DOMContentLoaded",loadPage);