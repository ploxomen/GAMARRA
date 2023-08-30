<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de clientes</title>
</head>
<body>
    <style>
        @page{
            font-size: 13px !important;
        }
    </style>
    @include('ranking.export.headerExport')
    <h3 class="text-center">REPORTE DE CLIENTES</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>PAIS</th>
                <th>TIPO DE DOCUMENTO</th>
                <th>NRO. DE DOCUMENTO</th>
                <th>CLIENTE</th>
                <th>CELULAR</th>
                <th>TELEFONO</th>
                <th>CORREO</th>
                <th>DIRECCION</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $estados = ["Descontinuado","Vigente"];
            @endphp
            @foreach ($clientes as $key => $cliente)
                @php
                    $estado = !isset($estados[$cliente->estado]) ? 'Estado no establecido' : $estados[$cliente->estado];
                @endphp
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$cliente->pais_espanish}}</td>
                    <td>{{$cliente->documento}}</td>
                    <td>{{$cliente->nro_documento}}</td>
                    <td>{{$cliente->nombreCliente}}</td>
                    <td>{{$cliente->celular}}</td>
                    <td>{{$cliente->telefono}}</td>
                    <td>{{$cliente->correo}}</td>
                    <td>{{$cliente->direccion}}</td>
                    <td>{{$estado === false ? 'Estado no establecido' : $estado}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>