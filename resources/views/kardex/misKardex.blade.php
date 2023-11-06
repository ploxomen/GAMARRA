@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/kardex/generalKardex.js?v1.8"></script>
    <script src="/kardex/misKardex.js?v1.8"></script>
    <title>Mis Kardex</title>
@endsection
@section('body')
<style>
    .formulario-remision label, input, textarea, select{
        font-size: 0.8rem !important;
    }
    .lista-noicons > .dropdown-menu > .dropdown-item i, .lista-noicons > .dropdown-menu > .dropdown-item > span{
        user-select: none;
        pointer-events: none;
    }
</style>
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/repartidor.png" alt="Imagen de una persona llevando una caja" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Kardex</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaKardex">
            <thead class="text-center">
                <tr>
                    <th>N° Kardex</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Kilaje</th>
                    <th>Importe</th>
                    <th>Factura</th>
                    <th>Guía Remisión</th>
                    <th>Guía aérea</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('kardex.modales.kardex')
    @include('kardex.modales.kardexClientes')
    @include('kardex.modales.nuevaFactura')
    @include('kardex.modales.nuevaGuiaRemitente')
    @include('helper.carga')
@endsection