<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get barcode setting by Class ID
     */
    public function get_barcode_by_class($class_id) {
        return $this->db->where(['class_id' => $class_id])->get('absensi_setting_barcode')->row();
    }

    /**
     * Save or update barcode setting
     */
    public function save_barcode($data) {
        return $this->db->replace('absensi_setting_barcode', $data);
    }

    /**
     * Get all students in a class
     */
    public function get_students_by_class($class_id, $tp_id, $smt_id) {
        $this->db->select('master_siswa.id_siswa, master_siswa.nama, master_siswa.nis, master_siswa.nisn');
        $this->db->from('kelas_siswa');
        $this->db->join('master_siswa', 'master_siswa.id_siswa = kelas_siswa.id_siswa');
        $this->db->where('kelas_siswa.id_kelas', $class_id);
        $this->db->where('kelas_siswa.id_tp', $tp_id);
        $this->db->where('kelas_siswa.id_smt', $smt_id);
        $this->db->order_by('master_siswa.nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get specific class attendance records for a mapel and date
     */
    public function get_attendance($class_id, $mapel_id, $date) {
        $this->db->select('*');
        $this->db->from('absensi_siswa');
        $this->db->where('class_id', $class_id);
        $this->db->where('mapel_id', $mapel_id);
        $this->db->where('date', $date);
        $results = $this->db->get()->result();

        $mapped = [];
        foreach ($results as $row) {
            $mapped[$row->student_id] = $row;
        }
        return $mapped;
    }

    /**
     * Save single attendance record
     */
    public function save_attendance($data) {
        return $this->db->replace('absensi_siswa', $data);
    }

    /**
     * Get attendance summary/recap for a class
     */
    public function get_attendance_recap($class_id, $tp_id, $smt_id, $mapel_id = null) {
        $this->db->select('
            master_siswa.id_siswa, 
            master_siswa.nama, 
            master_siswa.nis, 
            SUM(CASE WHEN absensi_siswa.status = "H" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN absensi_siswa.status = "S" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN absensi_siswa.status = "I" THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN absensi_siswa.status = "A" THEN 1 ELSE 0 END) as alpha
        ');
        $this->db->from('kelas_siswa');
        $this->db->join('master_siswa', 'master_siswa.id_siswa = kelas_siswa.id_siswa');
        $this->db->join('absensi_siswa', 'absensi_siswa.student_id = master_siswa.id_siswa AND absensi_siswa.class_id = kelas_siswa.id_kelas', 'left');
        $this->db->where('kelas_siswa.id_kelas', $class_id);
        $this->db->where('kelas_siswa.id_tp', $tp_id);
        $this->db->where('kelas_siswa.id_smt', $smt_id);
        if ($mapel_id !== null) {
            $this->db->where('absensi_siswa.mapel_id', $mapel_id);
        }
        $this->db->group_by('master_siswa.id_siswa');
        $this->db->order_by('master_siswa.nama', 'ASC');
        return $this->db->get()->result();
    }
}
