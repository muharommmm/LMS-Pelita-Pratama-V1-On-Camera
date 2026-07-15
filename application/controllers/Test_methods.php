<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system/');
define('APPPATH', FCPATH . 'application/');
require BASEPATH . 'core/Common.php';

$_SERVER['HTTP_HOST'] = 'localhost';
class Test_methods extends CI_Controller {
    public function index() {
        $this->load->model('Kelas_model', 'kelas');
        $methods = get_class_methods($this->kelas);
        
        $check = [
            'getMateriTugasSiswaDeadline',
            'getLogsSiswa',
            'getMateriKelasSiswa',
            'getMateriSiswa',
            'saveLog',
            'getStatusMateriSiswaByJadwal',
            'loadJadwalSiswaSeminggu',
            'loadJadwalSiswaHariIni',
            'getJadwalKbm',
            'getStatusMateriSiswaGuru',
            'getAllKodeMateri'
        ];
        
        foreach ($check as $m) {
            echo $m . ": " . (in_array($m, $methods) ? "EXISTS" : "MISSING") . "\n";
        }
    }
}
$t = new Test_methods();
$t->index();
