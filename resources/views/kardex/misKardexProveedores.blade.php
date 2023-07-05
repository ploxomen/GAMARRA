@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/kardex/misKardexProveedor.js"></script>
    <title>Mis Kardex - Proveedores</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/repartidor.png" alt="Imagen de una persona llevando una caja" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Kardex - Proveedores</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaProveedores">
            <thead class="text-center">
                <tr>
                    <th>N° Kardex</th>
                    <th>N° Guía Recep</th>
                    <th>Proveedor</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
@endsection