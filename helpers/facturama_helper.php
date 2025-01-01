<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Obtener la etiqueta de estado de factura
 */
function get_factura_status_label($estado)
{
    $classes = [
        'pendiente' => 'warning',
        'activa'    => 'success',
        'cancelada' => 'danger',
        'error'     => 'danger'
    ];

    return isset($classes[$estado]) ? $classes[$estado] : 'default';
}

/**
 * Obtener los regímenes fiscales
 */
function get_regimenes_fiscales()
{
    return [
        '601' => 'General de Ley Personas Morales',
        '603' => 'Personas Morales con Fines no Lucrativos',
        '605' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
        '606' => 'Arrendamiento',
        '607' => 'Régimen de Enajenación o Adquisición de Bienes',
        '608' => 'Demás ingresos',
        '609' => 'Consolidación',
        '610' => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
        '611' => 'Ingresos por Dividendos (socios y accionistas)',
        '612' => 'Personas Físicas con Actividades Empresariales y Profesionales',
        '614' => 'Ingresos por intereses',
        '615' => 'Régimen de los ingresos por obtención de premios',
        '616' => 'Sin obligaciones fiscales',
        '620' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
        '621' => 'Incorporación Fiscal',
        '622' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
        '623' => 'Opcional para Grupos de Sociedades',
        '624' => 'Coordinados',
        '625' => 'Régimen Simplificado de Confianza'
    ];
}

/**
 * Obtener los tipos de relación CFDI
 */
function get_tipos_relacion()
{
    return [
        '01' => 'Nota de crédito de los documentos relacionados',
        '02' => 'Nota de débito de los documentos relacionados',
        '03' => 'Devolución de mercancía sobre facturas o traslados previos',
        '04' => 'Sustitución de los CFDI previos',
        '05' => 'Traslados de mercancias facturados previamente',
        '06' => 'Factura generada por los traslados previos',
        '07' => 'CFDI por aplicación de anticipo'
    ];
}

/**
 * Verificar si existe un CFDI para una factura
 */
function has_cfdi($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('facturama/Facturama_model');
    return (bool)$CI->Facturama_model->get_by_invoice_id($invoice_id);
}

/**
 * Obtener el estado de un CFDI
 */
function get_cfdi_status($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('facturama/Facturama_model');
    $factura = $CI->Facturama_model->get_by_invoice_id($invoice_id);
    return $factura ? $factura->estado : null;
}