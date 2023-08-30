@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ranking/proveedores.js?v1.5"></script>
    <script src="/ranking/filtros.js?v1.5"></script>
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
        <div class="bg-white p-3 border mb-3">
            <div class="row">
                @csrf            
                @include('ranking.filtros',['tipo' => 'proveedores'])
            </div>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaProveedores" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Nombre Completo</th>
                        <th>Productos</th>
                        <th>Cantidades</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection