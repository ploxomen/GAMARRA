<table>
    <tr>
        @for ($i = 0; $i < 4; $i++)
            <th></th>
        @endfor
        <th>
            <img src="{{ public_path('img/logo-sin-fondo.png') }}" alt="logo de la empresa" width="120px">
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="2">REPORTE FAMILIAS, SUBFAMILIAS Y PRODUCTOS</th>
    </tr>
    <tr></tr>
    <tbody>
        @php
            $estados = ["Descontinuado","Vigente"];
        @endphp
        @foreach ($familias as $key => $familia)
            <tr style="background: #ffafaf;">
                <th></th>
                <th>CODIGO FAMILIA</th>
                <th>NOMBRE FAMILIA</th>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">{{$familia->codigo}}</td>
                <td class="text-center">{{$familia->nombre}}</td>
            </tr>
            @foreach ($familia->subFamila as $subfamilia)
                <tr style="background: rgb(175, 226, 255);">
                    <th></th>
                    <th>CODIGO SUBFAMILIA</th>
                    <th>NOMBRE SUBFAMILIA</th>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center">{{$subfamilia->codigo}}</td>
                    <td class="text-center">{{$subfamilia->nombre}}</td>
                </tr>
                @if ($subfamilia->productos->count())
                    <tr style="background: rgb(238, 255, 175);">
                        <th></th>
                        <th>CODIGO PRODUCTO</th>
                        <th>NOMBRE PRODUCTO</th>
                    </tr>
                    @foreach ($subfamilia->productos as $producto)
                        <tr>
                            <td></td>
                            <td class="text-center">{{$producto->codigo}}</td>
                            <td class="text-center">{{$producto->nombreProducto}}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>
