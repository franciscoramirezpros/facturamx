<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
* Módulo Facturama para Perfex CRM
* Versión: 1.0.0
* Requiere: Perfex CRM >= 3.2.0
*/

$CI = &get_instance();

// Cargar la configuración
$CI->load->config('facturama/config');

// Cargar el helper del módulo
require_once(__DIR__ . '/helpers/facturama_helper.php');

// Cargar la librería principal
require_once(__DIR__ . '/libraries/Facturama_lib.php');
$CI->load->library('facturama/Facturama_lib');

// Definir datos del módulo
$module_data = [
    'system_name'        => 'facturama',
    'module_name'        => 'Facturama MX',
    'module_version'     => '1.0.0',
    'required_version'   => '3.2.0',
    'description'        => 'Módulo de Facturación Electrónica para México usando Facturama',
    'author'            => 'Tu Nombre',
    'author_uri'        => 'tu_sitio_web.com',
];

// Registrar el módulo
register_module($module_data);

// Hooks y Acciones
hooks()->add_action('admin_init', 'facturama_module_init_menu_items');
hooks()->add_action('admin_init', 'facturama_register_permissions');
hooks()->add_action('after_invoice_added', 'facturama_after_invoice_added');
hooks()->add_action('after_invoice_updated', 'facturama_after_invoice_updated');
hooks()->add_action('before_invoice_deleted', 'facturama_before_invoice_deleted');
hooks()->add_action('invoice_status_changed', 'facturama_invoice_status_changed');

/**
 * Registra los permisos del módulo
 */
function facturama_register_permissions() {
    $capabilities = [
        'view'   => 'Ver facturas CFDI',
        'create' => 'Crear facturas CFDI',
        'edit'   => 'Editar facturas CFDI',
        'delete' => 'Eliminar facturas CFDI',
    ];

    register_staff_capabilities('facturama', $capabilities, 'Facturama MX');
}

/**
 * Inicializa los elementos del menú
 */
function facturama_module_init_menu_items() {
    $CI = &get_instance();

    if (staff_can('view', 'facturama')) {
        $CI->app_menu->add_sidebar_menu_item('facturama', [
            'name'     => 'Facturama MX',
            'collapse' => true,
            'position' => 30,
            'icon'     => 'fa-solid fa-file-invoice',
        ]);

        $CI->app_menu->add_sidebar_children_item('facturama', [
            'slug'     => 'facturama-dashboard',
            'name'     => 'Dashboard',
            'href'     => admin_url('facturama'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('facturama', [
            'slug'     => 'facturama-crear',
            'name'     => 'Crear CFDI',
            'href'     => admin_url('facturama/crear'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('facturama', [
            'slug'     => 'facturama-listado',
            'name'     => 'Listado CFDI',
            'href'     => admin_url('facturama/ver_facturas'),
            'position' => 10,
        ]);

        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('facturama', [
                'slug'     => 'facturama-config',
                'name'     => 'Configuración',
                'href'     => admin_url('facturama/configuracion'),
                'position' => 15,
            ]);
        }
    }
}

/**
 * Hook para cuando se crea una nueva factura
 */
function facturama_after_invoice_added($invoice_id) {
    $CI = &get_instance();
    $CI->load->model('facturama/Facturama_model');
    
    if (get_option('facturama_auto_invoice') == 1) {
        $CI->Facturama_model->crear_factura_desde_perfex($invoice_id);
    }
}

/**
 * Hook para cuando se actualiza una factura
 */
function facturama_after_invoice_updated($invoice_id) {
    $CI = &get_instance();
    $CI->load->model('facturama/Facturama_model');
    
    // Actualizar factura CFDI si existe
    if (get_option('facturama_auto_update') == 1) {
        $CI->Facturama_model->actualizar_factura_desde_perfex($invoice_id);
    }
}

/**
 * Hook para cuando cambia el estado de una factura
 */
function facturama_invoice_status_changed($data) {
    $CI = &get_instance();
    $CI->load->model('facturama/Facturama_model');
    
    // Si la factura se cancela
    if ($data['status'] == Invoices_model::STATUS_CANCELLED) {
        $factura = $CI->Facturama_model->get_by_invoice_id($data['invoice_id']);
        if ($factura) {
            $CI->Facturama_model->cancelar_factura($factura->uuid);
        }
    }
}

/**
 * Hook de activación del módulo
 */
function facturama_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
    
    $default_options = [
        'facturama_auto_invoice' => '0',
        'facturama_auto_update' => '0',
        'facturama_sandbox' => '1',
        'facturama_api_key' => '',
        'facturama_api_secret' => '',
        'facturama_pac' => 'facturama',
        'facturama_regimen_fiscal' => '601',
        'facturama_lugar_expedicion' => '',
    ];

    foreach ($default_options as $option => $value) {
        add_option($option, $value);
    }
    
    return true;
}

// Registro del hook de activación
register_activation_hook('facturama', 'facturama_activation_hook');

// Cargar assets
if (is_admin() && get_current_page() === 'facturama') {
    hooks()->add_action('app_admin_assets', function() {
        app_css_push([
            'path'    => 'modules/facturama/assets/css/facturama.css',
            'version' => '1.0.0'
        ]);
        app_js_push([
            'path'    => 'modules/facturama/assets/js/facturama.js',
            'version' => '1.0.0',
            'defer'   => true
        ]);
    });
}