<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifikasi_model
 *
 * Model khusus untuk sistem dashboard notifikasi aktifitas Guru & Siswa.
 * TIDAK menggunakan SQL Trigger - semua notifikasi dibuat via PHP dari Controller.
 *
 * Cara pakai di Controller:
 *   $this->load->model('Notifikasi_model', 'notif');
 *   $this->notif->createNotifikasi([...]);
 */
class Notifikasi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // =========================================================
    // WRITE — Membuat & mengelola notifikasi
    // =========================================================

    /**
     * Buat satu notifikasi baru.
     *
     * @param array $data Kolom: user_id, role, type, title, body (opsional), url (opsional), metadata (opsional - array/JSON)
     * @return bool
     */
    public function createNotifikasi($data) {
        $meta_str = isset($data['metadata']) ? (is_array($data['metadata']) ? json_encode($data['metadata']) : $data['metadata']) : null;
        
        // Prevent duplicate unread notification for the same user, type & assignment (id_materi)
        if ($data['type'] === 'nilai_keluar' || ($meta_str && strpos($meta_str, 'id_materi') !== false)) {
            $meta_arr = is_array($data['metadata'] ?? null) ? $data['metadata'] : @json_decode($meta_str ?? '', true);
            $id_materi = isset($meta_arr['id_materi']) ? $meta_arr['id_materi'] : null;

            if ($id_materi) {
                $unreads = $this->db->where('user_id', $data['user_id'])
                    ->where('type', $data['type'])
                    ->where('is_read', 0)
                    ->get('dashboard_notifications')->result();

                $existing = null;
                foreach ($unreads as $u) {
                    $u_meta = is_array($u->metadata) ? $u->metadata : @json_decode($u->metadata ?? '', true);
                    if (isset($u_meta['id_materi']) && (string)$u_meta['id_materi'] === (string)$id_materi) {
                        $existing = $u;
                        break;
                    }
                }

                if ($existing) {
                    $pk_col = $this->db->field_exists('id_notif', 'dashboard_notifications') ? 'id_notif' : 'id';
                    $update_data = [
                        'title'      => $data['title'],
                        'body'       => isset($data['body']) ? $data['body'] : null,
                        'url'        => isset($data['url']) ? $data['url'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'metadata'   => $meta_str
                    ];
                    $this->db->where($pk_col, $existing->$pk_col)->update('dashboard_notifications', $update_data);
                    return true;
                }
            }
        }

        $insert = [
            'user_id'    => $data['user_id'],
            'role'       => $data['role'],
            'type'       => $data['type'],
            'title'      => $data['title'],
            'body'       => isset($data['body']) ? $data['body'] : null,
            'url'        => isset($data['url']) ? $data['url'] : null,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'metadata'   => $meta_str,
        ];
        return $this->db->insert('dashboard_notifications', $insert);
    }

    /**
     * Buat notifikasi ke banyak user sekaligus (misal: satu guru upload materi → semua siswa sekelas dapat notif)
     *
     * @param array $user_ids Array of user_id
     * @param array $template Array notif template (role, type, title, body, url, metadata)
     */
    public function createNotifikasiBatch($user_ids, $template) {
        if (empty($user_ids)) return false;
        $rows = [];
        $now = date('Y-m-d H:i:s');
        $meta = isset($template['metadata']) ? (is_array($template['metadata']) ? json_encode($template['metadata']) : $template['metadata']) : null;
        foreach ($user_ids as $uid) {
            $rows[] = [
                'user_id'    => $uid,
                'role'       => $template['role'],
                'type'       => $template['type'],
                'title'      => $template['title'],
                'body'       => isset($template['body']) ? $template['body'] : null,
                'url'        => isset($template['url']) ? $template['url'] : null,
                'is_read'    => 0,
                'created_at' => $now,
                'metadata'   => $meta,
            ];
        }
        return $this->db->insert_batch('dashboard_notifications', $rows);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     *
     * @param int $id ID notifikasi
     * @param int $user_id Validasi kepemilikan
     */
    public function markAsRead($id, $user_id) {
        $pk_col = $this->db->field_exists('id_notif', 'dashboard_notifications') ? 'id_notif' : 'id';
        $notif = $this->db->where($pk_col, $id)->where('user_id', $user_id)->get('dashboard_notifications')->row();

        $this->db->where($pk_col, $id)->where('user_id', $user_id);
        $res = $this->db->update('dashboard_notifications', ['is_read' => 1]);

        if ($notif && ($notif->type === 'nilai_keluar' || !empty($notif->metadata))) {
            $meta = is_array($notif->metadata) ? $notif->metadata : @json_decode($notif->metadata ?? '', true);
            $id_materi = isset($meta['id_materi']) ? $meta['id_materi'] : null;
            if ($id_materi) {
                $unreads = $this->db->where('user_id', $user_id)
                    ->where('type', 'nilai_keluar')
                    ->where('is_read', 0)
                    ->get('dashboard_notifications')->result();

                foreach ($unreads as $u) {
                    $u_meta = is_array($u->metadata) ? $u->metadata : @json_decode($u->metadata ?? '', true);
                    if (isset($u_meta['id_materi']) && (string)$u_meta['id_materi'] === (string)$id_materi) {
                        $this->db->where($pk_col, $u->$pk_col)->update('dashboard_notifications', ['is_read' => 1]);
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Clear all unread grade notifications for user
     */
    public function clearNilaiNotifications($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'nilai_keluar');
        $this->db->where('is_read', 0);
        return $this->db->update('dashboard_notifications', ['is_read' => 1]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     *
     * @param int $user_id
     */
    public function markAllAsRead($user_id) {
        $this->db->where('user_id', $user_id)->where('is_read', 0);
        return $this->db->update('dashboard_notifications', ['is_read' => 1]);
    }

    /**
     * Hapus notifikasi lebih dari N hari (untuk kebersihan data)
     */
    public function cleanOld($days = 30) {
        $this->db->where('created_at <', date('Y-m-d', strtotime("-$days days")));
        return $this->db->delete('dashboard_notifications');
    }

    // =========================================================
    // READ — Mengambil notifikasi dari storage
    // =========================================================

    /**
     * Ambil notifikasi tersimpan untuk user tertentu
     *
     * @param int $user_id
     * @param int $limit
     * @param bool $unread_only
     */
    public function getNotifikasiByUser($user_id, $limit = 20, $unread_only = false) {
        $this->db->where('user_id', $user_id);
        if ($unread_only) $this->db->where('is_read', 0);
        $this->db->order_by('created_at', 'DESC')->limit($limit);
        return $this->db->get('dashboard_notifications')->result();
    }

    /**
     * Hitung jumlah notifikasi belum dibaca untuk badge
     */
    public function countUnread($user_id) {
        return $this->db->where('user_id', $user_id)->where('is_read', 0)->count_all_results('dashboard_notifications');
    }

    // =========================================================
    // LIVE QUERY — Query langsung ke tabel sumber (safety net)
    // Digunakan sebagai fallback bila stored notif belum ada
    // =========================================================

    /**
     * Live query: tugas siswa yang BELUM dinilai oleh guru
     * Mengembalikan jumlah & list tugas menunggu penilaian
     *
     * @param int $id_guru
     * @param int $id_tp
     * @param int $id_smt
     */
    public function getLiveTugasBelumDinilai($id_guru, $id_tp, $id_smt) {
        // kelas_materi.jenis=2 adalah tugas, status=1 artinya sudah dikumpulkan tapi belum ada nilai
        // Cek dari log_materi (siswa submit tugas) yang belum ada di rapor_nilai_harian
        $this->db->select('km.id_materi, km.judul_materi, km.deadline, COUNT(DISTINCT lm.id_siswa) as jml_siswa');
        $this->db->from('kelas_materi km');
        $this->db->join('log_materi lm', 'lm.id_materi = km.id_materi AND (lm.finish_time IS NOT NULL OR lm.log_desc LIKE "%mengumpulkan%")', 'left');
        $this->db->where('km.id_guru', $id_guru);
        $this->db->where('km.id_tp', $id_tp);
        $this->db->where('km.id_smt', $id_smt);
        $this->db->where('km.jenis', 2); // 2 = tugas
        $this->db->where('km.status', 1);
        $this->db->group_by('km.id_materi');
        $this->db->having('jml_siswa > 0');
        $this->db->order_by('km.deadline', 'ASC');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    /**
     * Live query: chat yang belum dibalas guru (masuk tapi is_read=0)
     *
     * @param int $user_id User ID guru
     */
    public function getLiveChatBelumDibaca($user_id, $limit = 5) {
        $this->db->select('
            cm.id_pesan, cm.pesan, cm.created_at, cm.pengirim_id, cm.pengirim_role,
            CASE
                WHEN cm.pengirim_role = "siswa" THEN ms.nama
                WHEN cm.pengirim_role = "guru"  THEN mg.nama_guru
                ELSE "Admin"
            END as nama_pengirim
        ', FALSE);
        $this->db->from('chat_messages cm');
        $this->db->join('master_siswa ms', 'ms.username = (SELECT username FROM users WHERE id = cm.pengirim_id LIMIT 1)', 'left');
        $this->db->join('master_guru mg', 'mg.id_user = cm.pengirim_id', 'left');
        $this->db->where('cm.penerima_id', $user_id);
        $this->db->where('cm.is_read', 0);
        $this->db->order_by('cm.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Live query: honor guru yang pending konfirmasi
     *
     * @param int $tutor_id ID guru
     */
    public function getLiveHonorPending($tutor_id) {
        $this->db->select('SUM(hm.amount) as total, COUNT(*) as jml, hm.status_konfirmasi_tutor');
        $this->db->from('honor_mutations hm');
        $this->db->where('hm.tutor_id', $tutor_id);
        $this->db->where('hm.status_konfirmasi_tutor', 0);
        $this->db->where('hm.type', 'credit');
        return $this->db->get()->row();
    }

    /**
     * Live query: tugas siswa yang belum dikerjakan & sudah/hampir deadline
     * Untuk dashboard siswa
     *
     * @param int $id_siswa
     * @param int $id_kelas
     * @param int $id_tp
     * @param int $id_smt
     */
    public function getLiveTugasSiswa($id_siswa, $id_kelas, $id_tp, $id_smt) {
        $now = date('Y-m-d H:i:s');
        $this->db->select('km.id_materi, km.judul_materi, km.deadline, km.id_guru, km.tgl_mulai,
            mg.nama_guru,
            DATEDIFF(km.deadline, NOW()) as sisa_hari,
            (SELECT COUNT(*) FROM log_materi lm WHERE lm.id_materi=km.id_materi AND lm.id_siswa='. (int)$id_siswa .' AND lm.finish_time IS NOT NULL) as sudah_kumpul
        ');
        $this->db->from('kelas_materi km');
        $this->db->join('master_guru mg', 'mg.id_guru = km.id_guru', 'left');
        $this->db->where('km.id_tp', $id_tp);
        $this->db->where('km.id_smt', $id_smt);
        $this->db->where('km.jenis', 2); // tugas
        $this->db->where('km.status', 1);
        $this->db->where('km.tgl_mulai <=', $now);
        $this->db->where('km.created_on >=', date('Y-m-d H:i:s', strtotime('-14 days')));
        $this->db->where('km.deadline >=', date('Y-m-d H:i:s', strtotime('-1 days')));
        // Materi untuk kelas siswa (serialized di DB, jadi pakai LIKE)
        $this->db->like('km.materi_kelas', '"' . $id_kelas . '"');
        $this->db->having('sudah_kumpul', 0);
        $this->db->order_by('km.deadline', 'ASC');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    /**
     * Live query: materi baru untuk siswa (belum dilihat)
     *
     * @param int $id_kelas
     * @param int $id_tp
     * @param int $id_smt
     * @param int $id_siswa
     * @param int $hari_ke_belakang Ambil materi yang dibuat dalam N hari terakhir
     */
    public function getLiveMateriBaruSiswa($id_kelas, $id_tp, $id_smt, $id_siswa, $hari_ke_belakang = 7) {
        $since = date('Y-m-d H:i:s', strtotime("-$hari_ke_belakang days"));
        $now   = date('Y-m-d H:i:s');
        $this->db->select('km.id_materi, km.judul_materi, km.created_on, km.tgl_mulai, km.jenis,
            mg.nama_guru,
            (SELECT COUNT(*) FROM log_materi lm WHERE lm.id_materi=km.id_materi AND lm.id_siswa='. (int)$id_siswa .') as sudah_buka
        ');
        $this->db->from('kelas_materi km');
        $this->db->join('master_guru mg', 'mg.id_guru = km.id_guru', 'left');
        $this->db->where('km.id_tp', $id_tp);
        $this->db->where('km.id_smt', $id_smt);
        $this->db->where('km.jenis', 1); // materi
        $this->db->where('km.status', 1);
        $this->db->where('km.tgl_mulai <=', $now);
        $this->db->where('km.created_on >=', $since);
        $this->db->like('km.materi_kelas', '"' . $id_kelas . '"');
        $this->db->having('sudah_buka', 0);
        $this->db->order_by('km.created_on', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    /**
     * Live query: chat belum dibaca untuk siswa
     *
     * @param int $user_id
     * @param int $limit
     */
    public function getLiveChatSiswa($user_id, $limit = 5) {
        $this->db->select('
            cm.id_pesan, cm.pesan, cm.created_at, cm.pengirim_id, cm.pengirim_role,
            CASE
                WHEN cm.pengirim_role = "guru" THEN mg.nama_guru
                ELSE ms.nama
            END as nama_pengirim
        ', FALSE);
        $this->db->from('chat_messages cm');
        $this->db->join('master_guru mg', 'mg.id_user = cm.pengirim_id', 'left');
        $this->db->join('master_siswa ms', 'ms.username = (SELECT username FROM users WHERE id = cm.pengirim_id LIMIT 1)', 'left');
        $this->db->where('cm.penerima_id', $user_id);
        $this->db->where('cm.is_read', 0);
        $this->db->order_by('cm.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Bangun data activity feed untuk GURU
     * Menggabungkan stored notifications + live query
     *
     * @param int $user_id
     * @param int $id_guru
     * @param int $id_tp
     * @param int $id_smt
     */
    public function buildFeedGuru($user_id, $id_guru, $id_tp, $id_smt) {
        $items = [];

        // 1. Honor pending (live query)
        $honor = $this->getLiveHonorPending($id_guru);
        if ($honor && $honor->jml > 0) {
            $items[] = [
                'type'     => 'honor_pending',
                'icon'     => '💰',
                'priority' => 1,
                'color'    => 'warning',
                'title'    => 'Honor siap dicairkan Rp ' . number_format($honor->total, 0, ',', '.'),
                'body'     => 'Klik untuk konfirmasi pencairan honor Anda',
                'url'      => 'honor',
                'age'      => null,
                'is_read'  => 0,
            ];
        }

        // 2. Chat belum dibaca (live query)
        $chats = $this->getLiveChatBelumDibaca($user_id, 3);
        if (!empty($chats)) {
            foreach ($chats as $chat) {
                $nama = $chat->nama_pengirim ?: 'Siswa';
                $items[] = [
                    'type'     => 'chat_masuk',
                    'icon'     => '💬',
                    'priority' => 2,
                    'color'    => 'info',
                    'title'    => 'Chat dari ' . $nama,
                    'body'     => substr($chat->pesan, 0, 80) . (strlen($chat->pesan) > 80 ? '...' : ''),
                    'url'      => 'chat?user=' . $chat->pengirim_id,
                    'age'      => $chat->created_at,
                    'is_read'  => 0,
                ];
            }
            // Jika lebih dari 1 chat, tambahkan summary
            if (count($chats) >= 3) {
                $items[] = [
                    'type'     => 'chat_masuk',
                    'icon'     => '💬',
                    'priority' => 2,
                    'color'    => 'info',
                    'title'    => count($chats) . ' chat belum dibalas',
                    'body'     => 'Ada pesan yang menunggu balasan Anda',
                    'url'      => 'chat',
                    'age'      => null,
                    'is_read'  => 0,
                ];
            }
        }

        // 3. Tugas belum dinilai (live query)
        $tugas = $this->getLiveTugasBelumDinilai($id_guru, $id_tp, $id_smt);
        if (!empty($tugas)) {
            $total_tugas = count($tugas);
            $total_siswa = array_sum(array_column((array)$tugas, 'jml_siswa'));
            if ($total_siswa > 0) {
                $items[] = [
                    'type'     => 'tugas_belum_nilai',
                    'icon'     => '📝',
                    'priority' => 3,
                    'color'    => 'danger',
                    'title'    => $total_siswa . ' tugas siswa belum dinilai',
                    'body'     => 'Dari ' . $total_tugas . ' tugas berbeda. Klik untuk memeriksa.',
                    'url'      => 'kelasmateri/tugas',
                    'age'      => null,
                    'is_read'  => 0,
                ];
            }
        }

        // 4. Stored notifications (PHP-driven, dari createNotifikasi() di Controller lain)
        $stored = $this->getNotifikasiByUser($user_id, 10, true);
        foreach ($stored as $notif) {
            $url = $notif->url;
            if ($notif->type === 'chat_masuk') {
                $meta = is_array($notif->metadata) ? $notif->metadata : @json_decode($notif->metadata ?? '', true);
                $pengirim_id = isset($meta['pengirim_id']) ? $meta['pengirim_id'] : null;
                if ($pengirim_id) {
                    if (strpos((string)$pengirim_id, 'komunitas_') === 0) {
                        $unread_chat = $this->db->where([
                            'user_id' => $user_id,
                            'type'    => 'chat_masuk',
                            'url'     => 'chat?user=' . $pengirim_id,
                            'is_read' => 0
                        ])->count_all_results('dashboard_notifications');
                    } else {
                        $unread_chat = $this->db->where([
                            'penerima_id' => $user_id,
                            'pengirim_id' => $pengirim_id,
                            'is_read'     => 0
                        ])->count_all_results('chat_messages');
                    }

                    if ($unread_chat == 0) {
                        $this->markAsRead(isset($notif->id) ? $notif->id : (isset($notif->id_notif) ? $notif->id_notif : 0), $user_id);
                        continue;
                    }
                    $url = 'chat?user=' . $pengirim_id;
                }
            }
            $items[] = [
                'type'     => $notif->type,
                'icon'     => $this->getIcon($notif->type),
                'priority' => 4,
                'color'    => $this->getColor($notif->type),
                'title'    => $notif->title,
                'body'     => $notif->body,
                'url'      => $url,
                'age'      => $notif->created_at,
                'is_read'  => (int)$notif->is_read,
                'id'       => isset($notif->id_notif) ? $notif->id_notif : (isset($notif->id) ? $notif->id : null),
            ];
        }

        // Sort by priority asc, then date desc
        usort($items, function($a, $b) {
            if ($a['priority'] !== $b['priority']) return $a['priority'] - $b['priority'];
            return strcmp($b['age'] ?? '', $a['age'] ?? '');
        });

        return $items;
    }

    /**
     * Bangun data activity feed untuk SISWA
     *
     * @param int $user_id
     * @param int $id_siswa
     * @param int $id_kelas
     * @param int $id_tp
     * @param int $id_smt
     */
    public function buildFeedSiswa($user_id, $id_siswa, $id_kelas, $id_tp, $id_smt) {
        $items = [];

        // 1. Tugas OVERDUE (merah)
        $tugas = $this->getLiveTugasSiswa($id_siswa, $id_kelas, $id_tp, $id_smt);
        foreach ($tugas as $t) {
            $sisa = (int)$t->sisa_hari;
            if ($sisa < 0) {
                $color = 'danger';
                $icon  = '❌';
                $badge = 'Lewat deadline!';
                $priority = 1;
            } elseif ($sisa === 0) {
                $color = 'danger';
                $icon  = '🔴';
                $badge = 'Deadline HARI INI!';
                $priority = 1;
            } elseif ($sisa <= 2) {
                $color = 'warning';
                $icon  = '🟡';
                $badge = $sisa . ' hari lagi';
                $priority = 2;
            } else {
                $color = 'primary';
                $icon  = '📝';
                $badge = $sisa . ' hari lagi';
                $priority = 3;
            }
            $items[] = [
                'type'     => 'tugas',
                'icon'     => $icon,
                'priority' => $priority,
                'color'    => $color,
                'title'    => 'Tugas: ' . $t->judul_materi,
                'body'     => 'Oleh ' . ($t->nama_guru ?: 'Guru') . ' — ' . $badge,
                'url'      => 'siswa/bukaTugas/' . $t->id_materi . '/0',
                'age'      => $t->deadline,
                'is_read'  => 0,
                'badge'    => $badge,
            ];
        }

        // 2. Materi baru belum dibuka
        $materis = $this->getLiveMateriBaruSiswa($id_kelas, $id_tp, $id_smt, $id_siswa, 7);
        foreach ($materis as $m) {
            $items[] = [
                'type'     => 'materi_baru',
                'icon'     => '📗',
                'priority' => 3,
                'color'    => 'success',
                'title'    => 'Materi baru: ' . $m->judul_materi,
                'body'     => 'Oleh ' . ($m->nama_guru ?: 'Guru') . ' — Belum kamu buka',
                'url'      => 'siswa/bukaMateri/' . $m->id_materi . '/0',
                'age'      => $m->created_on,
                'is_read'  => 0,
            ];
        }

        // 3. Chat belum dibaca
        $chats = $this->getLiveChatSiswa($user_id, 3);
        foreach ($chats as $chat) {
            $items[] = [
                'type'     => 'chat_masuk',
                'icon'     => '💬',
                'priority' => 2,
                'color'    => 'info',
                'title'    => 'Pesan dari ' . ($chat->nama_pengirim ?: 'Seseorang'),
                'body'     => substr($chat->pesan, 0, 80) . (strlen($chat->pesan) > 80 ? '...' : ''),
                'url'      => 'chat?user=' . $chat->pengirim_id,
                'age'      => $chat->created_at,
                'is_read'  => 0,
            ];
        }

        // 4. Stored notifications untuk siswa
        $stored = $this->getNotifikasiByUser($user_id, 15, true);
        $seen_nilai_materi = [];
        foreach ($stored as $notif) {
            if (in_array($notif->type, ['tugas_baru', 'materi_baru'])) continue;
            $url = $notif->url;
            if ($notif->type === 'nilai_keluar') {
                $meta = is_array($notif->metadata) ? $notif->metadata : @json_decode($notif->metadata ?? '', true);
                $id_materi = isset($meta['id_materi']) ? (string)$meta['id_materi'] : null;
                if ($id_materi) {
                    if (isset($seen_nilai_materi[$id_materi])) {
                        $pk_id = isset($notif->id_notif) ? $notif->id_notif : (isset($notif->id) ? $notif->id : 0);
                        if ($pk_id) {
                            $pk_col = $this->db->field_exists('id_notif', 'dashboard_notifications') ? 'id_notif' : 'id';
                            $this->db->where($pk_col, $pk_id)->where('user_id', $user_id)->update('dashboard_notifications', ['is_read' => 1]);
                        }
                        continue;
                    }
                    $seen_nilai_materi[$id_materi] = true;
                }
                $url = 'siswa/hasil';
            }
            if ($notif->type === 'chat_masuk') {
                $meta = is_array($notif->metadata) ? $notif->metadata : @json_decode($notif->metadata ?? '', true);
                $pengirim_id = isset($meta['pengirim_id']) ? $meta['pengirim_id'] : null;
                if ($pengirim_id) {
                    if (strpos((string)$pengirim_id, 'komunitas_') === 0) {
                        $unread_chat = $this->db->where([
                            'user_id' => $user_id,
                            'type'    => 'chat_masuk',
                            'url'     => 'chat?user=' . $pengirim_id,
                            'is_read' => 0
                        ])->count_all_results('dashboard_notifications');
                    } else {
                        $unread_chat = $this->db->where([
                            'penerima_id' => $user_id,
                            'pengirim_id' => $pengirim_id,
                            'is_read'     => 0
                        ])->count_all_results('chat_messages');
                    }

                    if ($unread_chat == 0) {
                        $this->markAsRead(isset($notif->id) ? $notif->id : (isset($notif->id_notif) ? $notif->id_notif : 0), $user_id);
                        continue;
                    }
                    $url = 'chat?user=' . $pengirim_id;
                }
            }
            $items[] = [
                'type'     => $notif->type,
                'icon'     => $this->getIcon($notif->type),
                'priority' => 4,
                'color'    => $this->getColor($notif->type),
                'title'    => $notif->title,
                'body'     => $notif->body,
                'url'      => $url,
                'age'      => $notif->created_at,
                'is_read'  => (int)$notif->is_read,
                'id'       => isset($notif->id_notif) ? $notif->id_notif : (isset($notif->id) ? $notif->id : null),
            ];
        }

        usort($items, function($a, $b) {
            if ($a['priority'] !== $b['priority']) return $a['priority'] - $b['priority'];
            return strcmp($b['age'] ?? '', $a['age'] ?? '');
        });

        return $items;
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function getIcon($type) {
        $map = [
            'tugas_baru'       => '📝',
            'nilai_keluar'     => '⭐',
            'chat_masuk'       => '💬',
            'honor_pending'    => '💰',
            'materi_baru'      => '📗',
            'deadline_dekat'   => '⏰',
            'ujian_baru'       => '📋',
            'absensi'          => '📅',
            'tugas_dikumpulkan'=> '✅',
        ];
        return isset($map[$type]) ? $map[$type] : '🔔';
    }

    private function getColor($type) {
        $map = [
            'tugas_baru'       => 'primary',
            'nilai_keluar'     => 'success',
            'chat_masuk'       => 'info',
            'honor_pending'    => 'warning',
            'materi_baru'      => 'success',
            'deadline_dekat'   => 'danger',
            'ujian_baru'       => 'primary',
            'absensi'          => 'secondary',
            'tugas_dikumpulkan'=> 'teal',
        ];
        return isset($map[$type]) ? $map[$type] : 'secondary';
    }

    /**
     * Format waktu relatif (misal: "5 menit lalu", "kemarin")
     */
    public function formatAge($datetime) {
        if (!$datetime) return '';
        $now   = new DateTime();
        $then  = new DateTime($datetime);
        $diff  = $now->diff($then);

        if ($diff->days === 0) {
            if ($diff->h === 0) {
                if ($diff->i < 1) return 'Baru saja';
                return $diff->i . ' menit lalu';
            }
            return $diff->h . ' jam lalu';
        } elseif ($diff->days === 1) {
            return 'Kemarin';
        } elseif ($diff->days <= 6) {
            return $diff->days . ' hari lalu';
        }
        return $then->format('d M Y');
    }
}
