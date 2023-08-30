<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de agentes de aduanas</title>
</head>
<body>
    <style>
        @page{
            font-size: 13px !important;
        }
    </style>
    @include('ranking.export.headerExport')
    <h3 class="text-center">REPORTE DE AGENTES DE ADUANAS</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>PAIS</th>
                <th>TIPO DE DOCUMENTO</th>
                <th>NRO. DE DOCUMENTO</th>
                <th>AGENTE</th>
                <th>TASA</th>
                <th>PRINCIPAL</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $estados = ["Descontinuado","Vigente"];
            @endphp
            @foreach ($aduaneros as $key => $aduanero)
                @php
                    $estado = !isset($estados[$aduanero->estado]) ? 'Estado no establecido' : $estados[$aduanero->estado];
                @endphp
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$aduanero->pais_espanish}}</td>
                    <td>{{$aduanero->documento}}</td>
                    <td>{{$aduanero->nro_documento}}</td>
                    <td>{{$aduanero->nombre_completo}}</td>
                    <td>${{number_format($aduanero->tasa, 2)}}</td>
                    <td>{{$aduanero->principal === 1 ? 'Principal' : 'Secundario'}}</td>
                    <td>{{$estado === false ? 'Estado no establecido' : $estado}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>