<?php
class TestNotif extends CI_Controller {
    public function index() {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Notifikasi_model', 'notif');
        try {
            // Mock data for user 1 (guru) or a siswa (user 3)
            $items = $this->notif->buildFeedSiswa(3, 1, 1, 1, 1);
            echo "SUCCESS:\n";
            print_r($items);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
}
