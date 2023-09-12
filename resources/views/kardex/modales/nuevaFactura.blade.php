<div class="modal fade" id="generarFactura" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Generar factura</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formFacturar">
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalFechaEmision">Fecha Emisión</label>
                    <input type="date" name="fechaEmision" required id="modalFechaEmision" class="form-control form-control-sm" value="{{$hoy}}" max="{{$hoy}}" min="{{$haceTresDias}}">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalTipoDocumentoSUNAT">Tipo Documento</label>
                    <select name="agenteTipoDocumento" required id="modalTipoDocumentoSUNAT" class="form-control select2-simple">
                        <option value="">Ninguno</option>
                        <option value="0" selected>DOC.TRIB.NO.DOM.SIN.RUC</option>
                        <option value="6">REGISTRO ÚNICO DE CONTRIBUYENTES</option>
                        <option value="7">PASPORTE</option>
                        <option value="A">CED. DIPLOMÁTICA DE IDENTIDAD</option>
                        <option value="B">DOCUMENTO INDENTIDAD PAÍS RESIDENCIA-NO.D</option>
                        <option value="C">TAX IDENTIFICACIÓN NUMBER - TIN - DOC TRIB PP.NN</option>
                        <option value="D">IDENTIFICATION NUMBER - IN - DOC TRIB PP.JJ</option>
                        <option value="E">TAM- TARJETA ANDINA DE MIGRACIÓN</option>
                        <option value="F">PERMISO TEMPORAL DE PERMANENCIA PTP</option>
                        <option value="G">SALVOCONDUCTO</option>
                        <option value="00">OTROS</option>
                        {{-- @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalagenteNumeroDocumento">Número Documento</label>
                    <input type="text" name="numeroDocumento" required id="modalagenteNumeroDocumento" class="form-control form-control-sm">

                </div>
                <div class="form-group col-12">
                    <label for="modalagente">Nombre del agente</label>
                    <input type="text" required name="nombreAgente" id="modalagente" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12">
                    <label for="modalDireccion">Dirección de mi Empresa</label>
                    <input type="text" required name="direccionAgente" id="modalDireccion" class="form-control form-control-sm" value="JR. AMERICA 626 URB. EL PORVENIR INT. 302 LIMA-LIMA-LA VICTORIA">
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoAlContado" required name="tipoFactura" value="Contado" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoAlContado">Al contado</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoACredito" checked value="Credito" required name="tipoFactura" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoACredito">A crédito</label>
                    </div>
                </div>
                <div class="form-group col-12">
                    <label for="modalObservaciones">Observaciones</label>
                    <textarea name="observaciones" id="modalObservaciones" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="form-group col-12" id="bloqueCredito">
                    <div class="d-flex justify-content-between form-group" style="gap: 10px;">
                        <b class="text-secondary">Créditos</b>
                        <button type="button" class="btn btn-sm btn-primary" id="btnAgregarCuotaFactura" title="Agregar cuotas">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha Límite</th>
                                    <th>Monto $</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="tablaCreditos">
                                <tr>
                                    <td colspan="100%" class="text-center">No se asignaron cuotas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group col-12">
                    <b class="text-secondary">Detalle de productos</b>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Descripcion</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Monto Total</th>
                                    <th id="modalimporte"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="form-group col-12">
                    <b>Total en letras: </b>
                    <span id="modaltotalLetras"></span>
                </div>
                <input type="submit" hidden id="inputFacturar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnFacturar">
                <i class="fas fa-save"></i>
                <span>Generar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnAtrasFrmContacto">
                <i class="far fa-hand-point-left"></i>                
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>