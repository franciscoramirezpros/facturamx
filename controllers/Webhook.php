<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('facturama/Facturama_model');
    }

    public function process($token = '')
    {
        // Verificar token de seguridad si está configurado
        if (get_option('facturama_webhook_token') && $token !== get_option('facturama_webhook_token')) {
            header('HTTP/1.0 403 Forbidden');
            exit('Access Denied');
        }

        // Obtener datos del webhook
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || !isset($data['eventType'])) {
            header('HTTP/1.0 400 Bad Request');
            exit('Invalid webhook data');
        }

        try {
            switch ($data['eventType']) {
                case 'cfdi.created':
                    $this->process_cfdi_created($data);
                    break;
                    
                case 'cfdi.canceled':
                    $this->process_cfdi_canceled($data);
                    break;
                    
                case 'cfdi.error':
                    $this->process_cfdi_error($data);
                    break;
                
                default:
                    log_activity('Webhook: Evento desconocido recibido - ' . $data['eventType']);
                    break;
            }

            // Responder éxito
            header('HTTP/1.1 200 OK');
            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            // Registrar error
            log_activity('Error procesando webhook: ' . $e->getMessage());
            
            // Responder error
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function process_cfdi_created($data)
    {
        if (isset($data['data']['uuid']) && isset($data['data']['invoiceId'])) {
            // Buscar la factura en el sistema
            $factura = $this->Facturama_model->get_by_invoice_id($data['data']['invoiceId']);
            
            if ($factura) {
                // Actualizar estado y UUID
                $this->Facturama_model->actualizar_estado($factura->id, [
                    'uuid' => $data['data']['uuid'],
                    'estado' => 'activa',
                    'fecha_actualizacion' => date('Y-m-d H:i:s')
                ]);

                // Descargar archivos si están disponibles
                if (isset($data['data']['files'])) {
                    $this->Facturama_model->guardar_archivos_factura(
                        $data['data']['uuid'],
                        $data['data']['files']
                    );
                }

                log_activity('CFDI creado vía webhook: ' . $data['data']['uuid']);
            }
        }
    }

    private function process_cfdi_canceled($data)
    {
        if (isset($data['data']['uuid'])) {
            // Actualizar estado de la factura
            $this->Facturama_model->actualizar_por_uuid($data['data']['uuid'], [
                'estado' => 'cancelada',
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            log_activity('CFDI cancelado vía webhook: ' . $data['data']['uuid']);
        }
    }

    private function process_cfdi_error($data)
    {
        if (isset($data['data']['uuid'])) {
            // Actualizar estado de error
            $this->Facturama_model->actualizar_por_uuid($data['data']['uuid'], [
                'estado' => 'error',
                'mensaje_error' => $data['data']['message'] ?? 'Error desconocido',
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            log_activity('Error en CFDI vía webhook: ' . $data['data']['uuid'] . 
                        ' - ' . ($data['data']['message'] ?? 'Error desconocido'));
        }
    }
}