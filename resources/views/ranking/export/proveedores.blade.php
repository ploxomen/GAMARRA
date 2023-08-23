<table>
    <tr>
        @for ($i = 0; $i < 6; $i++)
            <th></th>
        @endfor
        <th>
            <img src="{{public_path("img/logo-sin-fondo.png")}}" alt="logo de la empresa" width="120px">
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="4">RANKING PROVEEDORES DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
            <th>N°</th>
            <th>PROVEEDORES</th>
            <th>PRODUCTOS</th>
            <th>CANTIDADES</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $k => $dato)
            <tr>
                <td></td>
                <td>{{$k + 1}}</td>
                <td>{{$dato->nombre_proveedor}}</td>
                <td>{{$dato->nombreProducto}}</td>
                <td>{{$dato->cantidades}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th colspan="3">TOTAL</th>
            <th></th>
        </tr>
    </tfoot>
</table>