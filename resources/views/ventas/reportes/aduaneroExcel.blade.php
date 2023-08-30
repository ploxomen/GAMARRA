<table>
    <tr>
        @for ($i = 0; $i < 7; $i++)
            <th></th>
        @endfor
        <th>
            <img src="{{ public_path('img/logo-sin-fondo.png') }}" alt="logo de la empresa" width="120px">
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="8">REPORTE DE AGENTES DE ADUANAS</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
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
            $estados = ['Descontinuado', 'Vigente'];
        @endphp
        @foreach ($aduaneros as $key => $aduanero)
            @php
                $estado = !isset($estados[$aduanero->estado]) ? 'Estado no establecido' : $estados[$aduanero->estado];
            @endphp
            <tr>
                <td></td>
                <td>{{ $key + 1 }}</td>
                <td>{{ $aduanero->pais_espanish }}</td>
                <td>{{ $aduanero->documento }}</td>
                <td>{{ $aduanero->nro_documento }}</td>
                <td>{{ $aduanero->nombre_completo }}</td>
                <td>{{ $aduanero->tasa }}</td>
                <td>{{ $aduanero->principal === 1 ? 'Principal' : 'Secundario' }}</td>
                <td>{{ $estado === false ? 'Estado no establecido' : $estado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
