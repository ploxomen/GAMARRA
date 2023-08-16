<table>
    <tr></tr>
    <tr>
        <th></th>
        <th colspan="3">RANKING PROVEEDORES DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
            <th>N°</th>
            <th>PROVEEDORES</th>
            <th>CANTIDADES</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $k => $dato)
            <tr>
                <td></td>
                <td>{{$k + 1}}</td>
                <td>{{$dato->nombre_proveedor}}</td>
                <td>{{$dato->cantidades}}</td>{}
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th colspan="2">TOTAL</th>
            <th></th>
        </tr>
    </tfoot>
</table>