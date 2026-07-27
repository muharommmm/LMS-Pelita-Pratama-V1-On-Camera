<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Check if user is logged in
        if (!$this->ion_auth->logged_in()) {
            // If AJAX request, return unauthorized JSON, else redirect to login
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized access.']))
                    ->_display();
                exit;
            }
            redirect('auth');
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Chat_model', 'chat_model');
        $this->load->library('form_validation');
    }

    /**
     * Entry point to load the chat UI
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $unread_counts = [];
        $unread_query = $this->db->select('pengirim_id, COUNT(*) as count')
            ->from('chat_messages')
            ->where('penerima_id', $user->id)
            ->where('is_read', 0)
            ->group_by('pengirim_id')
            ->get()->result();
        foreach ($unread_query as $ur) {
            $unread_counts[$ur->pengirim_id] = intval($ur->count);
        }

        $kontak = [];
        $kelas_id = null;
        $nama_kelas = null;

        // Determine user group and load their specific contact list
        if ($this->ion_auth->in_group('siswa')) {
            $siswa = $this->db->where(['username' => $user->username])->get('master_siswa')->row();
            if ($siswa) {
                // Get student's class
                $kelas_siswa = $this->db->where([
                    'id_siswa' => $siswa->id_siswa,
                    'id_tp' => $tp->id_tp,
                    'id_smt' => $smt->id_smt
                ])->get('kelas_siswa')->row();
                if ($kelas_siswa) {
                    $kelas_id = $kelas_siswa->id_kelas;
                    $mk = $this->db->where(['id_kelas' => $kelas_id])->get('master_kelas')->row();
                    if ($mk) {
                        $nama_kelas = $mk->nama_kelas;
                        $unread_comm = $this->db->where([
                            'user_id' => $user->id,
                            'type' => 'chat_masuk',
                            'url' => 'chat?user=komunitas_' . $kelas_id,
                            'is_read' => 0
                        ])->count_all_results('dashboard_notifications');

                        // Prepend student's specific class community
                        $kontak[] = (object)[
                            'id_user' => 'komunitas_' . $kelas_id,
                            'nama' => 'Komunitas Kelas ' . $mk->nama_kelas,
                            'role' => 'komunitas',
                            'kelas' => null,
                            'unread' => $unread_comm
                        ];
                    }
                }
            }

            // Get teachers teaching this student
            $teachers = $this->_get_kontak_guru($user->username, $tp->id_tp, $smt->id_smt);
            foreach ($teachers as $t) {
                $kontak[] = (object)[
                    'id_user' => $t->id_user,
                    'nama' => $t->nama_guru,
                    'role' => 'guru',
                    'kelas' => null,
                    'unread' => isset($unread_counts[$t->id_user]) ? $unread_counts[$t->id_user] : 0
                ];
            }

            // Get Admins
            $admins = $this->ion_auth->users('admin')->result();
            foreach ($admins as $ad) {
                $kontak[] = (object)[
                    'id_user' => $ad->id,
                    'nama' => trim($ad->first_name . ' ' . $ad->last_name) ?: $ad->username,
                    'role' => 'admin',
                    'kelas' => null,
                    'unread' => isset($unread_counts[$ad->id]) ? $unread_counts[$ad->id] : 0
                ];
            }
        } elseif ($this->ion_auth->in_group('guru')) {
            $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
            
            // Get admins
            $admins = $this->ion_auth->users('admin')->result();
            foreach ($admins as $ad) {
                $kontak[] = (object)[
                    'id_user' => $ad->id,
                    'nama' => trim($ad->first_name . ' ' . $ad->last_name) ?: $ad->username,
                    'role' => 'admin',
                    'kelas' => null,
                    'unread' => isset($unread_counts[$ad->id]) ? $unread_counts[$ad->id] : 0
                ];
            }

            // Get students and class community rooms in classes taught by this teacher
            if ($guru) {
                $jg = $this->db->where([
                    'id_guru' => $guru->id_guru,
                    'id_tp' => $tp->id_tp,
                    'id_smt' => $smt->id_smt
                ])->get('jabatan_guru')->row();
                if ($jg) {
                    $class_ids = [];
                    if ($jg->id_kelas > 0) {
                        $class_ids[] = $jg->id_kelas;
                    }
                    if (!empty($jg->mapel_kelas)) {
                        $mapels = @unserialize($jg->mapel_kelas);
                        if (is_array($mapels)) {
                            foreach ($mapels as $mapel) {
                                $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                                $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                                if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                                    foreach ($kelas_mapel as $km) {
                                        $km_arr = is_object($km) ? (array)$km : $km;
                                        $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : null;
                                        if ($kls_id) {
                                            $class_ids[] = $kls_id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $class_ids = array_unique($class_ids);
                    if (!empty($class_ids)) {
                        // Prepend class communities for classes taught by this teacher
                        $this->db->where_in('id_kelas', $class_ids);
                        $kelas_taught = $this->db->get('master_kelas')->result();
                        foreach ($kelas_taught as $kt) {
                            $unread_comm = $this->db->where([
                                'user_id' => $user->id,
                                'type' => 'chat_masuk',
                                'url' => 'chat?user=komunitas_' . $kt->id_kelas,
                                'is_read' => 0
                            ])->count_all_results('dashboard_notifications');

                            $kontak[] = (object)[
                                'id_user' => 'komunitas_' . $kt->id_kelas,
                                'nama' => 'Komunitas Kelas ' . $kt->nama_kelas,
                                'role' => 'komunitas',
                                'kelas' => null,
                                'unread' => $unread_comm
                            ];
                        }

                        // Retrieve students with class names
                        $this->db->select('u.id as id_user, s.nama, mk.nama_kelas');
                        $this->db->from('master_siswa s');
                        $this->db->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa AND ks.id_tp = ' . $tp->id_tp . ' AND ks.id_smt = ' . $smt->id_smt, 'inner');
                        $this->db->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'inner');
                        $this->db->join('users u', 's.username = u.username', 'inner');
                        $this->db->where_in('ks.id_kelas', $class_ids);
                        $students = $this->db->get()->result();
                        foreach ($students as $st) {
                            $kontak[] = (object)[
                                'id_user' => $st->id_user,
                                'nama' => $st->nama,
                                'role' => 'siswa',
                                'kelas' => $st->nama_kelas,
                                'unread' => isset($unread_counts[$st->id_user]) ? $unread_counts[$st->id_user] : 0
                            ];
                        }
                    }
                }
            }
        } else {
            // Admin: Prepend all class communities
            $all_kelas = $this->db->get('master_kelas')->result();
            foreach ($all_kelas as $ak) {
                $unread_comm = $this->db->where([
                    'user_id' => $user->id,
                    'type' => 'chat_masuk',
                    'url' => 'chat?user=komunitas_' . $ak->id_kelas,
                    'is_read' => 0
                ])->count_all_results('dashboard_notifications');

                $kontak[] = (object)[
                    'id_user' => 'komunitas_' . $ak->id_kelas,
                    'nama' => 'Komunitas Kelas ' . $ak->nama_kelas,
                    'role' => 'komunitas',
                    'kelas' => null,
                    'unread' => $unread_comm
                ];
            }

            // Can chat with all teachers
            $this->db->select('id_user, nama_guru as nama');
            $this->db->from('master_guru');
            $this->db->where('id_user IS NOT NULL');
            $gurus = $this->db->get()->result();
            foreach ($gurus as $g) {
                $kontak[] = (object)[
                    'id_user' => $g->id_user,
                    'nama' => $g->nama,
                    'role' => 'guru',
                    'kelas' => null,
                    'unread' => isset($unread_counts[$g->id_user]) ? $unread_counts[$g->id_user] : 0
                ];
            }

            // Can chat with all students
            $this->db->select('u.id as id_user, s.nama, mk.nama_kelas');
            $this->db->from('master_siswa s');
            $this->db->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa AND ks.id_tp = ' . $tp->id_tp . ' AND ks.id_smt = ' . $smt->id_smt, 'left');
            $this->db->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'left');
            $this->db->join('users u', 's.username = u.username', 'inner');
            $students = $this->db->get()->result();
            foreach ($students as $st) {
                $kontak[] = (object)[
                    'id_user' => $st->id_user,
                    'nama' => $st->nama,
                    'role' => 'siswa',
                    'kelas' => $st->nama_kelas,
                    'unread' => isset($unread_counts[$st->id_user]) ? $unread_counts[$st->id_user] : 0
                ];
            }
        }

        // Ensure any requested target user or unread sender is included in $kontak
        $target_user_id = $this->input->get('user', true);
        $existing_user_ids = [];
        foreach ($kontak as $k) {
            $existing_user_ids[] = (string)$k->id_user;
        }

        $users_to_add = [];
        if ($target_user_id && !in_array((string)$target_user_id, $existing_user_ids)) {
            $users_to_add[] = $target_user_id;
        }
        foreach ($unread_counts as $uid => $cnt) {
            if (!in_array((string)$uid, $existing_user_ids) && !in_array($uid, $users_to_add)) {
                $users_to_add[] = $uid;
            }
        }

        foreach ($users_to_add as $uid) {
            $u = $this->db->where('id', $uid)->get('users')->row();
            if ($u) {
                $st = $this->db->select('s.nama, mk.nama_kelas')
                    ->from('master_siswa s')
                    ->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa AND ks.id_tp = ' . $tp->id_tp . ' AND ks.id_smt = ' . $smt->id_smt, 'left')
                    ->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'left')
                    ->where('s.username', $u->username)
                    ->get()->row();
                if ($st) {
                    $kontak[] = (object)[
                        'id_user' => $u->id,
                        'nama'    => $st->nama,
                        'role'    => 'siswa',
                        'kelas'   => $st->nama_kelas,
                        'unread'  => isset($unread_counts[$u->id]) ? $unread_counts[$u->id] : 0
                    ];
                } else {
                    $gr = $this->db->where('id_user', $u->id)->get('master_guru')->row();
                    if ($gr) {
                        $kontak[] = (object)[
                            'id_user' => $u->id,
                            'nama'    => $gr->nama_guru,
                            'role'    => 'guru',
                            'kelas'   => null,
                            'unread'  => isset($unread_counts[$u->id]) ? $unread_counts[$u->id] : 0
                        ];
                    } else {
                        $kontak[] = (object)[
                            'id_user' => $u->id,
                            'nama'    => trim($u->first_name . ' ' . $u->last_name) ?: $u->username,
                            'role'    => 'admin',
                            'kelas'   => null,
                            'unread'  => isset($unread_counts[$u->id]) ? $unread_counts[$u->id] : 0
                        ];
                    }
                }
            }
        }

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Chat Internal',
            'subjudul' => 'Hubungi Siswa, Guru, atau Admin',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'kontak' => $kontak,
            'kelas_id' => $kelas_id,
            'nama_kelas' => $nama_kelas
        ];

        // Load Views
        if ($this->ion_auth->in_group('siswa')) {
            $this->load->view('members/siswa/templates/header', $data);
            $this->load->view('chat/chat_view', $data);
            $this->load->view('members/siswa/templates/footer');
        } elseif ($this->ion_auth->in_group('guru')) {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('chat/chat_guru', $data);
            $this->load->view('members/guru/templates/footer');
        } else {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('chat/chat_guru', $data);
            $this->load->view('_templates/dashboard/_footer');
        }
    }

    /**
     * Kueri untuk mencari daftar guru yang mengampu di kelas siswa yang sedang login (AJAX)
     */
    public function get_kontak_guru() {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed', 403);
        }

        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $gurus = $this->_get_kontak_guru($user->username, $tp->id_tp, $smt->id_smt);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($gurus));
    }

    private function _get_kontak_guru($username, $tp_id, $smt_id) {
        $siswa = $this->db->where(['username' => $username])->get('master_siswa')->row();
        if (!$siswa) {
            return [];
        }

        $kelas_siswa = $this->db->where([
            'id_siswa' => $siswa->id_siswa,
            'id_tp' => $tp_id,
            'id_smt' => $smt_id
        ])->get('kelas_siswa')->row();

        // Fallback: If not found in current TP/SMT, try latest class of student
        if (!$kelas_siswa) {
            $kelas_siswa = $this->db->where(['id_siswa' => $siswa->id_siswa])
                ->order_by('id_tp', 'DESC')
                ->order_by('id_smt', 'DESC')
                ->get('kelas_siswa')->row();
        }

        if (!$kelas_siswa) {
            return [];
        }

        $student_class_id = intval($kelas_siswa->id_kelas);

        // 1. Get all teachers, auto-linking id_user from users table if master_guru.id_user is NULL/0
        $this->db->select('g.id_guru, g.nama_guru, g.no_hp, g.foto, COALESCE(NULLIF(g.id_user, 0), u.id) as id_user');
        $this->db->from('master_guru g');
        $this->db->join('users u', 'g.username = u.username OR g.nip = u.username', 'left');
        $all_gurus = $this->db->get()->result();

        $guru_allowed_ids = [];

        // 2. Add Wali Kelas from master_kelas if configured
        $mk = $this->db->where(['id_kelas' => $student_class_id])->get('master_kelas')->row();
        if ($mk && !empty($mk->guru_id) && intval($mk->guru_id) > 0) {
            $guru_allowed_ids[] = intval($mk->guru_id);
        }

        // Helper closure for unserialize/json_decode fallback
        $decode_data = function($raw) {
            if (empty($raw)) return [];
            $res = @unserialize($raw);
            if ($res === false || !is_array($res)) {
                $res = @json_decode($raw, true);
            }
            return is_array($res) ? $res : [];
        };

        // 3. Fetch jabatan_guru mapping for current TP/SMT
        $this->db->select('id_guru, mapel_kelas, id_kelas');
        $this->db->from('jabatan_guru');
        $this->db->group_start();
        $this->db->where('id_tp', $tp_id);
        $this->db->where('id_smt', $smt_id);
        $this->db->or_where('id_tp IS NULL');
        $this->db->or_where('id_tp', 0);
        $this->db->group_end();
        $jabatans = $this->db->get()->result();

        foreach ($jabatans as $jb) {
            if (intval($jb->id_kelas) === $student_class_id) {
                $guru_allowed_ids[] = intval($jb->id_guru);
            }
            if (!empty($jb->mapel_kelas)) {
                $mapels = $decode_data($jb->mapel_kelas);
                foreach ($mapels as $mapel) {
                    $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                    if (isset($mapel_arr['kelas']) && intval($mapel_arr['kelas']) === $student_class_id) {
                        $guru_allowed_ids[] = intval($jb->id_guru);
                    }
                    $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                    if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                        foreach ($kelas_mapel as $km) {
                            $km_arr = is_object($km) ? (array)$km : $km;
                            $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : (isset($km_arr['id_kelas']) ? $km_arr['id_kelas'] : $km_arr);
                            if (intval($kls_id) === $student_class_id) {
                                $guru_allowed_ids[] = intval($jb->id_guru);
                            }
                        }
                    }
                }
            }
        }

        // 4. Fetch kelas_materi mapping
        $this->db->select('id_guru, materi_kelas');
        $this->db->from('kelas_materi');
        $this->db->where('id_tp', $tp_id);
        $this->db->where('id_smt', $smt_id);
        $materis = $this->db->get()->result();

        foreach ($materis as $m) {
            if (!empty($m->materi_kelas)) {
                $kls = $decode_data($m->materi_kelas);
                foreach ($kls as $k) {
                    $k_id = is_array($k) ? (isset($k['kelas']) ? $k['kelas'] : (isset($k['id_kelas']) ? $k['id_kelas'] : null)) : $k;
                    if (intval($k_id) === $student_class_id) {
                        $guru_allowed_ids[] = intval($m->id_guru);
                        break;
                    }
                }
            }
        }

        // 5. Fallback: If no guru found yet, check all jabatan_guru records regardless of TP/SMT
        if (empty($guru_allowed_ids)) {
            $all_jabatans = $this->db->select('id_guru, mapel_kelas, id_kelas')->get('jabatan_guru')->result();
            foreach ($all_jabatans as $jb) {
                if (intval($jb->id_kelas) === $student_class_id) {
                    $guru_allowed_ids[] = intval($jb->id_guru);
                }
                if (!empty($jb->mapel_kelas)) {
                    $mapels = $decode_data($jb->mapel_kelas);
                    foreach ($mapels as $mapel) {
                        $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                        if (isset($mapel_arr['kelas']) && intval($mapel_arr['kelas']) === $student_class_id) {
                            $guru_allowed_ids[] = intval($jb->id_guru);
                        }
                        $kelas_mapel = isset($mapel_arr['kelas_mapel']) ? $mapel_arr['kelas_mapel'] : null;
                        if (is_array($kelas_mapel) || is_object($kelas_mapel)) {
                            foreach ($kelas_mapel as $km) {
                                $km_arr = is_object($km) ? (array)$km : $km;
                                $kls_id = isset($km_arr['kelas']) ? $km_arr['kelas'] : (isset($km_arr['id_kelas']) ? $km_arr['id_kelas'] : $km_arr);
                                if (intval($kls_id) === $student_class_id) {
                                    $guru_allowed_ids[] = intval($jb->id_guru);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Make list of allowed guru IDs unique and filter non-zero values
        $guru_allowed_ids = array_filter(array_unique($guru_allowed_ids));

        // Filter and return allowed teachers sorted by name, ensuring id_user is valid
        $teachers = [];
        foreach ($all_gurus as $g) {
            if (in_array(intval($g->id_guru), $guru_allowed_ids) && !empty($g->id_user) && intval($g->id_user) > 0) {
                $teachers[] = $g;
            }
        }

        // Sort by name alphabetically
        usort($teachers, function($a, $b) {
            return strcmp($a->nama_guru, $b->nama_guru);
        });

        return $teachers;
    }

    /**
     * Menarik riwayat pesan antara user yang login dan lawan bicaranya.
     * Logika fallback WA (3 hari unread) disematkan di sini.
     */
    public function get_pesan() {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed', 403);
        }

        $user = $this->ion_auth->user()->row();
        $lawan_bicara_id = $this->input->post('lawan_bicara_id', true);
        $is_komunitas = $this->input->post('is_komunitas', true) == '1';
        $id_kelas_komunitas = $this->input->post('id_kelas_komunitas', true);

        // Sanitize
        $lawan_bicara_id = ($lawan_bicara_id === '' || $lawan_bicara_id === 'null') ? null : intval($lawan_bicara_id);
        $id_kelas_komunitas = ($id_kelas_komunitas === '' || $id_kelas_komunitas === 'null') ? null : intval($id_kelas_komunitas);

        // Fetch messages from model
        $messages = $this->chat_model->get_riwayat_obrolan($user->id, $lawan_bicara_id, $is_komunitas, $id_kelas_komunitas);

        // Mark as read if private chat and we have a recipient
        if (!$is_komunitas && $lawan_bicara_id) {
            $this->chat_model->tandai_dibaca($user->id, $lawan_bicara_id);
        }

        // Mark as read for community chat
        if ($is_komunitas && $id_kelas_komunitas) {
            $this->db->where('user_id', $user->id)
                     ->where('type', 'chat_masuk')
                     ->where('url', 'chat?user=komunitas_' . $id_kelas_komunitas)
                     ->update('dashboard_notifications', ['is_read' => 1]);
        }

        // Logika Fallback WhatsApp (3 Hari)
        $tampilkan_tombol_wa = false;
        $wa_number = null;

        // Fallback only triggers if the logged-in user is a student, and opponent is a teacher
        if ($this->ion_auth->in_group('siswa') && !$is_komunitas && $lawan_bicara_id) {
            $opponent_user = $this->db->where(['id' => $lawan_bicara_id])->get('users')->row();
            if ($opponent_user && $this->ion_auth->in_group('guru', $lawan_bicara_id)) {
                $teacher = $this->db->where(['id_user' => $lawan_bicara_id])->get('master_guru')->row();
                if ($teacher) {
                    $wa_number = $teacher->no_hp;

                    // Filter messages sent by the logged-in student to this teacher
                    $student_msgs = array_filter($messages, function($m) use ($user) {
                        return $m->pengirim_id == $user->id;
                    });

                    if (!empty($student_msgs)) {
                        $last_student_msg = end($student_msgs);
                        if ($last_student_msg->is_read == 0) {
                            $time_limit = strtotime('-3 days');
                            if (strtotime($last_student_msg->created_at) < $time_limit) {
                                $tampilkan_tombol_wa = true;
                            }
                        }
                    }
                }
            }
        }

        // Fetch updated unread counts for active user
        $unread_query = $this->db->select('pengirim_id, COUNT(*) as count')
            ->from('chat_messages')
            ->where('penerima_id', $user->id)
            ->where('is_read', 0)
            ->group_by('pengirim_id')
            ->get()->result();
        $unread_list = [];
        foreach ($unread_query as $ur) {
            $unread_list[$ur->pengirim_id] = intval($ur->count);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'messages' => $messages,
                'tampilkan_tombol_wa' => $tampilkan_tombol_wa,
                'wa_number' => $wa_number,
                'unread_counts' => $unread_list
            ]));
    }

    /**
     * Mengirim pesan baru via AJAX
     */
    public function kirim_pesan() {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed', 403);
        }

        $this->form_validation->set_rules('pesan', 'Pesan', 'required|trim');
        $this->form_validation->set_rules('pengirim_role', 'Role Pengirim', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
        }

        $user = $this->ion_auth->user()->row();
        $penerima_id = $this->input->post('penerima_id', true);
        $penerima_role = $this->input->post('penerima_role', true);
        $id_kelas_komunitas = $this->input->post('id_kelas_komunitas', true);

        // Sanitize IDs
        $penerima_id = ($penerima_id === '' || $penerima_id === '0') ? null : intval($penerima_id);
        $id_kelas_komunitas = ($id_kelas_komunitas === '' || $id_kelas_komunitas === '0') ? null : intval($id_kelas_komunitas);

        $data = [
            'pengirim_id' => $user->id,
            'pengirim_role' => $this->input->post('pengirim_role', true),
            'penerima_id' => $penerima_id,
            'penerima_role' => empty($penerima_role) ? null : $penerima_role,
            'id_kelas_komunitas' => $id_kelas_komunitas,
            'pesan' => $this->input->post('pesan', true),
            'is_read' => 0
        ];

        if ($this->db->insert('chat_messages', $data)) {
            $data['id_pesan'] = $this->db->insert_id();
            $data['created_at'] = date('Y-m-d H:i:s');

            // Send dashboard notification if it's a private chat
            if ($penerima_id) {
                // Clear any incoming notifications and messages for the sender from recipient
                $this->chat_model->tandai_dibaca($user->id, $penerima_id);
                try {
                    $this->load->model('Notifikasi_model', 'notif_m');
                    $sender_name = 'Seseorang';
                    if ($data['pengirim_role'] == 'siswa') {
                        $sender = $this->db->where(['username' => $user->username])->get('master_siswa')->row();
                        if ($sender) $sender_name = $sender->nama;
                    } elseif ($data['pengirim_role'] == 'guru') {
                        $sender = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
                        if ($sender) $sender_name = $sender->nama_guru;
                    } else {
                        $sender_name = trim($user->first_name . ' ' . $user->last_name) ?: $user->username;
                    }
    
                    $notif_role = $penerima_role;
                    if ($notif_role == 'tutor') $notif_role = 'guru';
                    if (!in_array($notif_role, ['guru', 'siswa', 'admin'])) {
                        if ($this->ion_auth->in_group('guru', $penerima_id)) {
                            $notif_role = 'guru';
                        } elseif ($this->ion_auth->in_group('siswa', $penerima_id)) {
                            $notif_role = 'siswa';
                        } else {
                            $notif_role = 'admin';
                        }
                    }
    
                    $this->notif_m->createNotifikasi([
                        'user_id'  => $penerima_id,
                        'role'     => $notif_role,
                        'type'     => 'chat_masuk',
                        'title'    => 'Chat dari ' . $sender_name,
                        'body'     => substr($data['pesan'], 0, 100),
                        'url'      => 'chat?user=' . $user->id,
                        'metadata' => ['pengirim_id' => $user->id]
                    ]);
                } catch (Throwable $e) {
                    log_message('error', 'Gagal mengirim notifikasi chat: ' . $e->getMessage());
                }
            }

            // Send dashboard notification if it's a community chat
            if ($id_kelas_komunitas) {
                try {
                    $this->load->model('Notifikasi_model', 'notif_m');
                    $this->load->model('Dashboard_model', 'dashboard');
                    $tp = $this->dashboard->getTahunActive();
                    $smt = $this->dashboard->getSemesterActive();

                    $sender_name = 'Seseorang';
                    if ($data['pengirim_role'] == 'siswa') {
                        $sender = $this->db->where(['username' => $user->username])->get('master_siswa')->row();
                        if ($sender) $sender_name = $sender->nama;
                    } elseif ($data['pengirim_role'] == 'guru') {
                        $sender = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
                        if ($sender) $sender_name = $sender->nama_guru;
                    } else {
                        $sender_name = trim($user->first_name . ' ' . $user->last_name) ?: $user->username;
                    }

                    $mk = $this->db->where('id_kelas', $id_kelas_komunitas)->get('master_kelas')->row();
                    $class_name = $mk ? $mk->nama_kelas : 'Kelas';

                    // Get all students in the class
                    $this->db->select('u.id as id_user');
                    $this->db->from('master_siswa s');
                    $this->db->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa AND ks.id_tp = ' . $tp->id_tp . ' AND ks.id_smt = ' . $smt->id_smt, 'inner');
                    $this->db->join('users u', 's.username = u.username', 'inner');
                    $this->db->where('ks.id_kelas', $id_kelas_komunitas);
                    $students = $this->db->get()->result();

                    foreach ($students as $student) {
                        if ($student->id_user == $user->id) continue;
                        $this->notif_m->createNotifikasi([
                            'user_id'  => $student->id_user,
                            'role'     => 'siswa',
                            'type'     => 'chat_masuk',
                            'title'    => 'Komunitas ' . $class_name . ': ' . $sender_name,
                            'body'     => substr($data['pesan'], 0, 100),
                            'url'      => 'chat?user=komunitas_' . $id_kelas_komunitas,
                            'metadata' => ['komunitas_id' => $id_kelas_komunitas, 'pengirim_id' => 'komunitas_' . $id_kelas_komunitas]
                        ]);
                    }

                    // Get homeroom & subject teachers of the class to notify them as well
                    $teacher_user_ids = [];
                    if ($mk && $mk->guru_id) {
                        $wali = $this->db->where('id_guru', $mk->guru_id)->get('master_guru')->row();
                        if ($wali && $wali->id_user) {
                            $teacher_user_ids[] = $wali->id_user;
                        }
                    }
                    $jabs = $this->db->where('id_kelas', $id_kelas_komunitas)->get('jabatan_guru')->result();
                    foreach ($jabs as $j) {
                        $t_guru = $this->db->where('id_guru', $j->id_guru)->get('master_guru')->row();
                        if ($t_guru && $t_guru->id_user) {
                            $teacher_user_ids[] = $t_guru->id_user;
                        }
                    }
                    $teacher_user_ids = array_filter(array_unique($teacher_user_ids));

                    foreach ($teacher_user_ids as $t_uid) {
                        if ($t_uid == $user->id) continue;
                        $this->notif_m->createNotifikasi([
                            'user_id'  => $t_uid,
                            'role'     => 'guru',
                            'type'     => 'chat_masuk',
                            'title'    => 'Komunitas ' . $class_name . ': ' . $sender_name,
                            'body'     => substr($data['pesan'], 0, 100),
                            'url'      => 'chat?user=komunitas_' . $id_kelas_komunitas,
                            'metadata' => ['komunitas_id' => $id_kelas_komunitas, 'pengirim_id' => 'komunitas_' . $id_kelas_komunitas]
                        ]);
                    }
                } catch (Throwable $e) {
                    log_message('error', 'Gagal mengirim notifikasi chat komunitas: ' . $e->getMessage());
                }
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $data]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesan.']));
        }
    }

    /**
     * Mengubah status is_read = 1 ketika obrolan dibuka oleh penerima
     */
    public function tandai_dibaca($pengirim_id) {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed', 403);
        }

        $user = $this->ion_auth->user()->row();
        $pengirim_id = intval($pengirim_id);

        $this->db->where('pengirim_id', $pengirim_id);
        $this->db->where('penerima_id', $user->id);
        $this->db->where('is_read', 0);
        $update = $this->db->update('chat_messages', ['is_read' => 1]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => $update ? 'success' : 'error']));
    }
}
