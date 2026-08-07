<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
 defined("\x42\x41\123\105\120\101\124\x48") or exit("\116\x6f\40\144\x69\x72\145\143\x74\x20\163\x63\162\x69\x70\164\x20\x61\x63\x63\145\x73\163\40\x61\x6c\x6c\157\167\x65\x64"); class Welcome extends CI_Controller {
    public function index() {
        redirect('auth');
    }

    public function manifest() {
        // 1. Ambil data log_materi yang berkaitan dengan materi 19
        $logs = $this->db->select('a.*, s.nama as nama_siswa')
                         ->from('log_materi a')
                         ->join('master_siswa s', 's.id_siswa = a.id_siswa', 'left')
                         ->where('a.id_materi', 19)
                         ->or_where('a.id_siswa', 30)
                         ->get()->result();

        // 2. Ambil data siswa dengan ID 30 untuk memastikan nama Siswa A
        $siswa_a = $this->db->where('id_siswa', 30)->get('master_siswa')->row();

        $res = [
            "info" => "Diagnosis Tugas Materi 19 & Siswa ID 30",
            "siswa_id_30" => $siswa_a ? $siswa_a->nama : "Tidak ditemukan",
            "log_records" => $logs
        ];

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($res, JSON_PRETTY_PRINT));
    }
}
