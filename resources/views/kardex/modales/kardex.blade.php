<div class="modal fade" id="editarKardex" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modificar kardex</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                Fardo
            </h5>
            <form id="frmDatosKardex" class="form-row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                    <label for="idCliente" class="col-form-label col-form-label-sm">Cliente</label>
                    <select name="cliente" required id="idCliente" class="select2-simple">
                        <option value=""></option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                    <label for="idProveedor" class="col-form-label col-form-label-sm">Proveedor</label>
                    <select name="proveedor" id="idProveedor" class="select2-simple destruir-fardo" required>
                        <option value=""></option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{$proveedor->id}}">{{$proveedor->nombre_proveedor}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                    <label for="idProducto" class="col-form-label col-form-label-sm">Producto</label>
                    <select name="producto" id="idProducto" class="select2-simple destruir-fardo" required>
                        <option value=""></option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombreProducto}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-4 col-xl-3 form-group">
                    <label for="idPresentacion" class="col-form-label col-form-label-sm">Presentación</label>
                    <select name="presentacion" id="idPresentacion" class="select2-simple destruir-fardo" required>
                        <option value=""></option>
                        @foreach ($presentaciones as $presentacion)
                            <option value="{{$presentacion->id}}" {{$presentacion->id === 'NIU' ? 'selected' : ''}}>{{$presentacion->presentacion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-4 col-xl-2 form-group">
                    <label for="idCantidad" class="col-form-label col-form-label-sm">Cantidad</label>
                    <input name="cantidad" type="number" class="form-control" id="idCantidad" required>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-10 form-group">
                    <button type="submit" id="agregarFardo" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Agregar fardo"><i class="fas fa-plus"></i></button>
                    <button type="button" id="cerrarFardo" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Cerrar fardo"><i class="fas fa-door-closed"></i></button>
                    <b>N° de fardo activo: <span class="text-danger" id="txtFardoActivo">Ninguno</span></b>
                </div>
            </form>
            <div class="d-flex form-group justify-content-between flex-wrap" style="gap: 5px;">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Tasas por categoría
                </h5>
                <button type="button" class="btn btn-sm btn-danger" id="btnTasasCliente">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <form id="formTasasProductos">
                <div id="contenidoTasasProductos" class="d-flex flex-wrap" style="gap: 10px;">
                    <div class="w-100 form-group text-center">
                        <span>Seleccione un cliente para visualizar las tasas</span>
                    </div>
                </div>
                <input type="submit" hidden id="frmTasasProductoInput">
            </form>
            <div class="d-flex justify-content-between flex-wrap" style="gap: 5px;">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos Adicionales
                </h5>
                <button type="button" class="btn btn-sm btn-danger" id="btnTasasAdicionales">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <form id="formTasasKardex" class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="idModalguia_aerea">Guía aérea</label>
                    <div class="input-group">
                        <input type="text" maxlength="250" class="form-control" name="guia_aerea" id="idModalguia_aerea">
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="idModalid_aduanero">Agente aduanas</label>
                    <select name="aduanero" id="idModalid_aduanero" class="select2-simple">
                        <option value=""></option>
                        @foreach ($aduaneros as $aduanero)
                            <option value="{{$aduanero->id}}">{{$aduanero->nombre_completo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-5 col-lg-3">
                    <label for="idModaltasa_extranjera">Tasa Extranjera</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">$</div>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" name="tasa_extranjera" id="idModaltasa_extranjera" required>
                    </div>
                </div>
                <input type="submit" id="frmTasasKardexInput" hidden>
            </form>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" style="font-size: 0.8rem; text-align: center;">
                    <thead>
                        <tr>
                            <th>N° FARDO</th>
                            <th>PRESENTACION</th>
                            <th>CANTIDAD</th>
                            <th>PROVEEDOR</th>
                            <th>DESCRIPCION</th>
                            <th>COSTO</th>
                            <th>ELIMINAR</th>
                            <th>KILAJES</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tablaDetalle">
                        <tr>
                            <td colspan="100%" class="text-center">No se agregaron detalles</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>