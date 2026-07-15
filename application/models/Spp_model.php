<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spp_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get student by NIS
     */
    public function get_student_by_nis($nis) {
        return $this->db->where(['nis' => $nis])->get('master_siswa')->row();
    }

    /**
     * Get student by Username
     */
    public function get_student_by_username($username) {
        return $this->db->where(['username' => $username])->get('master_siswa')->row();
    }

    /**
     * Get student by NISN
     */
    public function get_student_by_nisn($nisn) {
        return $this->db->where(['nisn' => $nisn])->get('master_siswa')->row();
    }

    /**
     * Get SPP billing records for a student
     */
    public function get_billing_by_student($student_id, $tp_id, $smt_id) {
        $this->db->select('*');
        $this->db->from('spp_billing');
        $this->db->where('student_id', $student_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $this->db->order_by('month', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Save/Replace billing record
     */
    public function save_billing($data) {
        // Since we have a UNIQUE KEY on student_id, tp_id, smt_id, month
        return $this->db->replace('spp_billing', $data);
    }

    /**
     * Get all billing records for Admin view
     */
    public function get_all_billing($tp_id, $smt_id) {
        $this->db->select('spp_billing.*, master_siswa.nama, master_siswa.nis, master_kelas.nama_kelas');
        $this->db->from('spp_billing');
        $this->db->join('master_siswa', 'master_siswa.id_siswa = spp_billing.student_id');
        $this->db->join('kelas_siswa', 'kelas_siswa.id_siswa = master_siswa.id_siswa AND kelas_siswa.id_tp = ' . $tp_id . ' AND kelas_siswa.id_smt = ' . $smt_id, 'left');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = kelas_siswa.id_kelas', 'left');
        $this->db->where('spp_billing.tp_id', $tp_id);
        $this->db->where('spp_billing.smt_id', $smt_id);
        $this->db->order_by('master_kelas.nama_kelas', 'ASC');
        $this->db->order_by('master_siswa.nama', 'ASC');
        $this->db->order_by('spp_billing.month', 'ASC');
        return $this->db->get()->result();
    }
}
