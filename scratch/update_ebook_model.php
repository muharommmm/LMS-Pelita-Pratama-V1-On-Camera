<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\models\Ebook_model.php';
$c = file_get_contents($f);

$old_func = <<<EOT
    public function get_ebooks_for_student(\$class_id, \$user_id) {
        \$this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name, ebook_reading_history.last_page, ebook_reading_history.total_pages');
        \$this->db->from('ebooks');
        \$this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        \$this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        \$this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        \$this->db->join('users', 'users.id = ebooks.created_by', 'left');
        \$this->db->join('ebook_reading_history', 'ebook_reading_history.ebook_id = ebooks.id_ebook AND ebook_reading_history.user_id = ' . (int)\$user_id, 'left');
        
        // Show all ebooks without strict class filtering
        \$this->db->order_by('ebooks.created_at', 'DESC');
        return \$this->db->get()->result();
    }
EOT;

$new_func = <<<EOT
    public function get_ebooks_for_student(\$class_id, \$user_id) {
        \$this->db->select('ebooks.*, master_kelas.nama_kelas, master_mapel.nama_mapel, master_ekstra.nama_ekstra, users.first_name, users.last_name, ebook_reading_history.last_page, ebook_reading_history.total_pages');
        \$this->db->from('ebooks');
        \$this->db->join('master_kelas', 'master_kelas.id_kelas = ebooks.class_id', 'left');
        \$this->db->join('master_mapel', 'master_mapel.id_mapel = ebooks.mapel_id', 'left');
        \$this->db->join('master_ekstra', 'master_ekstra.id_ekstra = ebooks.ekstra_id', 'left');
        \$this->db->join('users', 'users.id = ebooks.created_by', 'left');
        \$this->db->join('ebook_reading_history', 'ebook_reading_history.ebook_id = ebooks.id_ebook AND ebook_reading_history.user_id = ' . (int)\$user_id, 'left');
        
        // Filter by class using FIND_IN_SET
        \$this->db->where("(ebooks.class_id = '' OR ebooks.class_id IS NULL OR FIND_IN_SET('\$class_id', ebooks.class_id) > 0)");
        
        \$this->db->order_by('ebooks.created_at', 'DESC');
        return \$this->db->get()->result();
    }
EOT;

$c = str_replace($old_func, $new_func, $c);
file_put_contents($f, $c);
echo "Updated Ebook_model.php\n";
