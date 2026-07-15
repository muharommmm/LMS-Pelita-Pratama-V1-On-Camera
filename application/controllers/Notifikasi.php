<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifikasi Controller
 *
 * Endpoints AJAX untuk sistem dashboard aktivitas Guru & Siswa.
 * Semua notifikasi dibuat via PHP (createNotifikasi()), bukan SQL Trigger.
 *
 * Routes:
 *   GET  notifikasi/guru          → feed untuk guru (AJAX)
 *   GET  notifikasi/siswa         → feed untuk siswa (AJAX)
 *   POST notifikasi/baca          → mark as read (id via POST)
 *   POST notifikasi/bacasemua     → mark all as read
 */
class Notifikasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
            exit;
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Notifikasi_model', 'notif');
    }

    private function json($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // ----------------------------------------------------------
    // GET notifikasi/guru
    // Diakses oleh dashboard guru via AJAX polling 30 detik
    // ----------------------------------------------------------
    public function guru() {
        if (!$this->ion_auth->in_group('guru') && !$this->ion_auth->is_admin()) {
            return $this->json(['status' => 'error', 'message' => 'Forbidden']);
        }

        $user  = $this->ion_auth->user()->row();
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        if (!$tp || !$smt) {
            return $this->json(['items' => [], 'unread' => 0]);
        }

        $guru  = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        if (!$guru) {
            return $this->json(['items' => [], 'unread' => 0]);
        }

        $items  = $this->notif->buildFeedGuru($user->id, $guru->id_guru, $tp->id_tp, $smt->id_smt);
        $unread = $this->notif->countUnread($user->id);

        // Format age untuk tampilan
        foreach ($items as &$item) {
            $item['age_label'] = $item['age'] ? $this->notif->formatAge($item['age']) : '';
        }

        $this->json([
            'status'   => 'ok',
            'items'    => $items,
            'unread'   => $unread,
            'generated'=> date('H:i:s'),
        ]);
    }

    // ----------------------------------------------------------
    // GET notifikasi/siswa
    // Diakses oleh dashboard siswa via AJAX polling 30 detik
    // ----------------------------------------------------------
    public function siswa() {
        if (!$this->ion_auth->in_group('siswa')) {
            return $this->json(['status' => 'error', 'message' => 'Forbidden']);
        }

        $user  = $this->ion_auth->user()->row();
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        if (!$tp || !$smt) {
            return $this->json(['items' => [], 'unread' => 0]);
        }

        $siswa = $this->dashboard->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        if (!$siswa) {
            return $this->json(['items' => [], 'unread' => 0]);
        }

        $id_kelas = $siswa->id_kelas ?? null;
        $id_siswa = $siswa->id_siswa ?? null;

        $items  = $this->notif->buildFeedSiswa($user->id, $id_siswa, $id_kelas, $tp->id_tp, $smt->id_smt);
        $unread = $this->notif->countUnread($user->id);

        foreach ($items as &$item) {
            $item['age_label'] = $item['age'] ? $this->notif->formatAge($item['age']) : '';
        }

        $this->json([
            'status'    => 'ok',
            'items'     => $items,
            'unread'    => $unread,
            'generated' => date('H:i:s'),
        ]);
    }

    // ----------------------------------------------------------
    // POST notifikasi/baca
    // Body: {id: 123}
    // ----------------------------------------------------------
    public function baca() {
        $id      = (int)$this->input->post('id');
        $user    = $this->ion_auth->user()->row();
        $result  = $this->notif->markAsRead($id, $user->id);
        $this->json(['status' => 'ok', 'updated' => $result]);
    }

    // ----------------------------------------------------------
    // POST notifikasi/bacasemua
    // ----------------------------------------------------------
    public function bacasemua() {
        $user   = $this->ion_auth->user()->row();
        $result = $this->notif->markAllAsRead($user->id);
        $this->json(['status' => 'ok', 'updated' => $result]);
    }
}
