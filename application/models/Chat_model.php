<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Queries message history between user and target (private) or within a community scope (class/global)
     */
    public function get_riwayat_obrolan($user_id, $lawan_bicara_id, $is_komunitas, $id_kelas) {
        // Fetch active school year and semester to query student class correctly
        $tp = $this->db->where(['active' => 1])->get('master_tp')->row();
        $smt = $this->db->where(['active' => 1])->get('master_smt')->row();
        $tp_id = $tp ? $tp->id_tp : 0;
        $smt_id = $smt ? $smt->id_smt : 0;

        $this->db->select("m.*, 
            CASE 
                WHEN m.pengirim_role = 'admin' THEN COALESCE(NULLIF(TRIM(CONCAT(u.first_name, ' ', u.last_name)), ''), u.username)
                WHEN m.pengirim_role = 'guru' THEN g.nama_guru 
                ELSE s.nama 
            END as nama_pengirim,
            CASE 
                WHEN m.pengirim_role = 'admin' THEN 'assets/img/user.jpg'
                WHEN m.pengirim_role = 'guru' THEN COALESCE(NULLIF(g.foto, ''), 'assets/img/user.jpg')
                ELSE COALESCE(NULLIF(s.foto, ''), 'assets/img/user.jpg')
            END as foto_pengirim,
            mk.nama_kelas as kelas_pengirim");
        $this->db->from('chat_messages m');
        $this->db->join('users u', 'm.pengirim_id = u.id', 'inner');
        $this->db->join('master_guru g', 'u.id = g.id_user', 'left');
        $this->db->join('master_siswa s', 'u.username = s.username', 'left');
        $this->db->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa AND ks.id_tp = ' . $tp_id . ' AND ks.id_smt = ' . $smt_id, 'left');
        $this->db->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'left');

        if ($is_komunitas) {
            $this->db->group_start();
            $this->db->where('m.penerima_id', NULL);
            $this->db->or_where('m.penerima_id', 0);
            $this->db->group_end();

            if (!empty($id_kelas)) {
                $this->db->where('m.id_kelas_komunitas', $id_kelas);
            } else {
                $this->db->group_start();
                $this->db->where('m.id_kelas_komunitas', NULL);
                $this->db->or_where('m.id_kelas_komunitas', 0);
                $this->db->group_end();
            }
        } else {
            $this->db->group_start();
            $this->db->group_start();
            $this->db->where('m.pengirim_id', $user_id);
            $this->db->where('m.penerima_id', $lawan_bicara_id);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('m.pengirim_id', $lawan_bicara_id);
            $this->db->where('m.penerima_id', $user_id);
            $this->db->group_end();
            $this->db->group_end();
            $this->db->where('m.id_kelas_komunitas', NULL);
        }

        $this->db->order_by('m.created_at', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Marks unread messages from a specific sender to the current user as read
     */
    public function tandai_dibaca($user_id, $lawan_bicara_id) {
        $this->db->where('pengirim_id', $lawan_bicara_id);
        $this->db->where('penerima_id', $user_id);
        $this->db->where('is_read', 0);
        return $this->db->update('chat_messages', ['is_read' => 1]);
    }
}
