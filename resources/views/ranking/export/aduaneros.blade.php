<table>
    <tr></tr>
    <tr>
        <th></th>
        <th colspan="4">RANKING AGENTES DE ADUANAS DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
            <th>N°</th>
            <th>PAIS</th>
            <th>AGENTE DE ADUANAS</th>
            <th>PAGOS $</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $k => $dato)
            <tr>
                <td></td>
                <td>{{$k + 1}}</td>
                <td>{{$dato->pais_espanish}}</td>
                <td>{{$dato->nombre_completo}}</td>
                <td>{{$dato->costos}}</td>{}
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