<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Facturama_model extends App_Model
{
    private $facturama;
    private $table = 'tblfacturamx';

    public function __construct()
    {
        parent::__construct();
        $this->init_facturama();
    }

    /**
     * Inicializa el cliente de Facturama
     */
    private function init_facturama()
    {
        // Obtener credenciales
        $client_id = get_option('facturama_api_key');
        $client_secret = get_option('facturama_api_secret');
        $is_sandbox = get_option('facturama_sandbox') === '1';

        // Cargar librerías
        $this->load_facturama_libraries();

        // Inicializar cliente
        try {
            $this->facturama = new Client($client_id, $client_secret, !$is_sandbox);
        } catch (Exception $e) {
            log_activity('Error de inicialización Facturama: ' . $e->getMessage());
        }
    }

    /**
     * Carga las librerías de Facturama
     */
    private function load_facturama_libraries()
    {
        // Cambiar la ruta para que use la estructura de módulos
        $library_path = dirname(__FILE__) . '/../libraries/';
        
        // Reordenar las librerías para cargar primero las dependencias
        $libs = [
            'FacturamaException.php',    // Excepciones primero
            'ModelException.php',
            'RequestException.php',
            'ResponseException.php',
            'CatalogInterface.php',      // Interfaces después
            'FederalTax.php',
            'Client.php',                // Cliente después de sus dependencias
            'CfdiType.php',             // Resto de clases
            'CfdiUse.php',
            'PaymentForm.php',
            'PaymentMethod.php'
        ];
    
        foreach ($libs as $lib) {
            $file_path = $library_path . $lib;
            if (!file_exists($file_path)) {
                throw new Exception('No se encuentra la librería: ' . $file_path);
            }
            require_once $file_path;
        }
    }

    /**
     * Obtiene el catálogo de CFDI
     */
    public function obtener_catalogo_cfdi()
    {
        try {
            return $this->facturama->CfdiType->getAll();
        } catch (Exception $e) {
            log_activity('Error al obtener catálogo CFDI: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los usos de CFDI
     */
    public function obtener_usos_cfdi()
    {
        try {
            return $this->facturama->CfdiUse->getAll();
        } catch (Exception $e) {
            log_activity('Error al obtener usos CFDI: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las formas de pago
     */
    public function obtener_formas_pago()
    {
        try {
            return $this->facturama->PaymentForm->getAll();
        } catch (Exception $e) {
            log_activity('Error al obtener formas de pago: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los métodos de pago
     */
    public function obtener_metodos_pago()
    {
        try {
            return $this->facturama->PaymentMethod->getAll();
        } catch (Exception $e) {
            log_activity('Error al obtener métodos de pago: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una factura CFDI
     */
    public function actualizar_factura($id, $data)
    {
        try {
            $factura = $this->db->where('id', $id)->get($this->table)->row();
            if (!$factura) {
                throw new Exception('Factura no encontrada');
            }

            // Actualizar en Facturama si es necesario
            if (isset($data['estado']) && $data['estado'] === 'cancelada') {
                $this->cancelar_factura($factura->uuid);
            }

            // Actualizar en base de datos
            $this->db->where('id', $id);
            $this->db->update($this->table, array_merge($data, [
                'fecha_actualizacion' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id()
            ]));

            return true;
        } catch (Exception $e) {
            log_activity('Error al actualizar factura: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica el estado de una factura en Facturama
     */
    public function verificar_estado_factura($uuid)
    {
        try {
            $response = $this->facturama->retrieveInvoice($uuid);
            if ($response && isset($response['Status'])) {
                $this->actualizar_por_uuid($uuid, [
                    'estado' => strtolower($response['Status']),
                    'fecha_actualizacion' => date('Y-m-d H:i:s')
                ]);
                return $response['Status'];
            }
            return false;
        } catch (Exception $e) {
            log_activity('Error al verificar estado de factura: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reenvía el CFDI por correo
     */
    public function reenviar_cfdi($uuid, $email)
    {
        try {
            $result = $this->facturama->sendInvoiceByEmail($uuid, $email);
            if ($result) {
                log_activity('CFDI reenviado: ' . $uuid . ' a ' . $email);
                return true;
            }
            return false;
        } catch (Exception $e) {
            log_activity('Error al reenviar CFDI: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Relaciona facturas (para notas de crédito)
     */
    public function relacionar_facturas($uuid_origen, $uuid_destino, $tipo_relacion = '01')
    {
        try {
            $result = $this->facturama->relateInvoices($uuid_origen, $uuid_destino, $tipo_relacion);
            if ($result) {
                log_activity('Facturas relacionadas: ' . $uuid_origen . ' con ' . $uuid_destino);
                return true;
            }
            return false;
        } catch (Exception $e) {
            log_activity('Error al relacionar facturas: ' . $e->getMessage());
            return false;
        }
    }

/**
     * Obtiene estadísticas de facturación
     */
    public function get_estadisticas($filtros = [])
    {
        $this->db->select([
            'COUNT(*) as total',
            'SUM(CASE WHEN estado = "activa" THEN 1 ELSE 0 END) as activas',
            'SUM(CASE WHEN estado = "cancelada" THEN 1 ELSE 0 END) as canceladas',
            'SUM(total) as monto_total',
            'DATE_FORMAT(fecha_creacion, "%Y-%m") as mes'
        ]);
        
        $this->db->from($this->table);
        
        if (isset($filtros['desde'])) {
            $this->db->where('fecha_creacion >=', $filtros['desde']);
        }
        
        if (isset($filtros['hasta'])) {
            $this->db->where('fecha_creacion <=', $filtros['hasta']);
        }
        
        $this->db->group_by('mes');
        $this->db->order_by('mes', 'DESC');
        
        return $this->db->get()->result_array();
    }
}