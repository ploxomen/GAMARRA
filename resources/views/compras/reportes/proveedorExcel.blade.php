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
        <th colspan="8">REPORTE DE PROVEEDORES</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
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
            $estados = ['Descontinuado', 'Vigente'];
        @endphp
        @foreach ($proveedores as $key => $proveedor)
            @php
                $estado = !isset($estados[$proveedor->estado]) ? 'Estado no establecido' : $estados[$proveedor->estado];
            @endphp
            <tr>
                <td></td>
                <td>{{ $key + 1 }}</td>
                <td>{{ $proveedor->tipoDocumento->documento }}</td>
                <td>{{ $proveedor->nro_documento }}</td>
                <td>{{ $proveedor->nombre_proveedor }}</td>
                <td>{{ $proveedor->telefono }}</td>
                <td>{{ $proveedor->celular }}</td>
                <td>{{ $proveedor->correo }}</td>
                <td>{{ $estado === false ? 'Estado no establecido' : $estado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
