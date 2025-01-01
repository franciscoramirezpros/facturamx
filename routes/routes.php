<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Rutas principales del módulo
$route['admin/facturamx'] = 'facturamx/Facturamx/index';
$route['admin/facturamx/dashboard'] = 'facturamx/Facturamx/index';

// Rutas para facturas
$route['admin/facturamx/crear/(:num)'] = 'facturamx/Facturamx/crear/$1';
$route['admin/facturamx/ver_facturas'] = 'facturamx/Facturamx/ver_facturas';
$route['admin/facturamx/ver_factura/(:any)'] = 'facturamx/Facturamx/ver_factura/$1';
$route['admin/facturamx/cancelar/(:any)'] = 'facturamx/Facturamx/cancelar/$1';

// Rutas para descargas y archivos
$route['admin/facturamx/descargar_pdf/(:any)'] = 'facturamx/Facturamx/descargar/pdf/$1';
$route['admin/facturamx/descargar_xml/(:any)'] = 'facturamx/Facturamx/descargar/xml/$1';

// Rutas para configuración
$route['admin/facturamx/configuracion'] = 'facturamx/Facturamx/configuracion';
$route['admin/facturamx/guardar_configuracion'] = 'facturamx/Facturamx/guardar_configuracion';

// Rutas para catálogos
$route['admin/facturamx/catalogos/usos_cfdi'] = 'facturamx/Facturamx/obtener_usos_cfdi';
$route['admin/facturamx/catalogos/formas_pago'] = 'facturamx/Facturamx/obtener_formas_pago';
$route['admin/facturamx/catalogos/metodos_pago'] = 'facturamx/Facturamx/obtener_metodos_pago';

// Rutas para reportes
$route['admin/facturamx/reportes'] = 'facturamx/Facturamx/reportes';
$route['admin/facturamx/reporte_mensual'] = 'facturamx/Facturamx/reporte_mensual';
$route['admin/facturamx/reporte_cliente/(:num)'] = 'facturamx/Facturamx/reporte_cliente/$1';

// Rutas para operaciones asíncronas (AJAX)
$route['admin/facturamx/ajax/verificar_estado/(:any)'] = 'facturamx/Facturamx/verificar_estado/$1';
$route['admin/facturamx/ajax/reenviar_email/(:any)'] = 'facturamx/Facturamx/reenviar_email/$1';

// Rutas para webhooks
$route['facturamx/webhook/(:any)'] = 'facturamx/Webhook/process/$1';
$route['facturamx/webhook'] = 'facturamx/Webhook/process';

// Rutas de API (opcional, si se necesita)
$route['api/facturamx/status/(:any)'] = 'facturamx/api/check_status/$1';
$route['api/facturamx/validate/(:any)'] = 'facturamx/api/validate/$1';