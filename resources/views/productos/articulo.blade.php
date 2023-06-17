@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/productos/adminArticulo.js"></script>
    <title>Artículo</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/articulo.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Articulos</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarArticulo">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaArticulo" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Código</th>
                        <th>Artículo</th>
                        <th>Familia</th>
                        <th>Subfamilia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('productos.modales.agregarArticulo')
@endsection