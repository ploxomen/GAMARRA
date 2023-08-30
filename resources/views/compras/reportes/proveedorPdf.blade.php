<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de proveedores</title>
</head>
<body>
    <style>
        @page{
            font-size: 13px !important;
        }
    </style>
    @include('ranking.export.headerExport')
    <h3 class="text-center">REPORTE DE PROVEEDORES</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>TIPO DE DOCUMENTO</th>
                <th>NRO. DE DOCUMENTO</th>
                <th>PROVEEDOR</th>
                <th>TELEFONO</th>
                <th>CELULAR</th>
                <th>CORREO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $estados = ["Descontinuado","Vigente"];
            @endphp
            @foreach ($proveedores as $key => $proveedor)
                @php
                    $estado = !isset($estados[$proveedor->estado]) ? 'Estado no establecido' : $estados[$proveedor->estado];
                @endphp
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$proveedor->tipoDocumento->documento}}</td>
                    <td>{{$proveedor->nro_documento}}</td>
                    <td>{{$proveedor->nombre_proveedor}}</td>
                    <td>{{$proveedor->telefono}}</td>
                    <td>{{$proveedor->celular}}</td>
                    <td>{{$proveedor->correo}}</td>
                    <td>{{$estado === false ? 'Estado no establecido' : $estado}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>