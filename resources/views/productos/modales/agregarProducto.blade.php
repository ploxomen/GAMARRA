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
                    <label for="idModalproductoCodigo">Código</label>
                    <input type="text" name="codigo" class="form-control" id="idModalproductoCodigo" required>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalproductoNombre">Producto</label>
                    <input type="text" name="nombreProducto" class="form-control" id="idModalproductoNombre" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModalproductoDescripcion">Descripción</label>
                    <textarea name="descripcion" id="idModalproductoDescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalprecioVenta">Precio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalproductoPrecioVenta" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0.00" required name="precioVenta" min="0" step="0.01" class="form-control" id="idModalproductoPrecioVenta">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalproductoPrecioVenta" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalid_categoria">Categoría</label>
                    <select name="id_categoria" id="idModalid_categoria" data-placeholder="Seleccione una categoria" required class="select2-simple">
                        <option value=""></option>
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->nombreCategoria}}</option>
                        @endforeach
                    </select>
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
                    <select name="id_subfamilia" id="idModalfamiliaSubId" data-placeholder="Seleccione una subfamilia" required class="select2-simple">
                    </select>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">VIGENTE</label>
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