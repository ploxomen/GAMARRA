<div class="form-group col-6 col-lg-4 col-xl-2">
    <label for="txtFechaInicio">Fecha Inicio</label>
    <input id="txtFechaInicio" name="fechaInicio" required type="date" value="{{date('Y-m-d',strtotime(date('Y-m-d') . ' - 30 days'))}}" class="form-control">
</div>
<div class="form-group col-6 col-lg-4 col-xl-2">
    <label for="txtFechaFin">Fecha Fin</label>
    <input id="txtFechaFin" name="fechaFin" required type="date" value="{{date('Y-m-d')}}" class="form-control">
</div>
<div class="form-group col-12 col lg-4 col-xl-3">
    <button class="btn btn-sm btn-primary" id="btnBuscar" type="button" title="Aplicar filtros" data-toggle="tooltip">
        <i class="fas fa-search"></i>
    </button>
    <button class="btn btn-sm btn-exportar btn-danger" data-type="{{$tipo}}" data-accion="pdf" type="button" title="Exportar en PDF" data-toggle="tooltip">
        <i class="fas fa-file-pdf"></i>               
    </button>
    <button class="btn btn-sm btn-exportar btn-success" data-type="{{$tipo}}" data-accion="excel" type="button" title="Exportar en Excel" data-toggle="tooltip">
        <i class="fas fa-file-excel"></i>                    
    </button>
</div>