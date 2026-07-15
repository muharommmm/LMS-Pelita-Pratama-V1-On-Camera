<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Ebooks.php';
$c = file_get_contents($f);

// 1. Modify `upload` method
$old_upload_class = <<<EOT
        \$class_id = \$this->input->post('class_id', true);
        if (\$class_id === '0') {
            \$class_id = null;
        }
EOT;

$new_upload_class = <<<EOT
        \$class_id_post = \$this->input->post('class_id');
        if (is_array(\$class_id_post)) {
            if (in_array('0', \$class_id_post)) {
                \$class_id = null;
            } else {
                \$class_id = implode(',', \$class_id_post);
            }
        } else {
            if (\$class_id_post === '0' || empty(\$class_id_post)) {
                \$class_id = null;
            } else {
                \$class_id = \$class_id_post;
            }
        }
EOT;

$c = str_replace($old_upload_class, $new_upload_class, $c);

// 2. Add `update` method
$update_method = <<<EOT
    /**
     * Update an eBook (Admin Only)
     */
    public function update(\$id_ebook) {
        if (!\$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        \$this->form_validation->set_rules('title', 'Judul E-Book', 'required|trim');
        \$this->form_validation->set_rules('sub_category_type', 'Sub-Kategori', 'required|trim');

        \$sub_category_type = \$this->input->post('sub_category_type', true);
        if (\$sub_category_type === 'mapel') {
            \$this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required|integer');
        } elseif (\$sub_category_type === 'ekskul') {
            \$this->form_validation->set_rules('ekstra_id', 'Ekstrakurikuler', 'required|integer');
        } elseif (\$sub_category_type === 'lainnya') {
            \$this->form_validation->set_rules('custom_category', 'Catatan Khusus', 'required|trim');
        }

        if (\$this->form_validation->run() == FALSE) {
            \$this->session->set_flashdata('error', validation_errors());
            redirect('ebooks');
        }

        \$ebook = \$this->ebook->get_ebook_by_id(\$id_ebook);
        if (!\$ebook) {
            \$this->session->set_flashdata('error', 'E-Book tidak ditemukan.');
            redirect('ebooks');
        }

        \$file_path = \$ebook->file_path;
        if (!empty(\$_FILES['ebook_file']['name'])) {
            \$upload_path = './uploads/ebooks/';
            if (!is_dir(\$upload_path)) {
                mkdir(\$upload_path, 0777, true);
            }

            \$config['upload_path']   = \$upload_path;
            \$config['allowed_types'] = 'pdf|epub|mobi';
            \$config['max_size']      = '51200'; // 50MB max
            \$config['encrypt_name']  = TRUE;

            \$this->upload->initialize(\$config);

            if (!\$this->upload->do_upload('ebook_file')) {
                \$this->session->set_flashdata('error', 'Gagal mengunggah file baru: ' . \$this->upload->display_errors('', ''));
                redirect('ebooks');
            } else {
                // Delete old file
                if (file_exists(FCPATH . \$file_path)) {
                    unlink(FCPATH . \$file_path);
                }
                \$upload_data = \$this->upload->data();
                \$file_path = 'uploads/ebooks/' . \$upload_data['file_name'];
            }
        }

        \$class_id_post = \$this->input->post('class_id');
        if (is_array(\$class_id_post)) {
            if (in_array('0', \$class_id_post)) {
                \$class_id = null;
            } else {
                \$class_id = implode(',', \$class_id_post);
            }
        } else {
            if (\$class_id_post === '0' || empty(\$class_id_post)) {
                \$class_id = null;
            } else {
                \$class_id = \$class_id_post;
            }
        }

        \$mapel_id = null;
        \$ekstra_id = null;
        \$custom_category = null;

        if (\$sub_category_type === 'mapel') {
            \$mapel_id = \$this->input->post('mapel_id', true);
        } elseif (\$sub_category_type === 'ekskul') {
            \$ekstra_id = \$this->input->post('ekstra_id', true);
        } elseif (\$sub_category_type === 'lainnya') {
            \$custom_category = \$this->input->post('custom_category', true);
        }

        \$data = [
            'title' => \$this->input->post('title', true),
            'class_id' => \$class_id,
            'mapel_id' => \$mapel_id,
            'ekstra_id' => \$ekstra_id,
            'custom_category' => \$custom_category,
            'file_path' => \$file_path
        ];

        \$this->db->where('id_ebook', \$id_ebook);
        if (\$this->db->update('ebooks', \$data)) {
            \$this->session->set_flashdata('success', 'E-Book berhasil diperbarui!');
        } else {
            \$this->session->set_flashdata('error', 'Gagal memperbarui data e-book ke database.');
        }

        redirect('ebooks');
    }

EOT;

$c = str_replace("    public function view(\$id_ebook) {", $update_method . "\n    public function view(\$id_ebook) {", $c);

// Need to also modify form_validation in upload method
$old_val = "\$this->form_validation->set_rules('class_id', 'Target Kelas', 'required|integer');";
$new_val = "\$this->form_validation->set_rules('class_id[]', 'Target Kelas', 'required');";
$c = str_replace($old_val, $new_val, $c);

file_put_contents($f, $c);
echo "Updated Ebooks.php\n";
