<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoices_model extends App_Model
{
    protected $table = 'tblinvoices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene una factura por su ID
     */
    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Obtiene todas las facturas
     */
    public function get_all($filters = [])
    {
        if (!empty($filters)) {
            $this->db->where($filters);
        }
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Crea una nueva factura
     */
    public function add($data)
    {
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('Nueva Factura Creada [ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * Actualiza una factura existente
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Factura Actualizada [ID: ' . $id . ']');
        }

        return $this->db->affected_rows() > 0;
    }

    /**
     * Elimina una factura
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);

        if ($this->db->affected_rows() > 0) {
            log_activity('Factura Eliminada [ID: ' . $id . ']');
        }

        return $this->db->affected_rows() > 0;
    }

    /**
     * Cambia el estado de una factura
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, ['status' => $status]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Estado de Factura Cambiado [ID: ' . $id . ', Nuevo Estado: ' . $status . ']');
        }

        return $this->db->affected_rows() > 0;
    }

    /**
     * Obtiene facturas por cliente
     */
    public function get_invoices_by_client($client_id)
    {
        return $this->db->where('clientid', $client_id)->get($this->table)->result_array();
    }

    /**
     * Obtiene el total de facturas
     */
    public function get_total_count($filters = [])
    {
        if (!empty($filters)) {
            $this->db->where($filters);
        }
        return $this->db->count_all_results($this->table);
    }

    /**
     * Busca facturas
     */
    public function search($search_term)
    {
        $this->db->like('number', $search_term);
        $this->db->or_like('clientnote', $search_term);
        $this->db->or_like('adminnote', $search_term);
        return $this->db->get($this->table)->result_array();
    }
}