<table>
    <tr></tr>
    <tr>
        <th></th>
        <th colspan="4">RANKING CLIENTES DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
            <th>N°</th>
            <th>PAIS</th>
            <th>CLIENTES</th>
            <th>KILAJES</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $k => $dato)
            <tr>
                <td></td>
                <td>{{$k + 1}}</td>
                <td>{{$dato->pais_espanish}}</td>
                <td>{{$dato->nombreCliente}}</td>
                <td>{{$dato->kilajes}}</td>{}
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