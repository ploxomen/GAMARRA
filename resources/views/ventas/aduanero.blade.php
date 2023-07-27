@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ventas/adminAduaneros.js"></script>
    <title>Aduaneros</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/seguro-de-entrega.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Agente de aduanas</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarAduanero">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaAduanero" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Tipo documento</th>
                        <th>N° documento</th>
                        <th>Nombre Completo</th>
                        <th>País</th>
                        <th>Tasa</th>
                        <th>Principal</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('ventas.modales.egregarAduaneros')
@endsection