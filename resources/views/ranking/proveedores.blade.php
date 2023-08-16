@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ranking/proveedores.js"></script>
    <title>Ranking proveedores</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/proveedor_ranking.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Ranking proveedores</h4>
            </div>
        </div>
        <div class="bg-white p-3 border">
            <form class="row" method="POST" action="{{route('reportesRankingProveedor')}}">
                @csrf
                <div class="form-group col-6 col-lg-4 col-xl-2">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input id="txtFechaInicio" name="fechaInicio" required type="date" value="{{date('Y-m-d',strtotime(date('Y-m-d') . ' - 30 days'))}}" class="form-control">
                </div>
                <div class="form-group col-6 col-lg-4 col-xl-2">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input id="txtFechaFin" name="fechaFin" required type="date" value="{{date('Y-m-d')}}" class="form-control">
                </div>
                <div class="form-group col-12 col lg-4 col-xl-2">
                    <button class="btn btn-sm btn-danger" id="btnBuscar" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-sm btn-success" type="submit">
                        <i class="fas fa-file-excel"></i>                    
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaProveedores" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Nombre Completo</th>
                        <th>Cantidades</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection