class Kardex{
    general = new General();
    async agregarFardo(datosKardex){
        const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/guardar",datosKardex,"POST");
        console.log(response);
    }
    actualizarFardo({fardo,cliente,proveedor,producto,cantidad,unidad}){

    }
    async obtenerKardexPendiente(idCliente){
        const response = await this.general.funcfetch(this.general.url + "/almacen/kardex/pendiente/" + idCliente,null,"GET");
        console.log(response);
    }
}