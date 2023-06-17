<div class="modal fade" id="agregarProducto" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProducto">Agregar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formProducto">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del producto
                    </h5>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalcodigoProducto">Código</label>
                    <input type="text" name="codigoProducto" class="form-control" id="idModalcodigoProducto" required>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalnombreProducto">Producto</label>
                    <input type="text" name="nombreProducto" class="form-control" id="idModalnombreProducto" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModaldescripcion">Descripción</label>
                    <textarea name="descripcion" id="idModaldescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalprecioVenta">Precio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVenta" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0.00" name="precioVenta" min="0" step="0.01" class="form-control" id="idModalprecioVenta">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVenta" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-8">
                    <label for="customFileLang">Imagen del Producto</label>
                    <input type="file" name="urlImagen" class="form-control-file form-control-sm" accept="image/*" id="customFileLang">
                </div>
                <div class="form-group col-12 col-md-4">
                    <label class="mb-0">Imagen Previa</label>
                    <div>
                        <img src="/img/imgprevproduc.png" id="imgPrevio" alt="Imagen del producto" width="80px">
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de la familia
                    </h5>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalfamiliaId">Familia</label>
                    @include('helper.combobox.cbFamilia',['idFamilia' => 'idModalfamiliaId'])
                </div>
                <div class="form-group col-md-6 form-required">
                    <label for="idModalfamiliaSubId">Subfamilia</label>
                    <select name="id_familia_sub" id="idModalfamiliaSubId" data-placeholder="Seleccione una subfamilia" required class="select2-simple">
                    </select>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalarticulo">Artículo</label>
                    <select name="id_articulo" id="idModalarticulo" data-placeholder="Seleccione un artículo" required class="select2-simple">
                    </select>
                </div>
                <div class="form-group col-12">
                    <div class="d-flex flex-wrap" style="gap: 20px;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                            <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                        </div>
                    </div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-eraser"></i>
                    <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>