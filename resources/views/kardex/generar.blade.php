@extends('helper.index')
@section('head')
    <link rel="stylesheet" href="/kardex/nuevoKardex.css">
    <script src="/kardex/generalKardex.js?v1.8"></script>
    <script src="/kardex/nuevoKardex.js?v1.8"></script>
    <title>Generar Kardex</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/documentos.png" alt="Imagen de kardex" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Generar Kardex</h4>
            </div>
        </div>
        <form id="frmKardex">
            <fieldset class="bg-white px-3 mb-4 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del kardex</legend>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="idCliente" class="col-form-label col-form-label-sm">Cliente</label>
                        <select name="cliente" required id="idCliente" class="select2-simple">
                            <option value=""></option>
                            @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="idProveedor" class="col-form-label col-form-label-sm">Proveedor</label>
                        <select name="proveedor" id="idProveedor" class="select2-simple destruir-fardo" required>
                            <option value=""></option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{$proveedor->id}}">{{$proveedor->nombre_proveedor}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="idProducto" class="col-form-label col-form-label-sm">Producto</label>
                        <select name="producto" id="idProducto" class="select2-simple destruir-fardo" required>
                            <option value=""></option>
                            @foreach ($productos as $producto)
                                <option value="{{$producto->id}}">{{$producto->nombreProducto}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 form-group">
                        <label for="idPresentacion" class="col-form-label col-form-label-sm">Presentación</label>
                        <select name="presentacion" id="idPresentacion" class="select2-simple destruir-fardo" required>
                            <option value=""></option>
                            @foreach ($presentaciones as $presentacion)
                                <option value="{{$presentacion->id}}" {{$presentacion->id === 'NIU' ? 'selected' : ''}}>{{$presentacion->presentacion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-2 form-group">
                        <label for="idCantidad" class="col-form-label col-form-label-sm">Cantidad</label>
                        <input name="cantidad" type="number" class="form-control" id="idCantidad" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-10 form-group">
                        <button type="submit" id="agregarFardo" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Agregar fardo"><i class="fas fa-plus"></i></button>
                        <button type="button" id="cerrarFardo" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Cerrar fardo"><i class="fas fa-door-closed"></i></button>
                        <b>N° de fardo activo: <span class="text-danger" id="txtFardoActivo">Ninguno</span></b>
                    </div>
            </fieldset>
        </form>
            <fieldset class="bg-white mb-3 px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Detalles del kardex</legend>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" style="font-size: 0.8rem; text-align: center;">
                        <thead>
                            <tr>
                                <th>N° FARDO</th>
                                <th>PRESENTACION</th>
                                <th>CANTIDAD</th>
                                <th>PROVEEDOR</th>
                                <th>DESCRIPCION</th>
                                <th>ELIMINAR</th>
                                <th>KILAJES</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalle">
                            <tr>
                                <td colspan="100%" class="text-center">No se agregaron detalles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            <div class="form-group text-center">
                <button class="btn btn-primary" type="button" id="btnGenerarKardex"><i class="fas fa-paper-plane"></i> Generar Kardex</button>
            </div>
        </form>
    </section>
@endsection