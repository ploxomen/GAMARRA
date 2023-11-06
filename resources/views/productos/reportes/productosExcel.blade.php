<table>
    <tr>
        @for ($i = 0; $i < 5; $i++)
            <th></th>
        @endfor
        <th>
            <img src="{{ public_path('img/logo-sin-fondo.png') }}" alt="logo de la empresa" width="120px">
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="7">REPORTE DE PRODUCTOS</th>
    </tr>
    <tr></tr>
    <thead>
        <tr>
            <th></th>
            <th>N°</th>
            <th>CODIGO</th>
            <th>PRODUCTO</th>
            <th>CATEGORÍA</th>
            <th>FAMILIA</th>
            <th>SUBFAMILIA</th>
            <th>PRECIO</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @php
            $estados = ['Descontinuado', 'Vigente'];
        @endphp
        @foreach ($productos as $key => $producto)
            @php
                $estado = !isset($estados[$producto->productoEstado]) ? 'Estado no establecido' : $estados[$producto->productoEstado];
            @endphp
            <tr>
                <td></td>
                <td>{{ $key + 1 }}</td>
                <td>{{ $producto->productoCodigo }}</td>
                <td>{{ $producto->productoNombre }}</td>
                <td>{{ $producto->nombreCategoria }}</td>
                <td>{{ $producto->familiaNombre }}</td>
                <td>{{ $producto->familiaSubNombre }}</td>
                <td>{{ $producto->precioVenta}}</td>
                <td>{{ $estado === false ? 'Estado no establecido' : $estado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
