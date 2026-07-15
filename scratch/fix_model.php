<?php
$model_file = 'C:\xampp\htdocs\garuda_cbt\application\models\Notifikasi_model.php';
$content = file_get_contents($model_file);

// Clean up duplicate entries if they exist in getLiveMateriBaruSiswa
// Original structure around line 250:
//         $this->db->where('km.jenis', 1); // materi
//         $this->db->where('km.status', 1);
//         $this->db->where('km.tgl_mulai <=', $now);
//         $this->db->where('km.created_on >=', date('Y-m-d H:i:s', strtotime('-14 days')));
//         $this->db->where('km.deadline >=', date('Y-m-d H:i:s', strtotime('-1 days')));
//         $this->db->where('km.created_on >=', $since);

$search_duplicate = <<<PHP
        \$this->db->where('km.jenis', 1); // materi
        \$this->db->where('km.status', 1);
        \$this->db->where('km.tgl_mulai <=', \$now);
        \$this->db->where('km.created_on >=', date('Y-m-d H:i:s', strtotime('-14 days')));
        \$this->db->where('km.deadline >=', date('Y-m-d H:i:s', strtotime('-1 days')));
        \$this->db->where('km.created_on >=', \$since);
PHP;

$replace_clean = <<<PHP
        \$this->db->where('km.jenis', 1); // materi
        \$this->db->where('km.status', 1);
        \$this->db->where('km.tgl_mulai <=', \$now);
        \$this->db->where('km.created_on >=', \$since);
PHP;

$content = str_replace($search_duplicate, $replace_clean, $content);
file_put_contents($model_file, $content);

echo "Model cleaned up.\n";
