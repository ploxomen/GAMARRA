<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ranking de los agentes de aduanas</title>
</head>
<body>
    @include('ranking.export.headerExport')
    <h3 class="text-center">RANKING AGENTES DE ADUANAS DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>PAIS</th>
                <th>AGENTE DE ADUANAS</th>
                <th>PAGOS $</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($datos as $k => $dato)
                @php
                    $total += $dato->costos;
                @endphp
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$dato->pais_espanish}}</td>
                    <td>{{$dato->nombre_completo}}</td>
                    <td class="text-center">$ {{number_format($dato->costos,2)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                <th class="text-center">$ {{number_format($total,2)}}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>