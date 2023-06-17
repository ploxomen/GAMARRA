<div class="modal fade" id="agregarFamilias" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloFamilia">Agregar Familia</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formFamilia">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de la familia
                    </h5>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalcodigo">Código</label>
                    <input type="text" name="codigoFamilia" class="form-control" id="idModalcodigo" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModalnombre">Nombre</label>
                    <input type="text" name="nombreFamilia" class="form-control" id="idModalnombre" required>
                </div>
                <div class="form-group col-12 d-flex justify-content-between">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de las subfamilias
                    </h5>
                    <button type="button" class="btn btn-sm btn-light" id="btnAgregarSubfamilia" title="Agregar subfamilias">
                        <i class="fas fa-plus"></i> 
                    </button>
                </div>
                <div class="col-12">
                    <p class="text-info text-center" id="txtSinSubfamilias">Sin Subfamilias</p>
                    <ol id="listaSubfamilia" class="ml-3"></ol>
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