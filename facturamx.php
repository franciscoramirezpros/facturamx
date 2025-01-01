<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Factura MX
Description: Módulo de Facturación Electrónica para México
Version: 1.0.0
Requires at least: 3.2.0
Author: Micrm
Author URI: micrm.pro
*/

const FACTURAMX_MODULE_NAME = 'facturamx';

// Registrar el módulo
register_activation_hook(FACTURAMX_MODULE_NAME, 'facturamx_activation_hook');

// Registrar idiomas si es necesario
register_language_files(FACTURAMX_MODULE_NAME, [FACTURAMX_MODULE_NAME]);

// Hooks y Acciones
hooks()->add_action('admin_init', 'facturamx_module_init_menu_items');
hooks()->add_action('admin_init', 'facturamx_register_permissions');
hooks()->add_action('after_invoice_added', 'facturamx_after_invoice_added');
hooks()->add_action('after_invoice_updated', 'facturamx_after_invoice_updated');
hooks()->add_action('invoice_status_changed', 'facturamx_invoice_status_changed');

/**
 * Hook de activación del módulo
 */
function facturamx_activation_hook()
{
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
}

/**
 * Registra los permisos del módulo
 */
function facturamx_register_permissions()
{
    $capabilities = [
        'view'   => 'Ver facturas',
        'create' => 'Crear facturas',
        'edit'   => 'Editar facturas',
        'delete' => 'Eliminar facturas',
    ];

    register_staff_capabilities(FACTURAMX_MODULE_NAME, $capabilities, 'Factura MX');
}

/**
 * Inicializa los elementos del menú
 */
function facturamx_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission(FACTURAMX_MODULE_NAME, '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('facturamx', [
            'name'     => 'Factura MX',
            'collapse' => true,
            'position' => 30,
            'icon'     => 'fa fa-file-invoice'
        ]);

        $CI->app_menu->add_sidebar_children_item('facturamx', [
            'slug'     => 'facturamx-dashboard',
            'name'     => 'Dashboard',
            'href'     => admin_url('facturamx'),
            'position' => 1
        ]);

        $CI->app_menu->add_sidebar_children_item('facturamx', [
            'slug'     => 'facturamx-crear',
            'name'     => 'Crear Factura',
            'href'     => admin_url('facturamx/crear'),
            'position' => 5
        ]);

        $CI->app_menu->add_sidebar_children_item('facturamx', [
            'slug'     => 'facturamx-listado',
            'name'     => 'Listado Facturas',
            'href'     => admin_url('facturamx/ver_facturas'),
            'position' => 10
        ]);

        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('facturamx', [
                'slug'     => 'facturamx-config',
                'name'     => 'Configuración',
                'href'     => admin_url('facturamx/configuracion'),
                'position' => 15
            ]);
        }
    }
}

/**
 * Hook para cuando se crea una nueva factura
 */
function facturamx_after_invoice_added($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('Facturama_model');
    
    if (get_option('facturama_auto_invoice') == 1) {
        $CI->Facturama_model->crear_factura_desde_perfex($invoice_id);
    }
}

/**
 * Hook para cuando se actualiza una factura
 */
function facturamx_after_invoice_updated($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('Facturama_model');
    
    if (get_option('facturama_auto_update') == 1) {
        $CI->Facturama_model->actualizar_factura_desde_perfex($invoice_id);
    }
}

/**
 * Hook para cuando cambia el estado de una factura
 */
function facturamx_invoice_status_changed($data)
{
    $CI = &get_instance();
    $CI->load->model('Facturama_model');
    
    if ($data['status'] == 2) { // 2 = Cancelled status
        $factura = $CI->Facturama_model->get_by_invoice_id($data['invoice_id']);
        if ($factura) {
            $CI->Facturama_model->cancelar_factura($factura->uuid);
        }
    }
}