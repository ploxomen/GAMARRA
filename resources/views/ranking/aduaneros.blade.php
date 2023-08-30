@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ranking/aduaneros.js"></script>
    <script src="/ranking/filtros.js?v1.5"></script>
    <title>Ranking agente de aduanas</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/aduanero_ranking.png" alt="Imagen de clientes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Ranking agente de aduanas</h4>
            </div>
        </div>
        <div class="bg-white p-3 border mb-3">
            <div class="row">
                @csrf
                @include('ranking.filtros',['tipo' => 'aduaneros'])
            </div>
        </div>
        <div class="bg-white p-3 border">
            <table id="tablaAduaneros" class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>País</th>
                        <th>Nombre Completo</th>
                        <th>Pagos $</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection