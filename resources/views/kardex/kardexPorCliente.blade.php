@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/kardex/kardexCliente.js"></script>
    <title>Kardex por cliente</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/companero.png" alt="Imagen de dos peronas sincronizando la compra" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Kardex por cliente</h4>
            </div>
        </div>
        <div class="bg-white p-3 border mb-3">
            <form class="form-row">
                @csrf
                <div class="form-group col-6 col-lg-4 col-xl-3">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input id="txtFechaInicio" name="fechaInicio" required type="date" value="{{$fechaInicio}}" class="form-control">
                </div>
                <div class="form-group col-6 col-lg-4 col-xl-3">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input id="txtFechaFin" name="fechaFin" required type="date" value="{{$fechafin}}" class="form-control">
                </div>
                <div class="form-group col-6 col-lg-4 col-xl-3">
                    <label for="cbClientes">Clientes</label>
                    <select name="cliente" required id="cbClientes" class="form-control select2-simple">
                        <option value=""></option>
                        {{-- <option value="todos">TODOS</option> --}}
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col lg-4 col-xl-3">
                    <button class="btn btn-sm btn-primary" id="btnBuscar" type="button" title="Aplicar filtros" data-toggle="tooltip">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-sm btn-exportar btn-danger" data-accion="pdf" type="button" title="Exportar en PDF" data-toggle="tooltip">
                        <i class="fas fa-file-pdf"></i>               
                    </button>
                    <button class="btn btn-sm btn-exportar btn-success" data-accion="excel" type="button" title="Exportar en Excel" data-toggle="tooltip">
                        <i class="fas fa-file-excel"></i>                    
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaClientes" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N° kardex</th>
                        <th>Fecha Kardex</th>
                        <th>Cliente</th>
                        <th>N° Fardo</th>
                        <th>Cantidad</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection