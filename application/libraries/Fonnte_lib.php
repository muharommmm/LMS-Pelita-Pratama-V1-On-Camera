<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fonnte_lib
 *
 * Library helper untuk mengirim pesan WhatsApp melalui API Fonnte.
 * Dokumentasi API: https://fonnte.com/api
 *
 * Cara Pakai di Controller:
 *   $this->load->library('Fonnte_lib');
 *   $result = $this->fonnte_lib->send('08123456789', 'Hello World!');
 */
class Fonnte_lib {

    private $CI;
    private $api_url = 'https://api.fonnte.com/send';
    private $token = '';

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Set API token secara manual (override dari DB).
     * @param string $token
     */
    public function set_token($token) {
        $this->token = $token;
    }

    /**
     * Ambil token dari tabel `setting` kolom `wa_api_token`.
     * @return string
     */
    private function get_token() {
        if (!empty($this->token)) {
            return $this->token;
        }

        $setting = $this->CI->db->get('setting')->row();
        if ($setting && isset($setting->wa_api_token) && !empty($setting->wa_api_token)) {
            $this->token = $setting->wa_api_token;
        }

        return $this->token;
    }

    /**
     * Format nomor HP: 08xx -> 628xx
     * @param string $phone
     * @return string
     */
    public function format_phone($phone) {
        if (strpos($phone, '@g.us') !== false || strpos($phone, ',') !== false) {
            return trim($phone);
        }
        $phone = preg_replace('/[^0-9]/', '', $phone); // Hapus karakter non-angka
        if (substr($phone, 0, 2) === '08') {
            $phone = '62' . substr($phone, 1);
        }
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    /**
     * Kirim pesan WhatsApp ke satu nomor atau grup.
     *
     * @param string $phone Nomor HP penerima atau Group ID Fonnte (@g.us)
     * @param string $message Isi pesan
     * @return array ['success' => bool, 'response' => string, 'detail' => mixed]
     */
    public function send($phone, $message) {
        $token = $this->get_token();
        if (empty($token)) {
            return [
                'success' => false,
                'response' => 'Token Fonnte belum dikonfigurasi. Silahkan isi di Pengaturan WA.',
                'detail' => null
            ];
        }

        $phone = $this->format_phone($phone);

        if (empty($phone) || (strpos($phone, '@g.us') === false && strlen($phone) < 10)) {
            return [
                'success' => false,
                'response' => 'Nomor HP/Grip ID tidak valid: ' . $phone,
                'detail' => null
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => [
                'target'  => $phone,
                'message' => $message,
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $token
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            return [
                'success' => false,
                'response' => 'cURL Error: ' . $err,
                'detail' => null
            ];
        }

        $decoded = json_decode($response, true);
        $is_success = ($http_code == 200 && isset($decoded['status']) && $decoded['status'] === true);

        return [
            'success' => $is_success,
            'response' => $response,
            'detail' => $decoded
        ];
    }

    /**
     * Kirim pesan ke banyak nomor sekaligus.
     *
     * @param array $messages Array of ['phone' => '08xx', 'message' => 'teks']
     * @return array ['total' => int, 'sent' => int, 'failed' => int, 'results' => array]
     */
    public function send_bulk($messages) {
        $results = [];
        $sent = 0;
        $failed = 0;

        foreach ($messages as $msg) {
            $result = $this->send($msg['phone'], $msg['message']);
            $result['phone'] = $msg['phone'];
            $results[] = $result;

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
            }

            // Delay 1 detik antar pesan untuk menghindari rate limit
            usleep(500000); // 0.5 detik
        }

        return [
            'total' => count($messages),
            'sent' => $sent,
            'failed' => $failed,
            'results' => $results
        ];
    }
}
