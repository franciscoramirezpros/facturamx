<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Facturamx_lib {
    protected $CI;
    private $api_key;
    private $api_secret;
    private $sandbox_mode;
    private $client;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        // Cargar configuración
        $this->CI->config->load('facturamx/config');
        $this->api_key = get_option('facturama_api_key');
        $this->api_secret = get_option('facturama_api_secret');
        $this->sandbox_mode = get_option('facturama_sandbox') === '1';
        
        // Definir el path base para las librerías de Facturama
        $base_path = FCPATH . 'modules/facturamx/libraries/Facturama/';
        
        // Cargar las librerías de Facturama en orden específico
        $facturama_libs = [
            'CatalogInterface.php',
            'Request.php',
            'Utils.php',
            'Services/CFDI.php',
            'CfdiType.php',
            'CfdiUse.php',
            'Client.php',
            'FacturamaException.php',
            'FederalTax.php',
            'ModelException.php',
            'PaymentForm.php',
            'PaymentMethod.php',
            'RequestException.php',
            'ResponseException.php'
        ];

        // Cargar cada librería
        foreach ($facturama_libs as $lib) {
            $lib_path = $base_path . $lib;
            if (file_exists($lib_path)) {
                require_once($lib_path);
            } else {
                log_activity('Facturamx: Unable to load library ' . $lib_path);
            }
        }
        
        // Inicializar el cliente
        $this->initialize_api();
    }
    
    public function initialize_api()
    {
        if (empty($this->api_key) || empty($this->api_secret)) {
            log_activity('Facturamx: API credentials are not configured');
            return false;
        }

        try {
            $this->client = new \Facturama\Client(
                $this->api_key,
                $this->api_secret,
                !$this->sandbox_mode
            );
            return true;
        } catch (Exception $e) {
            log_activity('Facturamx API Error: ' . $e->getMessage());
            return false;
        }
    }

    public function get_client()
    {
        if (!$this->is_initialized()) {
            $this->initialize_api();
        }
        return $this->client;
    }

    public function is_initialized()
    {
        return isset($this->client) && $this->client !== null;
    }

    // Métodos de ayuda para CFDI
    public function crear_cfdi($data)
    {
        try {
            return $this->get_client()->Cfdi->create($data);
        } catch (Exception $e) {
            log_activity('Error al crear CFDI: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cancelar_cfdi($uuid, $motivo = '02')
    {
        try {
            return $this->get_client()->Cfdi->cancel($uuid, $motivo);
        } catch (Exception $e) {
            log_activity('Error al cancelar CFDI: ' . $e->getMessage());
            throw $e;
        }
    }

    public function descargar_cfdi($uuid, $formato = 'pdf')
    {
        try {
            return $this->get_client()->Cfdi->download($uuid, $formato);
        } catch (Exception $e) {
            log_activity('Error al descargar CFDI: ' . $e->getMessage());
            throw $e;
        }
    }
}