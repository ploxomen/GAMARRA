@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/productos/adminFamilia.js?1.5"></script>
    <title>Categoria</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/categoria.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Familias y subfamilias</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#agregarFamilias">
                <i class="fas fa-plus"></i>
                <span>AGREGAR</span>
            </button>
            <button class="btn btn-danger exportar-datos" data-type="pdf">
                <i class="fas fa-file-pdf"></i>
                <span>PDF</span>
            </button>
            <button class="btn btn-success exportar-datos" data-type="excel">
                <i class="fas fa-file-excel"></i>
                <span>EXCEL</span>
            </button>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaFamilia" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Código</th>
                        <th>Familia</th>
                        <th>Cantidad <br>Subfamilias</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('productos.modales.agregarFamilia')
@endsection