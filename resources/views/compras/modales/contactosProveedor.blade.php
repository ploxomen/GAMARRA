<div class="modal fade" id="agregarProveedorContacto" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProveedor">Contactos proveedor</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formProveedorContactos">
                <div class="col-12 form-group text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="agregarContacto">
                        <i class="fas fa-plus"></i>
                        Añadir contacto
                    </button>
                </div>
                <ol class="ml-3" id="listaContactosProveedor">
                </ol>
                <input type="submit" hidden id="btnFrmEnviarContactos">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrmContactos">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnAtrasFrmContacto">
                <i class="far fa-hand-point-left"></i>                
                <span>Atras</span>
            </button>
        </div>
      </div>
    </div>
  </div>