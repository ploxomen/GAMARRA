@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ranking/clientes.js"></script>
    <script src="/ranking/filtros.js"></script>
    <title>Ranking clientes</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/clientes_ranking.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Ranking clientes</h4>
            </div>
        </div>
        <div class="bg-white p-3 border mb-3">
            <div class="row">
                @csrf
                @include('ranking.filtros',['tipo' => 'clientes'])
            </div>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaClientes" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Pais</th>
                        <th>Nombre Completo</th>
                        <th>Kilajes</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection