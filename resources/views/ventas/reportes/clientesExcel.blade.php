<table>
    <tr>
        @for ($i = 0; $i < 8; $i++)
            <th></th>
        @endfor
        <th>
            <img src="{{public_path("img/logo-sin-fondo.png")}}" alt="logo de la empresa" width="120px">
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="10">REPORTE DE CLIENTES</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
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
                <td></td>
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