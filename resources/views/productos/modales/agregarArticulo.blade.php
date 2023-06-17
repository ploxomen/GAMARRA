<div class="modal fade" id="agregarArticulo" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloArticulo">Agregar Artículo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formArticulo">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del artículo
                    </h5>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalarticuloCodigo">Código</label>
                    <input type="text" name="codigoArticulo" class="form-control" id="idModalarticuloCodigo" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModalarticuloNombre">Nombre</label>
                    <input type="text" name="nombreArticulo" class="form-control" id="idModalarticuloNombre" required>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de la familia
                    </h5>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalfamiliaId">Familia</label>
                    <select name="id_familia" id="idModalfamiliaId" data-placeholder="Seleccione una familia" required class="select2-simple">
                        <option value=""></option>
                        @foreach ($familias as $familia)
                            <option value="{{$familia->id}}">{{$familia->codigo .' - ' . $familia->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalfamiliaSubId">Subfamilia</label>
                    <select name="id_familia_sub" id="idModalfamiliaSubId" data-placeholder="Seleccione una subfamilia" required class="select2-simple">
                    </select>
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