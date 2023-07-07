<div class="modal fade" id="editarKardexProveedor" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Recepción proveedor</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="row" id="frmKardexProveedor">
            <div class="form-group col-12 col-md-6">
                <label for="idModalfechaRecepcion">Fecha:</label>
                <input type="date" name="fechaRecepcion" required id="idModalfechaRecepcion" class="form-control form-control-sm">
            </div>
            <div class="form-group col-12">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>CANT.</th>
                                <th>PRESENT.</th>
                                <th>DETALLE</th>
                                <th>IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalleProveedor">
                            <tr>
                                <td colspan="100%" class="text-center">No se encontraron detalles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group col-12">
                <label for="idModalobservaciones">Observaciones:</label>
                <textarea name="observaciones" id="idModalobservaciones" class="form-control form-control-sm" rows="5" maxlength="500"></textarea>
            </div>
            <input type="submit" id="btnEnviar" hidden>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardar">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cancelar</span>
          </button>
        </div>
      </div>
    </div>
  </div>