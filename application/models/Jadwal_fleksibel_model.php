<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_fleksibel_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Helper: Get tutor name for a specific class and mapel from jabatan_guru assignments
     */
    public function get_tutor_by_class_and_mapel($class_id, $mapel_id, $tp_id, $smt_id) {
        $this->db->select('jabatan_guru.id_guru, master_guru.nama_guru');
        $this->db->from('jabatan_guru');
        $this->db->join('master_guru', 'master_guru.id_guru = jabatan_guru.id_guru');
        $this->db->where('jabatan_guru.id_tp', $tp_id);
        $this->db->where('jabatan_guru.id_smt', $smt_id);
        $gurus = $this->db->get()->result();

        foreach ($gurus as $guru) {
            $jg = $this->db->where([
                'id_guru' => $guru->id_guru,
                'id_tp' => $tp_id,
                'id_smt' => $smt_id
            ])->get('jabatan_guru')->row();

            if ($jg && !empty($jg->mapel_kelas)) {
                $mapels = @unserialize($jg->mapel_kelas);
                if (is_array($mapels)) {
                    foreach ($mapels as $mapel) {
                        $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                        $id_mapel = isset($mapel_arr['id_mapel']) ? $mapel_arr['id_mapel'] : null;
                        
                        if ($id_mapel == $mapel_id) {
                            $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                            if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                                foreach ($kelas_mapel as $km) {
                                    $km_arr = is_object($km) ? (array)$km : $km;
                                    $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : null;
                                    if ($kls_id == $class_id) {
                                        return $guru->nama_guru;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return 'Belum Ditentukan';
    }

    /**
     * Check if a teacher teaches a specific class and mapel
     */
    public function is_tutor_assigned_to_class_mapel($tutor_id, $class_id, $mapel_id, $tp_id, $smt_id) {
        $jg = $this->db->where([
            'id_guru' => $tutor_id,
            'id_tp' => $tp_id,
            'id_smt' => $smt_id
        ])->get('jabatan_guru')->row();

        if ($jg && !empty($jg->mapel_kelas)) {
            $mapels = @unserialize($jg->mapel_kelas);
            if (is_array($mapels)) {
                foreach ($mapels as $mapel) {
                    $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                    $id_mapel = isset($mapel_arr['id_mapel']) ? $mapel_arr['id_mapel'] : null;
                    
                    if ($id_mapel == $mapel_id) {
                        $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                        if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                            foreach ($kelas_mapel as $km) {
                                $km_arr = is_object($km) ? (array)$km : $km;
                                $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : null;
                                if ($kls_id == $class_id) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get all flexible schedules
     */
    public function get_all_flexible_schedules($tp_id, $smt_id) {
        $this->db->select('jadwal_fleksibel.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_mapel.kode');
        $this->db->from('jadwal_fleksibel');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = jadwal_fleksibel.class_id');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = jadwal_fleksibel.mapel_id');
        $this->db->where('jadwal_fleksibel.tp_id', $tp_id);
        $this->db->where('jadwal_fleksibel.smt_id', $smt_id);
        $this->db->order_by('jadwal_fleksibel.day', 'ASC');
        $this->db->order_by('jadwal_fleksibel.start_time', 'ASC');
        
        $schedules = $this->db->get()->result();

        // Enrich with dynamic tutor name
        foreach ($schedules as $s) {
            $s->nama_guru = $this->get_tutor_by_class_and_mapel($s->class_id, $s->mapel_id, $tp_id, $smt_id);
        }

        return $schedules;
    }

    /**
     * Save/Insert schedule
     */
    public function insert_schedule($data) {
        return $this->db->insert('jadwal_fleksibel', $data);
    }

    /**
     * Delete schedule
     */
    public function delete_schedule($id_jadwal) {
        return $this->db->delete('jadwal_fleksibel', ['id_jadwal' => $id_jadwal]);
    }

    /**
     * Get flexible schedules for a student's class
     */
    public function get_schedules_by_class($class_id, $tp_id, $smt_id, $day = null) {
        $minggu_sekarang = (int)date('W');
        $pola_berjalan = ($minggu_sekarang % 2 == 0) ? 'Genap' : 'Ganjil';

        $this->db->select('jadwal_fleksibel.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_mapel.kode');
        $this->db->from('jadwal_fleksibel');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = jadwal_fleksibel.class_id');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = jadwal_fleksibel.mapel_id');
        $this->db->where('jadwal_fleksibel.class_id', $class_id);
        $this->db->where('jadwal_fleksibel.tp_id', $tp_id);
        $this->db->where('jadwal_fleksibel.smt_id', $smt_id);
        if ($day !== null) {
            $this->db->where('jadwal_fleksibel.day', $day);
        }

        $this->db->group_start();
        $this->db->where('jadwal_fleksibel.pola_mingguan', 'Semua');
        $this->db->or_where('jadwal_fleksibel.pola_mingguan', $pola_berjalan);
        $this->db->group_end();

        $this->db->order_by('jadwal_fleksibel.day', 'ASC');
        $this->db->order_by('jadwal_fleksibel.start_time', 'ASC');
        
        $schedules = $this->db->get()->result();

        // Enrich with dynamic tutor name
        foreach ($schedules as $s) {
            $s->nama_guru = $this->get_tutor_by_class_and_mapel($s->class_id, $s->mapel_id, $tp_id, $smt_id);
        }

        return $schedules;
    }

    /**
     * Get flexible schedules for a specific tutor (filtered from all schedules based on assignments)
     */
    public function get_schedules_by_tutor($tutor_id, $tp_id, $smt_id, $day = null) {
        $minggu_sekarang = (int)date('W');
        $pola_berjalan = ($minggu_sekarang % 2 == 0) ? 'Genap' : 'Ganjil';

        $this->db->select('jadwal_fleksibel.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_mapel.kode');
        $this->db->from('jadwal_fleksibel');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = jadwal_fleksibel.class_id');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = jadwal_fleksibel.mapel_id');
        $this->db->where('jadwal_fleksibel.tp_id', $tp_id);
        $this->db->where('jadwal_fleksibel.smt_id', $smt_id);
        if ($day !== null) {
            $this->db->where('jadwal_fleksibel.day', $day);
        }

        $this->db->group_start();
        $this->db->where('jadwal_fleksibel.pola_mingguan', 'Semua');
        $this->db->or_where('jadwal_fleksibel.pola_mingguan', $pola_berjalan);
        $this->db->group_end();

        $this->db->order_by('jadwal_fleksibel.day', 'ASC');
        $this->db->order_by('jadwal_fleksibel.start_time', 'ASC');
        
        $all_schedules = $this->db->get()->result();
        $filtered = [];

        foreach ($all_schedules as $s) {
            if ($this->is_tutor_assigned_to_class_mapel($tutor_id, $s->class_id, $s->mapel_id, $tp_id, $smt_id)) {
                $filtered[] = $s;
            }
        }

        return $filtered;
    }

    /**
     * Check if a tutor has overlapping schedule on a specific day and time range
     */
    public function cek_bentrok($tutor_id, $day, $start_time, $end_time, $tp_id, $smt_id) {
        $this->db->select('class_id, mapel_id, start_time, end_time');
        $this->db->from('jadwal_fleksibel');
        $this->db->where('day', $day);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $schedules = $this->db->get()->result();

        foreach ($schedules as $s) {
            // Overlap condition: s->start_time < end_time AND s->end_time > start_time
            if ($s->start_time < $end_time && $s->end_time > $start_time) {
                if ($this->is_tutor_assigned_to_class_mapel($tutor_id, $s->class_id, $s->mapel_id, $tp_id, $smt_id)) {
                    return true; // Conflict found
                }
            }
        }
        return false;
    }
}
