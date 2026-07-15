<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agenda_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get active agendas by user role and optional class
     */
    public function get_agendas_by_role($role, $class_id = null) {
        $this->db->select('*');
        $this->db->from('agendas');
        $this->db->group_start();
        $this->db->where('target_role', 'all');
        $this->db->or_where('target_role', $role);
        $this->db->group_end();
        
        if ($class_id !== null) {
            $this->db->group_start();
            $this->db->where('target_class_id', $class_id);
            $this->db->or_where('target_class_id', NULL);
            $this->db->group_end();
        }
        
        $this->db->where('end_date >=', date('Y-m-d H:i:s'));
        $this->db->order_by('start_date', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get all agendas (Admin list)
     */
    public function get_all_agendas() {
        $this->db->select('agendas.*, master_kelas.nama_kelas');
        $this->db->from('agendas');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = agendas.target_class_id', 'left');
        $this->db->order_by('agendas.start_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert agenda
     */
    public function insert_agenda($data) {
        return $this->db->insert('agendas', $data);
    }

    /**
     * Delete agenda
     */
    public function delete_agenda($id_agenda) {
        return $this->db->delete('agendas', ['id_agenda' => $id_agenda]);
    }
}
