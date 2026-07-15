<?php
class TestApi extends CI_Controller {
    public function index() {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Notifikasi_model', 'notif');
        // Pretend we are user 3 (siswa)
        $id_kelas = 1;
        $id_tp = 1;
        $id_smt = 1;
        $id_siswa = 1;
        echo "Tugas: ";
        try {
            print_r($this->notif->getLiveTugasSiswa($id_siswa, $id_kelas, $id_tp, $id_smt));
            echo "Materi: ";
            print_r($this->notif->getLiveMateriBaruSiswa($id_kelas, $id_tp, $id_smt, $id_siswa));
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
