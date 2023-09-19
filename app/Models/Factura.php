<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $fillable = ['xml_pdf_numero_documento','fecha_emision','id_kardex','tipo_documento_destinatario','numero_documento_destinatario','nombre_completo_destinatario','xml_pdf_IDComprobante','observaciones','xml_pdf_Codigo','xml_pdf_IDRepositorio','tipo_factura','xml_pdf_Firma','monto_total','cdr_IDComprobante','cdr_Codigo','cdr_IDRepositorio','cdr_firma','tipo_moneda','estado'];
    const CREATED_AT = 'fecha_creada';
    const UPDATED_AT = 'fecha_actualizada';

}
