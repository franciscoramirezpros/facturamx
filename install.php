<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Tabla principal de facturas
if (!$CI->db->table_exists(db_prefix() . 'facturamx')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "facturamx` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `uuid` varchar(36) NOT NULL,
            `rfc_cliente` varchar(13) NOT NULL,
            `nombre_cliente` varchar(255) NOT NULL,
            `fecha` datetime NOT NULL,
            `serie` varchar(25) DEFAULT NULL,
            `folio` varchar(40) DEFAULT NULL,
            `moneda` varchar(3) NOT NULL DEFAULT 'MXN',
            `tipo_comprobante` varchar(1) NOT NULL DEFAULT 'I',
            `forma_pago` varchar(3) DEFAULT NULL,
            `metodo_pago` varchar(3) DEFAULT NULL,
            `uso_cfdi` varchar(3) NOT NULL DEFAULT 'G03',
            `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
            `total` decimal(15,2) NOT NULL DEFAULT '0.00',
            `total_impuestos` decimal(15,2) NOT NULL DEFAULT '0.00',
            `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
            `pdf_path` varchar(255) DEFAULT NULL,
            `xml_path` varchar(255) DEFAULT NULL,
            `staff_id` int(11) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uuid` (`uuid`),
            KEY `invoice_id` (`invoice_id`),
            KEY `rfc_cliente` (`rfc_cliente`),
            KEY `estado` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Tabla para items de la factura
if (!$CI->db->table_exists(db_prefix() . 'facturamx_items')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "facturamx_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `factura_id` int(11) NOT NULL,
            `invoice_item_id` int(11) DEFAULT NULL,
            `clave_producto` varchar(8) NOT NULL,
            `clave_unidad` varchar(3) NOT NULL,
            `descripcion` text NOT NULL,
            `cantidad` decimal(15,6) NOT NULL,
            `valor_unitario` decimal(15,6) NOT NULL,
            `importe` decimal(15,2) NOT NULL,
            `descuento` decimal(15,2) DEFAULT '0.00',
            `objeto_imp` varchar(2) NOT NULL DEFAULT '02',
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `factura_id` (`factura_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Tabla para impuestos de los items
if (!$CI->db->table_exists(db_prefix() . 'facturamx_impuestos')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "facturamx_impuestos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `factura_item_id` int(11) NOT NULL,
            `impuesto` varchar(3) NOT NULL,
            `tipo_factor` varchar(10) NOT NULL,
            `tasa_cuota` decimal(8,6) NOT NULL,
            `base` decimal(15,2) NOT NULL,
            `importe` decimal(15,2) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `factura_item_id` (`factura_item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Tabla para el registro de eventos
if (!$CI->db->table_exists(db_prefix() . 'facturamx_log')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "facturamx_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `factura_id` int(11) NOT NULL,
            `tipo` varchar(50) NOT NULL,
            `descripcion` text NOT NULL,
            `staff_id` int(11) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `factura_id` (`factura_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Agregar campos personalizados necesarios
$custom_fields = [
    [
        'fieldto' => 'customers',
        'name' => 'RFC',
        'slug' => 'rfc',
        'required' => 1,
        'type' => 'input',
        'options' => '',
        'display_inline' => 1
    ],
    [
        'fieldto' => 'customers',
        'name' => 'Régimen Fiscal',
        'slug' => 'regimen_fiscal',
        'required' => 1,
        'type' => 'select',
        'options' => '601,603,605,606,607,608,609,610,611,612,614,615,616,620,621,622,623,624,625',
        'display_inline' => 1
    ],
    [
        'fieldto' => 'customers',
        'name' => 'Uso CFDI',
        'slug' => 'uso_cfdi',
        'required' => 1,
        'type' => 'select',
        'options' => 'G01,G02,G03,I01,I02,I03,I04,I05,I06,I07,I08,D01,D02,D03,D04,D05,D06,D07,D08,D09,D10,P01,CP01,CN01,S01',
        'display_inline' => 1
    ]
];

foreach ($custom_fields as $field) {
    if (!custom_field_exists_by_slug($field['slug'], $field['fieldto'])) {
        $CI->db->insert(db_prefix() . 'customfields', [
            'fieldto' => $field['fieldto'],
            'name' => $field['name'],
            'slug' => $field['slug'],
            'required' => $field['required'],
            'type' => $field['type'],
            'options' => $field['options'],
            'display_inline' => $field['display_inline'],
            'show_on_pdf' => 1,
            'show_on_table' => 1,
            'show_on_client_portal' => 1,
            'disalow_client_to_edit' => 0,
            'bs_column' => 6,
            'active' => 1
        ]);
    }
}

// Crear las opciones por defecto si no existen
$options = [
    'facturama_sandbox' => '1',
    'facturama_api_key' => '',
    'facturama_api_secret' => '',
    'facturama_serie' => '',
    'facturama_regimen_fiscal' => '601',
    'facturama_lugar_expedicion' => '',
    'facturama_auto_invoice' => '0',
    'facturama_auto_cancel' => '0',
    'facturama_email_cfdi' => '1'
];

foreach ($options as $name => $value) {
    add_option($name, $value);
}