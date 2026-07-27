<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ebook_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all ebooks with class names, mapel names, ekstra names, and uploader details
     */
    public function get_all_ebooks() {
        $this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name');
        $this->db->from('ebooks');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        $this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        $this->db->join('users', 'users.id = ebooks.created_by', 'left');
        $this->db->order_by('ebooks.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ebooks assigned to a specific class
     */
    public function get_ebooks_by_class($class_id) {
        $this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name');
        $this->db->from('ebooks');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        $this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        $this->db->join('users', 'users.id = ebooks.created_by', 'left');
        $this->db->where('ebooks.class_id', $class_id);
        $this->db->order_by('ebooks.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ebooks assigned to multiple classes
     */
    public function get_ebooks_by_classes($class_ids) {
        if (empty($class_ids)) {
            return [];
        }
        $this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name');
        $this->db->from('ebooks');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        $this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        $this->db->join('users', 'users.id = ebooks.created_by', 'left');
        $this->db->where_in('ebooks.class_id', $class_ids);
        $this->db->order_by('ebooks.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ebooks for student view with reading progress
     */
    public function get_ebooks_for_student($class_id, $user_id) {
        $this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name, ebook_reading_history.last_page, ebook_reading_history.total_pages');
        $this->db->from('ebooks');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        $this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        $this->db->join('users', 'users.id = ebooks.created_by', 'left');
        $this->db->join('ebook_reading_history', 'ebook_reading_history.ebook_id = ebooks.id_ebook AND ebook_reading_history.user_id = ' . (int)$user_id, 'left');
        
        // Filter by class using FIND_IN_SET
        $this->db->where("(ebooks.class_id = '' OR ebooks.class_id IS NULL OR FIND_IN_SET('$class_id', ebooks.class_id) > 0)");
        
        $this->db->order_by('ebooks.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ebooks for tutor view based on their taught subjects
     */
    public function get_ebooks_for_tutor($class_ids, $mapel_ids) {
        if (empty($mapel_ids)) {
            return [];
        }
        $this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name');
        $this->db->from('ebooks');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        $this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        $this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        $this->db->join('users', 'users.id = ebooks.created_by', 'left');
        
        $this->db->where_in('ebooks.mapel_id', $mapel_ids);
        $this->db->order_by('ebooks.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get all subjects
     */
    public function get_all_mapels($for_filter = false) {
        if ($for_filter) {
            $this->db->select('master_mapel.*');
            $this->db->from('master_mapel');
            $this->db->join('ebooks', 'ebooks.mapel_id = master_mapel.id_mapel', 'left');
            $this->db->group_by('master_mapel.id_mapel');
            $this->db->order_by('master_mapel.nama_mapel', 'ASC');
            return $this->db->get()->result();
        }
        return $this->db->order_by('nama_mapel', 'ASC')->get('master_mapel')->result();
    }

    /**
     * Get all extracurriculars
     */
    public function get_all_ekskuls($for_filter = false) {
        if ($for_filter) {
            $this->db->select('master_ekstra.*');
            $this->db->from('master_ekstra');
            $this->db->join('ebooks', 'ebooks.ekstra_id = master_ekstra.id_ekstra', 'left');
            $this->db->group_by('master_ekstra.id_ekstra');
            $this->db->order_by('master_ekstra.nama_ekstra', 'ASC');
            return $this->db->get()->result();
        }
        return $this->db->order_by('nama_ekstra', 'ASC')->get('master_ekstra')->result();
    }

    /**
     * Get reading progress for a user and ebook
     */
    public function get_reading_history($user_id, $ebook_id) {
        return $this->db->where([
            'user_id' => $user_id,
            'ebook_id' => $ebook_id
        ])->get('ebook_reading_history')->row();
    }

    /**
     * Save reading progress for a user and ebook
     */
    public function save_reading_history($user_id, $ebook_id, $last_page, $total_pages) {
        $existing = $this->get_reading_history($user_id, $ebook_id);
        if ($existing) {
            $this->db->where('id_history', $existing->id_history);
            return $this->db->update('ebook_reading_history', [
                'last_page' => $last_page,
                'total_pages' => $total_pages
            ]);
        } else {
            return $this->db->insert('ebook_reading_history', [
                'user_id' => $user_id,
                'ebook_id' => $ebook_id,
                'last_page' => $last_page,
                'total_pages' => $total_pages
            ]);
        }
    }

    /**
     * Get all classes assigned/taught by a teacher
     */
    public function get_tutor_classes($tutor_id, $tp_id, $smt_id) {
        $class_ids = [];

        // Homeroom (walikelas)
        $this->db->select('master_kelas.id_kelas, master_kelas.nama_kelas');
        $this->db->from('jabatan_guru');
        $this->db->join('master_kelas', 'master_kelas.id_kelas = jabatan_guru.id_kelas');
        $this->db->where('jabatan_guru.id_guru', $tutor_id);
        $this->db->where('jabatan_guru.id_tp', $tp_id);
        $this->db->where('jabatan_guru.id_smt', $smt_id);
        $homeroom = $this->db->get()->result_array();

        foreach ($homeroom as $h) {
            if ($h['id_kelas'] > 0) {
                $class_ids[$h['id_kelas']] = $h['nama_kelas'];
            }
        }

        // Taught classes (mapel_kelas column)
        $this->db->select('mapel_kelas');
        $this->db->from('jabatan_guru');
        $this->db->where('id_guru', $tutor_id);
        $this->db->where('id_tp', $tp_id);
        $this->db->where('id_smt', $smt_id);
        $assignments = $this->db->get()->result();

        foreach ($assignments as $assign) {
            if (!empty($assign->mapel_kelas)) {
                $mapels = @unserialize($assign->mapel_kelas);
                if (is_array($mapels)) {
                    foreach ($mapels as $mapel) {
                        // Check if array or object
                        $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                        $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                        if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                            foreach ($kelas_mapel as $km) {
                                $km_arr = is_object($km) ? (array)$km : $km;
                                $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : null;
                                if ($kls_id) {
                                    $class_row = $this->db->where('id_kelas', $kls_id)->get('master_kelas')->row();
                                    if ($class_row) {
                                        $class_ids[$kls_id] = $class_row->nama_kelas;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $class_ids;
    }

    /**
     * Insert a new ebook record
     */
    public function insert_ebook($data) {
        return $this->db->insert('ebooks', $data);
    }

    /**
     * Get an ebook by ID
     */
    public function get_ebook_by_id($id_ebook) {
        return $this->db->where('id_ebook', $id_ebook)->get('ebooks')->row();
    }

    /**
     * Delete an ebook record
     */
    public function delete_ebook($id_ebook) {
        return $this->db->delete('ebooks', ['id_ebook' => $id_ebook]);
    }
}

