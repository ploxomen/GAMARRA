<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prefacturación</title>
</head>
<body>
    <table>
        <tr></tr>
        <tr>
            <th colspan="5">PRE-FACTURA</th>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">RUC:</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2"></td>
            <td>Fecha</td>
            <td>{{date('d/m/Y')}}</td>
        </tr>
        <thead>
            <tr>
                <th>N°</th>
                <th>CANTIDAD</th>
                <th>DESCRIPCION</th>
                <th>PRECIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kardex as $key => $producto)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$producto['cantidad']}}</td>
                    <td>{{$producto['descripcion']}}</td>
                    <td>{{$producto['precio']}}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>TOTAL</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>