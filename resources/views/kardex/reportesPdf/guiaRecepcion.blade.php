<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Guía de Recepción</title>
</head>
<body>
    <style>
        @page{
            font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        }
        table{
            border-collapse: collapse;
            width: 100%;
        }
        .titulo{
            text-align: center;
            text-decoration: underline;
        }
        .bordes-firmas{
            border-top: 2px solid black;
            width: 200px;
            margin: auto;
        }
        .tabla-firmas td{
            padding: 5px 0;
        }
        .text-center{
            text-align: center;
        }
        .border{
            border: 1px solid black;
        }
    </style>
    <table>
        <tr>
            <td rowspan="3" style="width: 500px; height: 50px;">
                <img src="{{public_path("img/logo-sin-fondo.png")}}" alt="logo de la empresa" width="100px">
            </td>
            <td class="text-center border"><strong>N° Guía - {{str_pad($kardex->id,5,'0',STR_PAD_LEFT)}}</strong></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        {{-- <tr>
            <td class="text-center border">RUC: 20603897766</td>
        </tr> --}}
    </table>
    <h2 class="titulo">GUÍA DE RECEPCIÓN DE MERCADERÍA</h2>
    <p>
        <strong>EL SIGUIENTE DOCUMENTO ES UNA GUÍA INTERNA DE RECEPCIÓN DE MERCADERÍA:</strong>
    </p>
    <p>
        Recibí(mos) de: <strong>{{$kardex->proveedor->nombre_proveedor}}</strong>
    </p>
    <p>
        Cliente: <strong>{{$clienteModel->nombreCliente}}</strong>
    </p>
    <p>
        <b>La siguiente mercadería:</b>
    </p>
    <table border="1">
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Unid.<br>Medi.</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumaTotal = 0;
            @endphp
            @foreach ($listaDetalles as $detalle)
                @php
                    $sumaTotal += $detalle->sumaCantidad;
                @endphp
                <tr>
                    <td class="text-center">
                        {{floatval($detalle->sumaCantidad)}}
                    </td>
                    <td class="text-center">
                        {{$detalle->presentacion}}
                    </td>
                    <td>
                        {{$detalle->nombreProducto}}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>{{$sumaTotal}}</th>
                <th colspan="2"></th>
                
            </tr>
        </tfoot>
    </table>
    <p>
        <b>Observaciones: </b><br>{{empty($kardex->observaciones) ? '-' : $kardex->observaciones}}

    </p>
    <p class="text-center">
        <span>Lima, {{$fechaLarga}}</span>
    </p>
    <table class="tabla-firmas">
        <tr>
            <td class="text-center" style="width: 370px; padding-top: 80px; padding-bottom: 10px;">
                <div class="bordes-firmas"></div>
                <small>(Firma y huella digital)</small>
            </td>
            <td class="text-center" style="padding-top: 80px; padding-bottom: 10px; ">                <div class="bordes-firmas"></div>
                <small>(Firma)</small>
            </td>
        </tr>
        <tr>
            <td>Apellidos:</td>
            <td>Apellidos: {{auth()->user()->apellidos}}</td>
        </tr>
        <tr>
            <td>Nombres:</td>
            <td>Nombres: {{auth()->user()->nombres}}</td>
        </tr>
        <tr>
            <td>Cargo:</td>
            <td></td>
        </tr>
        <tr>
            <td class="text-center"><b>Entregue conforme</b></td>
            <td class="text-center"><b>Recibí conforme</b></td>
        </tr>
    </table>
</body>
</html>