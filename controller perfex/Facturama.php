<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facturama extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Check if admin is logged in
        if (!is_admin()) {
            redirect(admin_url());
        }
        
        // Load required models
        $this->load->model('Facturama_model');
        $this->load->model('clients_model');
        $this->load->model('emails_model');
    }

    // Method to create a CFDI invoice
    public function crear()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Validate input data
            if ($this->_validate_input($data)) {
                // Format data for Facturama API
                $invoice_data = [
                    'fecha' => $data['fecha'],
                    'moneda' => $data['moneda'],
                    'rfc_cliente' => $data['rfc_cliente'],
                    'nombre_cliente' => $data['nombre_cliente'],
                    'items' => json_decode($data['items'], true)
                ];

                // Call model method to create invoice
                $uuid = $this->Facturama_model->crear_factura($invoice_data);
                
                if ($uuid) {
                    // Send notification email
                    $this->_enviar_correo_notificacion(
                        $data['nombre_cliente'], 
                        $data['rfc_cliente'], 
                        $uuid
                    );

                    set_alert('success', _l('factura_creada_exito') . $uuid);
                } else {
                    set_alert('danger', _l('error_crear_factura'));
                    log_activity('Error al crear factura en Facturama', get_staff_user_id());
                }
            }
        }

        $data['title'] = _l('crear_factura');
        $this->load->view('admin/facturama/crear_factura', $data);
    }

    // Method to get invoice PDF
    public function obtener_pdf($uuid = '')
    {
        if (empty($uuid)) {
            set_alert('warning', _l('uuid_requerido'));
            redirect(admin_url('facturama/ver_facturas'));
        }

        $pdf = $this->Facturama_model->obtener_pdf_factura($uuid);
            
        if ($pdf) {
            $filename = 'factura_' . $uuid . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $pdf;
        } else {
            set_alert('danger', _l('error_obtener_pdf'));
            log_activity('Error al obtener PDF de factura: ' . $uuid, get_staff_user_id());
            redirect(admin_url('facturamx/ver_facturas'));
        }
    }

    // Method to view invoices with search
    public function ver_facturas()
    {
        $search = $this->input->get('search');
        
        // Get filtered invoices
        $facturas = $this->Facturama_model->get_facturas($search);
        
        $data = [
            'facturas' => $facturas,
            'search' => $search,
            'title' => _l('ver_facturas')
        ];

        $this->load->view('admin/facturamx/ver_facturas', $data);
    }

    // Method to cancel an invoice
    public function cancelar_factura($uuid)
    {
        if (empty($uuid)) {
            set_alert('warning', _l('uuid_requerido'));
            redirect(admin_url('facturamx/ver_facturas'));
        }

        $resultado = $this->Facturama_model->cancelar_factura($uuid);
        
        if ($resultado) {
            // Update invoice status in database
            $this->db->where('uuid', $uuid);
            $this->db->update(db_prefix() . 'facturas', ['estado' => 'Cancelada']);
            
            set_alert('success', _l('factura_cancelada_exito'));
            log_activity('Factura cancelada: ' . $uuid, get_staff_user_id());
        } else {
            set_alert('danger', _l('error_cancelar_factura'));
            log_activity('Error al cancelar factura: ' . $uuid, get_staff_user_id());
        }

        redirect(admin_url('facturamx/ver_facturas'));
    }

    // Method to get CFDI types
    public function obtener_catalogo_cfdi()
    {
        $cfdiTypes = $this->Facturama_model->obtener_catalogo_cfdi();
        
        if ($cfdiTypes) {
            echo json_encode(['success' => true, 'data' => $cfdiTypes]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('error_obtener_cfdi_tipos')]);
            log_activity('Error al obtener catálogo CFDI', get_staff_user_id());
        }
    }

    // Method to get CFDI uses
    public function obtener_usos_cfdi()
    {
        $cfdiUses = $this->Facturama_model->obtener_usos_cfdi();
        
        if ($cfdiUses) {
            echo json_encode(['success' => true, 'data' => $cfdiUses]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('error_obtener_cfdi_usos')]);
            log_activity('Error al obtener usos CFDI', get_staff_user_id());
        }
    }

    // Private method to validate input data
    private function _validate_input($data)
    {
        if (empty($data['rfc_cliente']) || 
            empty($data['nombre_cliente']) || 
            empty($data['fecha']) || 
            empty($data['moneda']) || 
            empty($data['items'])) {
            set_alert('warning', _l('campos_requeridos'));
            return false;
        }

        if (!preg_match("/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/", $data['rfc_cliente'])) {
            set_alert('warning', _l('rfc_invalido'));
            return false;
        }

        if (!in_array($data['moneda'], ['MXN', 'USD'])) {
            set_alert('warning', _l('moneda_invalida'));
            return false;
        }

        if (!filter_var($data['items'], FILTER_VALIDATE_JSON)) {
            set_alert('warning', _l('items_formato_invalido'));
            return false;
        }

        return true;
    }

    // Private method to send notification email
    private function _enviar_correo_notificacion($nombre_cliente, $rfc_cliente, $uuid)
    {
        $email_template = 'factura-creada';
        $merge_fields = [
            '{nombre_cliente}' => $nombre_cliente,
            '{rfc_cliente}' => $rfc_cliente,
            '{uuid}' => $uuid
        ];

        // Get client email from database
        $client = $this->clients_model->get_client_by_rfc($rfc_cliente);
        if ($client && !empty($client->email)) {
            $this->emails_model->send_email_template($email_template, $client->email, $merge_fields);
        } else {
            log_activity('No se pudo enviar email de factura. Cliente no encontrado: ' . $rfc_cliente, get_staff_user_id());
        }
    }
}