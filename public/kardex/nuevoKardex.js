function loadPage() {
    let kardex = new Kardex();
    const tableDetalleKardex = document.querySelector("#tablaDetalle");
    const txtProveedor = document.querySelector("#idProveedor");
    const txtProducto = document.querySelector("#idProducto");
    const txtCantidad = document.querySelector("#idCantidad");
    const txtPresentacion = document.querySelector("#idPresentacion");
    const txtFardoActivo = document.querySelector("#txtFardoActivo");
    $('#idCliente').on("select2:select",function(e){
        kardex.obtenerKardexPendiente($(this).val(),tableDetalleKardex,txtProveedor,txtProducto,txtCantidad,txtPresentacion,txtFardoActivo);
    });
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