<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Honor_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get honor rates for a tutor or global rate
     */
    public function get_rate_by_tutor($tutor_id) {
        $rate = $this->db->where(['tutor_id' => $tutor_id])->get('honor_rates')->row();
        if (!$rate) {
            // Fallback to global rate (where tutor_id is NULL)
            $rate = $this->db->where(['tutor_id' => NULL])->get('honor_rates')->row();
        }
        if (!$rate) {
            // Default dummy rates if database is empty
            $rate = (object)[
                'id_rate' => 0,
                'tutor_id' => NULL,
                'rate_offline' => 50000.00,
                'rate_online' => 35000.00,
                'rate_check_task' => 2000.00,
                'rate_create_cbt' => 15000.00
            ];
        }
        return $rate;
    }

    /**
     * Get all custom and global rates
     */
    public function get_rates() {
        $this->db->select('honor_rates.*, master_guru.nama_guru');
        $this->db->from('honor_rates');
        $this->db->join('master_guru', 'master_guru.id_guru = honor_rates.tutor_id', 'left');
        return $this->db->get()->result();
    }

    /**
     * Save rate
     */
    public function save_rate($data) {
        return $this->db->replace('honor_rates', $data);
    }

    /**
     * Save single honor record
     */
    public function save_honor_record($data) {
        return $this->db->replace('honor_records', $data);
    }

    /**
     * Save mutation
     */
    public function save_mutation($data) {
        return $this->db->insert('honor_mutations', $data);
    }

    /**
     * Get mutations list
     */
    public function get_mutations($tutor_id) {
        $this->db->select('honor_mutations.*');
        $this->db->from('honor_mutations');
        $this->db->join('honor_records', 'honor_records.mutation_id = honor_mutations.id_mutation');
        $this->db->where('honor_mutations.tutor_id', $tutor_id);
        $this->db->where('honor_records.status', 'paid');
        $this->db->group_by('honor_mutations.id_mutation');
        $this->db->order_by('honor_mutations.transaction_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single mutation by ID
     */
    public function get_mutation_by_id($id_mutation) {
        return $this->db->where(['id_mutation' => $id_mutation])->get('honor_mutations')->row();
    }

    /**
     * Update mutation confirmation
     */
    public function confirm_mutation($id_mutation) {
        $this->db->where('id_mutation', $id_mutation);
        return $this->db->update('honor_mutations', ['status_konfirmasi_tutor' => 1]);
    }

    /**
     * Get honor record details with optional status and month/year filtering
     */
    public function get_honor_records($tutor_id, $status = null, $start_date = null, $end_date = null) {
        $this->db->select('*');
        $this->db->from('honor_records');
        $this->db->where('tutor_id', $tutor_id);
        if ($status !== null) {
            $this->db->where('status', $status);
        }
        if ($start_date !== null) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
        }
        if ($end_date !== null) {
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        $this->db->order_by('created_at', 'DESC');
        $records = $this->db->get()->result();

        // Enrich records with nama_kelas and nama_mapel
        if (!empty($records)) {
            // Fetch class names
            $class_names = [];
            $classes = $this->db->get('master_kelas')->result();
            foreach ($classes as $c) {
                $class_names[$c->id_kelas] = $c->nama_kelas;
            }

            // Fetch mapel names
            $mapel_names = [];
            $mapels = $this->db->get('master_mapel')->result();
            foreach ($mapels as $m) {
                $mapel_names[$m->id_mapel] = $m->nama_mapel;
            }

            // Group absensi_siswa records by session to support merged classes display
            $absensi_map = [];
            $this->db->select('MIN(class_id) as min_class_id, GROUP_CONCAT(DISTINCT class_id) as all_class_ids, mapel_id, date, jenis_kegiatan');
            $this->db->from('absensi_siswa');
            $this->db->where('tutor_id_input', $tutor_id);
            $this->db->group_by(['mapel_id', 'date', 'time', 'jenis_kegiatan']);
            $session_groups = $this->db->get()->result();

            foreach ($session_groups as $group) {
                $jk = !empty($group->jenis_kegiatan) ? $group->jenis_kegiatan : 'offline';
                $hash = crc32($group->min_class_id . '-' . $group->mapel_id . '-' . $group->date . '-' . $jk) & 0x7FFFFFFF;
                $absensi_map[$hash] = [
                    'class_ids' => explode(',', $group->all_class_ids),
                    'mapel_id'  => $group->mapel_id
                ];

                // Backward compatibility old hash (without jenis_kegiatan)
                $old_hash = crc32($group->min_class_id . '-' . $group->mapel_id . '-' . $group->date) & 0x7FFFFFFF;
                if (!isset($absensi_map[$old_hash])) {
                    $absensi_map[$old_hash] = [
                        'class_ids' => explode(',', $group->all_class_ids),
                        'mapel_id'  => $group->mapel_id
                    ];
                }
            }

            foreach ($records as $rec) {
                $class_ids = [];
                $class_id = null;
                $mapel_id = null;

                // 1. Try absensi map first (for ANY type, since absensi_siswa can create any type manually)
                if (isset($absensi_map[$rec->reference_id])) {
                    $class_ids = $absensi_map[$rec->reference_id]['class_ids'];
                    $mapel_id = $absensi_map[$rec->reference_id]['mapel_id'];
                }

                // 2. Try absensi_siswa DB fallback for ANY type
                if (empty($class_ids) && !$mapel_id) {
                    $this->db->select('class_id, mapel_id');
                    $this->db->from('absensi_siswa');
                    $this->db->where('tutor_id_input', $tutor_id);
                    $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date, "-", jenis_kegiatan)) & 2147483647) =', $rec->reference_id);
                    $this->db->limit(1);
                    $direct = $this->db->get()->row();
                    if ($direct) {
                        $class_id = $direct->class_id;
                        $mapel_id = $direct->mapel_id;
                    } else {
                        // Old fallback
                        $this->db->select('class_id, mapel_id');
                        $this->db->from('absensi_siswa');
                        $this->db->where('tutor_id_input', $tutor_id);
                        $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date)) & 2147483647) =', $rec->reference_id);
                        $this->db->limit(1);
                        $direct = $this->db->get()->row();
                        if ($direct) {
                            $class_id = $direct->class_id;
                            $mapel_id = $direct->mapel_id;
                        }
                    }
                }

                // 3. Type specific fallback
                if (empty($class_ids) && !$class_id && !$mapel_id) {
                    if ($rec->type == 'check_task') {
                        $this->db->select('kelas_materi.id_mapel, kelas_siswa.id_kelas');
                        $this->db->from('log_materi');
                        $this->db->join('kelas_siswa', 'kelas_siswa.id_siswa = log_materi.id_siswa', 'left');
                        $this->db->join('kelas_materi', 'kelas_materi.id_materi = log_materi.id_materi', 'left');
                        $this->db->where('(CRC32(CAST(log_materi.id_log AS CHAR)) & 2147483647) =', $rec->reference_id);
                        $this->db->limit(1);
                        $task_info = $this->db->get()->row();
                        if ($task_info) {
                            $class_id = $task_info->id_kelas;
                            $mapel_id = $task_info->id_mapel;
                        }
                    } elseif ($rec->type == 'create_cbt') {
                        $this->db->select('bank_mapel_id, bank_kelas');
                        $this->db->from('cbt_bank_soal');
                        $this->db->where('id_bank', $rec->reference_id);
                        $this->db->limit(1);
                        $cbt_info = $this->db->get()->row();
                        if ($cbt_info) {
                            $mapel_id = $cbt_info->bank_mapel_id;
                            $cbt_classes = @unserialize($cbt_info->bank_kelas);
                            if (is_array($cbt_classes) && !empty($cbt_classes)) {
                                $first_class = reset($cbt_classes);
                                $class_id = isset($first_class['kelas']) ? $first_class['kelas'] : null;
                            }
                        }
                    }
                }

                if (!empty($class_ids)) {
                    $names = [];
                    foreach ($class_ids as $cid) {
                        if (isset($class_names[$cid])) {
                            $names[] = $class_names[$cid];
                        }
                    }
                    $rec->nama_kelas = implode(', ', $names);
                } else {
                    $rec->nama_kelas = isset($class_names[$class_id]) ? $class_names[$class_id] : '-';
                }
                $rec->nama_mapel = isset($mapel_names[$mapel_id]) ? $mapel_names[$mapel_id] : '-';
            }
        }

        return $records;
    }

    /**
     * Get summary of all tutors for Admin
     */
    public function get_all_tutors_summary($start_date, $end_date) {
        // Calculate pending, approved, and paid amounts per tutor for a specific date range
        $this->db->select('
            master_guru.id_guru, 
            master_guru.nama_guru, 
            master_guru.nip,
            GROUP_CONCAT(DISTINCT DATE_FORMAT(honor_records.created_at, "%M %Y") SEPARATOR ", ") AS periode_mengajar,
            COALESCE(SUM(CASE WHEN LOWER(status) = "pending" THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as total_pending,
            COALESCE(SUM(CASE WHEN LOWER(status) = "approved" THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as total_approved,
            COALESCE(SUM(CASE WHEN LOWER(status) = "paid" THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as total_paid
        ', FALSE);
        $this->db->from('master_guru');
        $this->db->join('honor_records', 'honor_records.tutor_id = master_guru.id_guru AND honor_records.created_at >= ' . $this->db->escape($start_date . ' 00:00:00') . ' AND honor_records.created_at <= ' . $this->db->escape($end_date . ' 23:59:59'), 'left');
        $this->db->group_by('master_guru.id_guru');
        $this->db->order_by('master_guru.nama_guru', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Process Payout: Change status to paid and link to mutation record
     */
    public function pay_honor($tutor_id, $amount, $notes, $start_date, $end_date) {
        $this->db->trans_start();

        // Generate Indonesian month-year period automatically
        $months_id = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];
        
        $fallback_start = !empty($start_date) ? $start_date : date('Y-m-01');
        $fallback_end = !empty($end_date) ? $end_date : date('Y-m-t');

        $time_start = strtotime($fallback_start);
        $time_end = strtotime($fallback_end);

        $m_start = intval(date('n', $time_start));
        $y_start = date('Y', $time_start);
        $bulan_mulai = ($m_start >= 1 && $m_start <= 12) ? ($months_id[$m_start] . ' ' . $y_start) : date('F Y', $time_start);

        $m_end = intval(date('n', $time_end));
        $y_end = date('Y', $time_end);
        $bulan_akhir = ($m_end >= 1 && $m_end <= 12) ? ($months_id[$m_end] . ' ' . $y_end) : date('F Y', $time_end);

        $periode_teks = ($bulan_mulai == $bulan_akhir) ? $bulan_mulai : $bulan_mulai . ' - ' . $bulan_akhir;
        $keterangan_periode = "Honorarium Periode: " . $periode_teks;
        if (!empty($notes)) {
            $keterangan_periode .= " (" . $notes . ")";
        }

        // 1. Create debit mutation
        $mutation_data = [
            'tutor_id' => $tutor_id,
            'amount' => $amount,
            'type' => 'debit',
            'notes' => $keterangan_periode,
            'transaction_date' => date('Y-m-d H:i:s'),
            'status_konfirmasi_tutor' => 0
        ];
        $this->db->insert('honor_mutations', $mutation_data);
        $mutation_id = $this->db->insert_id();

        // 2. Run FIFO payout process
        $this->proses_pembayaran_fifo($tutor_id, $amount, $start_date, $end_date, $mutation_id);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Allocate payments using FIFO logic and perform row splitting for partial records
     */
    public function proses_pembayaran_fifo($tutor_id, $nominal_bayar, $start_date, $end_date, $mutation_id) {
        // Tarik semua data tagihan yang sah (urutkan dari yang paling lama)
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('status', 'approved');
        $this->db->where('created_at >=', $start_date . ' 00:00:00');
        $this->db->where('created_at <=', $end_date . ' 23:59:59');
        $this->db->order_by('created_at', 'ASC'); // Bayar yang paling tua dulu
        $tagihan = $this->db->get('honor_records')->result();

        $sisa_uang = $nominal_bayar;

        foreach ($tagihan as $row) {
            if ($sisa_uang <= 0) break; // Uang sudah habis

            // Tentukan nilai tagihan baris ini
            $tagihan_baris = ($row->adjusted_amount !== null && floatval($row->adjusted_amount) > 0) ? floatval($row->adjusted_amount) : floatval($row->amount);

            if ($sisa_uang >= $tagihan_baris) {
                // Kasus 1: Uang cukup untuk melunasi penuh baris ini
                $this->db->where('id_honor_record', $row->id_honor_record);
                $this->db->update('honor_records', [
                    'status' => 'paid',
                    'mutation_id' => $mutation_id
                ]);

                $sisa_uang -= $tagihan_baris;
            } else {
                // Kasus 2: Uang TIDAK CUKUP (Pembayaran Parsial / Cicilan)
                // A. Ubah baris saat ini menjadi paid dengan nominal seadanya (sisa uang)
                $this->db->where('id_honor_record', $row->id_honor_record);
                $this->db->update('honor_records', [
                    'status' => 'paid',
                    'adjusted_amount' => $sisa_uang, // Timpa dengan nominal yang berhasil dibayar
                    'mutation_id' => $mutation_id
                ]);

                // B. Pecah Baris: Insert sisa tagihan ke baris baru agar tetap tertagih
                $sisa_tagihan = $tagihan_baris - $sisa_uang;
                $baris_baru = (array) $row;
                unset($baris_baru['id_honor_record']); // Hilangkan ID agar auto-increment
                $baris_baru['status'] = 'approved';
                $baris_baru['adjusted_amount'] = $sisa_tagihan; // Nominal sisa
                $baris_baru['mutation_id'] = null; // Reset mutation link on the unpaid split part
                $this->db->insert('honor_records', $baris_baru);

                $sisa_uang = 0;
                break; // Pembayaran selesai
            }
        }
    }

    /**
     * Automatically sync/calculate teacher activities into honor_records
     */
    public function sync_honor($tutor_id, $tp_id, $smt_id) {
        $rate = $this->get_rate_by_tutor($tutor_id);

        // A. Sync Class Sessions (from absensi_siswa)
        // Grouped by mapel, date, time, and jenis_kegiatan to handle merged classes as 1 session
        $this->db->select('MIN(class_id) as class_id, mapel_id, date, jenis_kegiatan, COUNT(id_absensi) as student_count');
        $this->db->from('absensi_siswa');
        $this->db->where('tutor_id_input', $tutor_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $this->db->where('method', 'manual_tutor');
        $this->db->group_by(['mapel_id', 'date', 'time', 'jenis_kegiatan']);
        $sessions = $this->db->get()->result();

        $active_refs = [];
        foreach ($sessions as $session) {
            // Use fallback if jenis_kegiatan is empty
            $jenis_keg = !empty($session->jenis_kegiatan) ? $session->jenis_kegiatan : 'offline';
            
            // Unique reference logic: hash of class, mapel, date, jenis_kegiatan
            $ref_id = crc32($session->class_id . '-' . $session->mapel_id . '-' . $session->date . '-' . $jenis_keg) & 0x7FFFFFFF;
            $active_refs[] = $ref_id;
            
            // Check if record already exists
            $exists = $this->db->where([
                'tutor_id' => $tutor_id,
                'tp_id' => $tp_id,
                'smt_id' => $smt_id,
                'reference_id' => $ref_id
            ])->get('honor_records')->row();

            if (!$exists) {
                // Determine rate based on jenis_kegiatan
                if ($jenis_keg == 'online') {
                    $rate_val = floatval($rate->rate_online);
                    $type_val = 'online';
                } elseif ($jenis_keg == 'offline') {
                    $rate_val = floatval($rate->rate_offline);
                    $type_val = 'offline';
                } elseif ($jenis_keg == 'check_task' || $jenis_keg == 'tugas') {
                    $rate_val = floatval($rate->rate_check_task);
                    $type_val = 'check_task';
                } elseif ($jenis_keg == 'create_cbt' || $jenis_keg == 'cbt') {
                    $rate_val = floatval($rate->rate_create_cbt);
                    $type_val = 'create_cbt';
                } else {
                    $rate_val = floatval($rate->rate_offline);
                    $type_val = 'offline';
                }

                // Smart Validation: Auto-Approve / Pending
                // Check if class/mapel/day/jenis_kegiatan matches in jadwal_fleksibel
                $day_of_week = date('N', strtotime($session->date));
                
                $cek_jadwal = $this->db->where([
                    'class_id' => $session->class_id,
                    'mapel_id' => $session->mapel_id,
                    'day' => $day_of_week,
                    'jenis_kegiatan' => $jenis_keg,
                    'tp_id' => $tp_id,
                    'smt_id' => $smt_id
                ])->get('jadwal_fleksibel')->row();

                $status_honor = ($cek_jadwal) ? 'approved' : 'pending';

                $this->save_honor_record([
                    'tutor_id' => $tutor_id,
                    'tp_id' => $tp_id,
                    'smt_id' => $smt_id,
                    'type' => $type_val,
                    'reference_id' => $ref_id,
                    'qty' => 1,
                    'rate' => $rate_val,
                    'amount' => $rate_val,
                    'status' => $status_honor,
                    'created_at' => $session->date . ' ' . date('H:i:s')
                ]);
            }
        }

        // Collect refs from Section A that are check_task or create_cbt
        // These will be merged into Section B/C active_refs to prevent them from being rejected
        $absensi_check_task_refs = [];
        $absensi_create_cbt_refs = [];
        foreach ($sessions as $session) {
            $jk = !empty($session->jenis_kegiatan) ? $session->jenis_kegiatan : 'offline';
            $ref = crc32($session->class_id . '-' . $session->mapel_id . '-' . $session->date . '-' . $jk) & 0x7FFFFFFF;
            if ($jk == 'check_task' || $jk == 'tugas') {
                $absensi_check_task_refs[] = $ref;
            } elseif ($jk == 'create_cbt' || $jk == 'cbt') {
                $absensi_create_cbt_refs[] = $ref;
            }
        }

        // Clean up deleted offline/online sessions (only if pending or approved) -> Soft Delete
        // IMPORTANT: Only target offline/online here. check_task and create_cbt are handled by Section B and C cleanups.
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $this->db->where_in('type', ['offline', 'online']);
        $this->db->where_in('status', ['pending', 'approved']);
        $this->db->group_start();
            $this->db->where('admin_notes IS NULL');
            $this->db->or_not_like('admin_notes', '[Input Massal]');
        $this->db->group_end();
        if (!empty($active_refs)) {
            $this->db->where_not_in('reference_id', $active_refs);
        }
        $this->db->update('honor_records', ['status' => 'rejected']);

        // B. Sync Checked Tasks (from log_materi where nilai is graded and jenis is task)
        $this->db->select('log_materi.id_log, kelas_materi.id_materi');
        $this->db->from('log_materi');
        $this->db->join('kelas_materi', 'kelas_materi.id_materi = log_materi.id_materi');
        $this->db->where('kelas_materi.id_guru', $tutor_id);
        $this->db->where('kelas_materi.id_tp', $tp_id);
        $this->db->where('kelas_materi.id_smt', $smt_id);
        $this->db->where('kelas_materi.jenis', 2); // 2 = tugas
        $this->db->where('log_materi.nilai IS NOT NULL');
        $graded_tasks = $this->db->get()->result();

        $active_task_refs = [];
        foreach ($graded_tasks as $task) {
            $ref_id = crc32((string)$task->id_log) & 0x7FFFFFFF;
            $active_task_refs[] = $ref_id;

            $exists = $this->db->where([
                'tutor_id' => $tutor_id,
                'tp_id' => $tp_id,
                'smt_id' => $smt_id,
                'type' => 'check_task',
                'reference_id' => $ref_id
            ])->get('honor_records')->row();

            if (!$exists) {
                $amount = $rate->rate_check_task;
                $this->save_honor_record([
                    'tutor_id' => $tutor_id,
                    'tp_id' => $tp_id,
                    'smt_id' => $smt_id,
                    'type' => 'check_task',
                    'reference_id' => $ref_id,
                    'qty' => 1,
                    'rate' => $rate->rate_check_task,
                    'amount' => $amount,
                    'status' => 'pending'
                ]);
            }
        }

        // Clean up deleted tasks -> Soft Delete
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $this->db->where('type', 'check_task');
        $this->db->where_in('status', ['pending', 'approved']);
        $this->db->group_start();
            $this->db->where('admin_notes IS NULL');
            $this->db->or_not_like('admin_notes', '[Input Massal]');
        $this->db->group_end();
        // Merge refs from absensi-input check_task records so they survive this cleanup
        $active_task_refs = array_merge($active_task_refs, $absensi_check_task_refs);
        if (!empty($active_task_refs)) {
            $this->db->where_not_in('reference_id', $active_task_refs);
        }
        $this->db->update('honor_records', ['status' => 'rejected']);

        // C. Sync CBT Created (from cbt_bank_soal where status_soal = 1)
        $this->db->select('id_bank');
        $this->db->from('cbt_bank_soal');
        $this->db->where('bank_guru_id', $tutor_id);
        $this->db->where('id_tp', $tp_id);
        $this->db->where('id_smt', $smt_id);
        $this->db->where('status_soal', 1); // 1 = selesai
        $cbt_banks = $this->db->get()->result();

        $active_cbt_refs = [];
        foreach ($cbt_banks as $bank) {
            $ref_id = intval($bank->id_bank);
            $active_cbt_refs[] = $ref_id;

            $exists = $this->db->where([
                'tutor_id' => $tutor_id,
                'tp_id' => $tp_id,
                'smt_id' => $smt_id,
                'type' => 'create_cbt',
                'reference_id' => $ref_id
            ])->get('honor_records')->row();

            if (!$exists) {
                $amount = $rate->rate_create_cbt;
                $this->save_honor_record([
                    'tutor_id' => $tutor_id,
                    'tp_id' => $tp_id,
                    'smt_id' => $smt_id,
                    'type' => 'create_cbt',
                    'reference_id' => $ref_id,
                    'qty' => 1,
                    'rate' => $rate->rate_create_cbt,
                    'amount' => $amount,
                    'status' => 'pending'
                ]);
            }
        }

        // Clean up deleted CBTs -> Soft Delete
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->where('smt_id', $smt_id);
        $this->db->where('type', 'create_cbt');
        $this->db->where_in('status', ['pending', 'approved']);
        $this->db->group_start();
            $this->db->where('admin_notes IS NULL');
            $this->db->or_not_like('admin_notes', '[Input Massal]');
        $this->db->group_end();
        // Merge refs from absensi-input create_cbt records so they survive this cleanup
        $active_cbt_refs = array_merge($active_cbt_refs, $absensi_create_cbt_refs);
        if (!empty($active_cbt_refs)) {
            $this->db->where_not_in('reference_id', $active_cbt_refs);
        }
        $this->db->update('honor_records', ['status' => 'rejected']);
    }

    /**
     * Update honor record adjusted_amount and admin_notes
     */
    public function update_honor_by_admin($id_honor, $data) {
        $this->db->where('id_honor_record', $id_honor);
        return $this->db->update('honor_records', $data);
    }

    /**
     * Calculate slip data grouped by Mapel + Class within a date range
     */
    public function get_slip_per_tutor($tutor_id, $start_date, $end_date) {
        $this->db->select('honor_records.*');
        $this->db->from('honor_records');
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('status', 'approved');
        if ($start_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
        }
        if ($end_date) {
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        $records = $this->db->get()->result();

        // Fetch class names and mapel names
        $class_names = [];
        $classes = $this->db->get('master_kelas')->result();
        foreach ($classes as $c) {
            $class_names[$c->id_kelas] = $c->nama_kelas;
        }

        $mapel_names = [];
        $mapels = $this->db->get('master_mapel')->result();
        foreach ($mapels as $m) {
            $mapel_names[$m->id_mapel] = $m->nama_mapel;
        }

        // Fetch absensi_siswa details for this tutor
        $absensi_map = [];
        $this->db->select('class_id, mapel_id, date, jenis_kegiatan');
        $this->db->from('absensi_siswa');
        $this->db->where('tutor_id_input', $tutor_id);
        $abs_list = $this->db->get()->result();
        foreach ($abs_list as $abs) {
            $jk = !empty($abs->jenis_kegiatan) ? $abs->jenis_kegiatan : 'offline';
            $hash = crc32($abs->class_id . '-' . $abs->mapel_id . '-' . $abs->date . '-' . $jk) & 0x7FFFFFFF;
            $absensi_map[$hash] = [
                'class_id' => $abs->class_id,
                'mapel_id' => $abs->mapel_id
            ];
            // Also add fallback key without jenis_kegiatan for backward compatibility
            $old_hash = crc32($abs->class_id . '-' . $abs->mapel_id . '-' . $abs->date) & 0x7FFFFFFF;
            if (!isset($absensi_map[$old_hash])) {
                $absensi_map[$old_hash] = [
                    'class_id' => $abs->class_id,
                    'mapel_id' => $abs->mapel_id
                ];
            }
        }

        $aggregated = [];
        foreach ($records as $rec) {
            $class_id = null;
            $mapel_id = null;

            if (isset($absensi_map[$rec->reference_id])) {
                $class_id = $absensi_map[$rec->reference_id]['class_id'];
                $mapel_id = $absensi_map[$rec->reference_id]['mapel_id'];
            }

            if (!$class_id && !$mapel_id) {
                $this->db->select('class_id, mapel_id');
                $this->db->from('absensi_siswa');
                $this->db->where('tutor_id_input', $tutor_id);
                $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date, "-", jenis_kegiatan)) & 2147483647) =', $rec->reference_id);
                $this->db->limit(1);
                $direct = $this->db->get()->row();
                if ($direct) {
                    $class_id = $direct->class_id;
                    $mapel_id = $direct->mapel_id;
                } else {
                    $this->db->select('class_id, mapel_id');
                    $this->db->from('absensi_siswa');
                    $this->db->where('tutor_id_input', $tutor_id);
                    $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date)) & 2147483647) =', $rec->reference_id);
                    $this->db->limit(1);
                    $direct = $this->db->get()->row();
                    if ($direct) {
                        $class_id = $direct->class_id;
                        $mapel_id = $direct->mapel_id;
                    }
                }
            }

            if (!$class_id && !$mapel_id) {
                if ($rec->type == 'check_task') {
                    $this->db->select('kelas_materi.id_mapel, kelas_siswa.id_kelas');
                    $this->db->from('log_materi');
                    $this->db->join('kelas_siswa', 'kelas_siswa.id_siswa = log_materi.id_siswa', 'left');
                    $this->db->join('kelas_materi', 'kelas_materi.id_materi = log_materi.id_materi', 'left');
                    $this->db->where('(CRC32(CAST(log_materi.id_log AS CHAR)) & 2147483647) =', $rec->reference_id);
                    $task_info = $this->db->get()->row();
                    if ($task_info) {
                        $class_id = $task_info->id_kelas;
                        $mapel_id = $task_info->id_mapel;
                    }
                } elseif ($rec->type == 'create_cbt') {
                    $this->db->select('bank_mapel_id, bank_kelas');
                    $this->db->from('cbt_bank_soal');
                    $this->db->where('id_bank', $rec->reference_id);
                    $cbt_info = $this->db->get()->row();
                    if ($cbt_info) {
                        $mapel_id = $cbt_info->bank_mapel_id;
                        $classes = @unserialize($cbt_info->bank_kelas);
                        if (is_array($classes) && !empty($classes)) {
                            $first_class = reset($classes);
                            $class_id = isset($first_class['kelas']) ? $first_class['kelas'] : null;
                        }
                    }
                }
            }

            $class_name = isset($class_names[$class_id]) ? $class_names[$class_id] : 'Umum / Semua Kelas';
            $mapel_name = isset($mapel_names[$mapel_id]) ? $mapel_names[$mapel_id] : 'Umum / Semua Mapel';

            $key = $class_id . '_' . $mapel_id;
            if (!isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'class_id' => $class_id,
                    'mapel_id' => $mapel_id,
                    'class_name' => $class_name,
                    'mapel_name' => $mapel_name,
                    'qty_offline' => 0,
                    'qty_online' => 0,
                    'qty_check_task' => 0,
                    'qty_create_cbt' => 0,
                    'total_amount' => 0.00
                ];
            }

            $final_amount = ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? $rec->adjusted_amount : $rec->amount;
            $aggregated[$key]['total_amount'] += $final_amount;

            if ($rec->type == 'offline') {
                $aggregated[$key]['qty_offline'] += $rec->qty;
            } elseif ($rec->type == 'online') {
                $aggregated[$key]['qty_online'] += $rec->qty;
            } elseif ($rec->type == 'check_task') {
                $aggregated[$key]['qty_check_task'] += $rec->qty;
            } elseif ($rec->type == 'create_cbt') {
                $aggregated[$key]['qty_create_cbt'] += $rec->qty;
            }
        }

        return array_values($aggregated);
    }

    /**
     * Get raw tutor honor sum per level_id of classes for matrix reporting
     */
    public function get_rekap_yayasan_raw($start_date, $end_date) {
        $this->db->select('honor_records.*, master_guru.nama_guru, master_guru.nip');
        $this->db->from('honor_records');
        $this->db->join('master_guru', 'master_guru.id_guru = honor_records.tutor_id');
        $this->db->where("LOWER(honor_records.status) IN ('approved', 'paid')", NULL, FALSE);
        if ($start_date) {
            $this->db->where('honor_records.created_at >=', $start_date . ' 00:00:00');
        }
        if ($end_date) {
            $this->db->where('honor_records.created_at <=', $end_date . ' 23:59:59');
        }
        $records = $this->db->get()->result();

        // Fetch class levels
        $class_levels = [];
        $classes = $this->db->get('master_kelas')->result();
        foreach ($classes as $c) {
            $class_levels[$c->id_kelas] = $c->level_id;
        }

        $raw_mapped = [];
        foreach ($records as $rec) {
            $class_id = null;

            $this->db->select('class_id');
            $this->db->from('absensi_siswa');
            $this->db->where('tutor_id_input', $rec->tutor_id);
            $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date, "-", jenis_kegiatan)) & 2147483647) =', $rec->reference_id);
            $this->db->limit(1);
            $direct = $this->db->get()->row();
            if ($direct) {
                $class_id = $direct->class_id;
            } else {
                $this->db->select('class_id');
                $this->db->from('absensi_siswa');
                $this->db->where('tutor_id_input', $rec->tutor_id);
                $this->db->where('(CRC32(CONCAT(class_id, "-", mapel_id, "-", date)) & 2147483647) =', $rec->reference_id);
                $this->db->limit(1);
                $direct = $this->db->get()->row();
                if ($direct) {
                    $class_id = $direct->class_id;
                }
            }

            if (!$class_id) {
                if ($rec->type == 'check_task') {
                    $this->db->select('kelas_siswa.id_kelas');
                    $this->db->from('log_materi');
                    $this->db->join('kelas_siswa', 'kelas_siswa.id_siswa = log_materi.id_siswa', 'left');
                    $this->db->where('(CRC32(CAST(log_materi.id_log AS CHAR)) & 2147483647) =', $rec->reference_id);
                    $task_info = $this->db->get()->row();
                    if ($task_info) {
                        $class_id = $task_info->id_kelas;
                    }
                } elseif ($rec->type == 'create_cbt') {
                    $this->db->select('bank_kelas');
                    $this->db->from('cbt_bank_soal');
                    $this->db->where('id_bank', $rec->reference_id);
                    $cbt_info = $this->db->get()->row();
                    if ($cbt_info) {
                        $classes = @unserialize($cbt_info->bank_kelas);
                        if (is_array($classes) && !empty($classes)) {
                            $first_class = reset($classes);
                            $class_id = isset($first_class['kelas']) ? $first_class['kelas'] : null;
                        }
                    }
                }
            }

            $level_id = isset($class_levels[$class_id]) ? intval($class_levels[$class_id]) : null;
            $final_amount = ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? $rec->adjusted_amount : $rec->amount;

            $raw_mapped[] = (object)[
                'tutor_id' => $rec->tutor_id,
                'nama_guru' => $rec->nama_guru,
                'nip' => $rec->nip,
                'level_id' => $level_id,
                'amount' => $final_amount
            ];
        }

        return $raw_mapped;
    }

    /**
     * Get tutor accumulated income summary for a specific year (tp_id)
     */
    public function get_tutor_yearly_summary($tutor_id, $tp_id) {
        $this->db->select('
            type,
            COALESCE(SUM(CASE WHEN status = "paid" THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as paid_amount,
            COALESCE(SUM(CASE WHEN status = "approved" THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as unpaid_amount,
            COALESCE(SUM(CASE WHEN status IN ("approved", "paid") THEN (CASE WHEN adjusted_amount IS NOT NULL AND adjusted_amount > 0 THEN adjusted_amount ELSE amount END) ELSE 0 END), 0) as total_amount
        ');
        $this->db->from('honor_records');
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('tp_id', $tp_id);
        $this->db->group_by('type');
        return $this->db->get()->result();
    }

    /**
     * Delete any honor record permanently (Admin Only) -> Soft Delete
     */
    public function delete_record_by_admin($id_honor_record) {
        $this->db->where('id_honor_record', $id_honor_record);
        return $this->db->update('honor_records', ['status' => 'rejected']);
    }

    /**
     * Delete pending/approved honor record by guru owner -> Soft Delete
     */
    public function delete_honor_record($id_honor_record, $tutor_id) {
        $this->db->where('id_honor_record', $id_honor_record);
        $this->db->where('tutor_id', $tutor_id);
        $this->db->group_start();
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'approved');
        $this->db->group_end();
        return $this->db->update('honor_records', ['status' => 'rejected']);
    }

    /**
     * Batalkan status lunas (paid → approved) untuk seluruh record milik seorang tutor.
     * Digunakan Admin jika terjadi kesalahan proses bayar.
     */
    public function batalkan_status_lunas($tutor_id, $start_date, $end_date) {
        // 1. Dapatkan all mutation_ids yang akan dibatalkan
        $this->db->select('distinct(mutation_id) as mutation_id');
        $this->db->from('honor_records');
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('status', 'paid');
        $this->db->where('created_at >=', $start_date . ' 00:00:00');
        $this->db->where('created_at <=', $end_date . ' 23:59:59');
        $records = $this->db->get()->result();

        $mutation_ids = [];
        foreach ($records as $r) {
            if (!empty($r->mutation_id)) {
                $mutation_ids[] = intval($r->mutation_id);
            }
        }

        // 2. Kembalikan status rincian ke approved dan hilangkan link mutation_id
        $this->db->where('tutor_id', $tutor_id);
        $this->db->where('status', 'paid');
        $this->db->where('created_at >=', $start_date . ' 00:00:00');
        $this->db->where('created_at <=', $end_date . ' 23:59:59');
        $update_status = $this->db->update('honor_records', ['status' => 'approved', 'mutation_id' => NULL]);

        // 3. Hapus log riwayat pencairan (honor_mutations) jika belum dikonfirmasi tutor (status_konfirmasi_tutor = 0)
        if (!empty($mutation_ids)) {
            $this->db->where_in('id_mutation', $mutation_ids);
            $this->db->where('status_konfirmasi_tutor', 0);
            $this->db->delete('honor_mutations');
        }

        return $update_status;
    }
}
