<div class="modal fade" id="agregarAduanero" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloArticulo">Agregar agente de aduanas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formAduanero">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos
                    </h5>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModaltipo_documento">Tipo Documento</label>
                    <select name="tipo_documento" id="idModaltipo_documento" class="select2-simple">
                        <option value=""></option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalnro_documento">N° Documento</label>
                    <input type="text" maxlength="20" name="nro_documento" class="form-control" id="idModalnro_documento">
                </div>
                <div class="form-group col-12">
                    <label for="idModalnombre_completo">Nombres completos</label>
                    <input type="text" name="nombre_completo" class="form-control" id="idModalnombre_completo" required>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalid_pais">País</label>
                    <select name="id_pais" id="idModalid_pais" required class="select2-simple" data-placeholder="Seleccione un país">
                        <option value=""></option>
                        @foreach ($paises as $pais)
                            <option value="{{$pais->id}}" {{$pais->id == 165 ? 'selected' : ''}}>{{$pais->pais_espanish}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModaltasa">Tasa</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">$</div>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" name="tasa" id="idModaltasa" required>
                    </div>
                </div>
                <div class="form-group col-6">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="principal" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" id="idModalprincipal">
                        <label class="custom-control-label" for="idModalprincipal">PRINCIPAL</label>
                    </div>
                </div>
                <div class="form-group col-6">
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