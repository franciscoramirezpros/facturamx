<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Facturama MX - Módulo de Facturación Electrónica para Perfex CRM
 *
 * @package     Facturamx
 * @author      Tu Nombre
 * @version     1.0.0
 */

class Facturamx extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        if (!is_admin() && !staff_can('view', 'facturamx')) {
            access_denied('facturamx');
        }
        
        $this->load->model('facturamx/Facturama_model');
        $this->load->model('invoices_model');
        $this->load->library('facturamx/Facturamx_lib');
    }

    /**
     * Vista principal/dashboard
     */
    public function index()
    {
        if (!has_permission('facturamx', '', 'view')) {
            access_denied('facturamx');
        }

        $data['title'] = _l('facturamx_module');
        
        // Obtener estadísticas
        $data['total_facturas'] = $this->Facturama_model->get_total_count();
        $data['facturas_activas'] = $this->Facturama_model->get_total_count(['estado' => 'activa']);
        $data['facturas_canceladas'] = $this->Facturama_model->get_total_count(['estado' => 'cancelada']);
        $data['monto_total'] = $this->Facturama_model->get_total_amount();
        $data['grafica_mensual'] = $this->Facturama_model->get_monthly_stats();
        
        $this->load->view('admin/facturamx/dashboard', $data);
    }

    /**
     * Crear nuevo CFDI
     */
    public function crear($invoice_id = null)
    {
        if (!has_permission('facturamx', '', 'create')) {
            access_denied('facturamx');
        }

        if ($invoice_id) {
            $invoice = $this->invoices_model->get($invoice_id);
            if (!$invoice) {
                show_404();
            }

            try {
                $result = $this->Facturama_model->crear_factura_desde_perfex($invoice_id);
                set_alert('success', _l('facturamx_cfdi_creado'));
            } catch (Exception $e) {
                set_alert('danger', $e->getMessage());
            }

            redirect(admin_url('facturamx'));
        }

        $data['title'] = _l('facturamx_crear_cfdi');
        $data['clientes'] = $this->clients_model->get();
        $this->load->view('admin/facturamx/crear', $data);
    }

    /**
     * Ver listado de facturas
     */
    public function ver_facturas()
    {
        if (!has_permission('facturamx', '', 'view')) {
            access_denied('facturamx');
        }

        $data['title'] = _l('facturamx_listado');
        $data['facturas'] = $this->Facturama_model->get_facturas();
        $this->load->view('admin/facturamx/ver_facturas', $data);
    }

    /**
     * Descargar archivo PDF o XML
     */
    public function descargar($tipo, $uuid)
    {
        if (!has_permission('facturamx', '', 'view')) {
            access_denied('facturamx');
        }

        try {
            $content = $this->Facturama_model->get_file($uuid, $tipo);
            
            header('Content-Type: ' . ($tipo === 'pdf' ? 'application/pdf' : 'text/xml'));
            header('Content-Disposition: attachment; filename="cfdi_' . $uuid . '.' . $tipo . '"');
            echo $content;
        } catch (Exception $e) {
            set_alert('danger', $e->getMessage());
            redirect(admin_url('facturamx'));
        }
    }

    /**
     * Cancelar CFDI
     */
    public function cancelar($uuid)
    {
        if (!has_permission('facturamx', '', 'delete')) {
            access_denied('facturamx');
        }

        try {
            $this->Facturama_model->cancelar_factura($uuid);
            set_alert('success', _l('facturamx_cfdi_cancelado'));
        } catch (Exception $e) {
            set_alert('danger', $e->getMessage());
        }

        redirect(admin_url('facturamx'));
    }

    /**
     * Configuración del módulo
     */
    public function configuracion()
    {
        if (!is_admin()) {
            access_denied('facturamx');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            
            update_option('facturama_api_key', $data['api_key']);
            update_option('facturama_api_secret', $data['api_secret']);
            update_option('facturama_sandbox', isset($data['sandbox']) ? 1 : 0);
            update_option('facturama_auto_invoice', isset($data['auto_invoice']) ? 1 : 0);
            update_option('facturama_serie', $data['serie']);
            update_option('facturama_lugar_expedicion', $data['lugar_expedicion']);
            
            set_alert('success', _l('settings_updated'));
            redirect(admin_url('facturamx/configuracion'));
        }

        $data['title'] = _l('facturamx_configuracion');
        $this->load->view('admin/facturamx/configuracion', $data);
    }
}