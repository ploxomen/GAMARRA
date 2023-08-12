<div class="modal fade" id="agregarProveedor" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProveedor">Agregar Proveedor</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formProveedor">
                <div class="form-group col-12 col-md-6">
                    <label for="idModaltipo_documento">Tipo documento</label>
                    <select name="tipo_documento" id="idModaltipo_documento" class="form-control" required>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            @if (!in_array($tipoDocumento->id,[4,5,6]))
                                <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalnro_documento">N° Documento</label>
                    <input type="text" maxlength="20" name="nro_documento" class="form-control" id="idModalnro_documento" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModalnombre_proveedor">Proveedor</label>
                    <input type="text" name="nombre_proveedor" class="form-control" id="idModalnombre_proveedor" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalcelular">Celular</label>
                    <input type="tel" name="celular" class="form-control" id="idModalcelular">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModaltelefono">Teléfono</label>
                    <input type="tel" name="telefono" class="form-control" id="idModaltelefono">
                </div>
                <div class="form-group col-12">
                    <label for="idModalcorreo">Correo</label>
                    <input type="email" name="correo" class="form-control" id="idModalcorreo">
                </div>
                <div class="form-group col-12">
                    <label for="idModaldireccion">Dirección</label>
                    <textarea name="direccion" class="form-control" id="idModaldireccion" rows="3"></textarea>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                    </div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-hand-point-right"></i>
                <span>Siguiente</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>