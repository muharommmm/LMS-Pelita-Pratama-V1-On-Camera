<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php';
$c = file_get_contents($f);

$old_func = "private function syncOfficialAttendance(\$id_siswa, \$id_kjm) {
        \$this->load->model('Dashboard_model', 'dashboard');
        \$tp = \$this->dashboard->getTahunActive();
        \$smt = \$this->dashboard->getSemesterActive();

        // Fetch schedule directly (it already stores id_mapel, id_kelas, id_tp, id_smt)
        \$schedule = \$this->db->where(['id_kjm' => \$id_kjm])->get('kelas_jadwal_materi')->row();
        if (!\$schedule) return false;

        // id_mapel and id_kelas come from schedule - no need for separate lookup
        \$id_mapel = \$schedule->id_mapel;
        \$id_kelas = \$schedule->id_kelas;

        // Still verify student exists in this class for active year/semester
        \$student = \$this->db->where([
            'id_siswa' => \$id_siswa,
            'id_kelas' => \$id_kelas,
            'id_tp'    => \$tp->id_tp,
            'id_smt'   => \$smt->id_smt
        ])->get('kelas_siswa')->row();
        if (!\$student) return false;

        // Get tutor id from kelas_materi
        \$materi = \$this->db->where(['id_materi' => \$schedule->id_materi])->get('kelas_materi')->row();
        \$tutor_id = \$materi ? \$materi->id_guru : null;

        \$insert_data = [
            'student_id'     => \$id_siswa,
            'class_id'       => \$id_kelas,
            'tp_id'          => \$tp->id_tp,
            'smt_id'         => \$smt->id_smt,
            'mapel_id'       => \$id_mapel,
            'date'           => \$schedule->jadwal_materi,
            'time'           => date('H:i:s'),
            'status'         => 'H',
            'method'         => 'manual_tutor',
            'tutor_id_input' => \$tutor_id,
            'notes'          => '[Auto] Hadir via Materi/Tugas Online',
            'jenis_kegiatan' => 'online'
        ];

        \$exists = \$this->db->where([
            'student_id' => \$id_siswa,
            'class_id'   => \$id_kelas,
            'tp_id'      => \$tp->id_tp,
            'smt_id'     => \$smt->id_smt,
            'mapel_id'   => \$id_mapel,
            'date'       => \$schedule->jadwal_materi
        ])->get('absensi_siswa')->row();

        if (\$exists) {
            \$this->db->where('id_absensi', \$exists->id_absensi);
            return \$this->db->update('absensi_siswa', \$insert_data);
        } else {
            return \$this->db->insert('absensi_siswa', \$insert_data);
        }
    }";

$new_func = "private function syncOfficialAttendance(\$id_siswa, \$id_kjm) {
        \$this->load->model('Dashboard_model', 'dashboard');
        \$tp = \$this->dashboard->getTahunActive();
        \$smt = \$this->dashboard->getSemesterActive();

        // id_kjm represents id_materi in flexible schedule
        \$materi = \$this->db->where(['id_materi' => \$id_kjm])->get('kelas_materi')->row();
        if (!\$materi) return false;

        \$student = \$this->db->where([
            'id_siswa' => \$id_siswa,
            'id_tp'    => \$tp->id_tp,
            'id_smt'   => \$smt->id_smt
        ])->get('kelas_siswa')->row();
        
        if (!\$student) return false;
        
        \$id_kelas = \$student->id_kelas;
        \$id_mapel = \$materi->id_mapel;
        \$tutor_id = \$materi->id_guru;

        \$insert_data = [
            'student_id'     => \$id_siswa,
            'class_id'       => \$id_kelas,
            'tp_id'          => \$tp->id_tp,
            'smt_id'         => \$smt->id_smt,
            'mapel_id'       => \$id_mapel,
            'date'           => date('Y-m-d'),
            'time'           => date('H:i:s'),
            'status'         => 'H',
            'method'         => 'manual_tutor',
            'tutor_id_input' => \$tutor_id,
            'notes'          => '[Auto] Hadir via Materi/Tugas Online',
            'jenis_kegiatan' => 'online'
        ];

        \$exists = \$this->db->where([
            'student_id' => \$id_siswa,
            'class_id'   => \$id_kelas,
            'tp_id'      => \$tp->id_tp,
            'smt_id'     => \$smt->id_smt,
            'mapel_id'   => \$id_mapel,
            'date'       => date('Y-m-d')
        ])->get('absensi_siswa')->row();

        if (\$exists) {
            \$this->db->where('id_absensi', \$exists->id_absensi);
            return \$this->db->update('absensi_siswa', \$insert_data);
        } else {
            return \$this->db->insert('absensi_siswa', \$insert_data);
        }
    }";

if (strpos($c, $old_func) !== false) {
    $c = str_replace($old_func, $new_func, $c);
    file_put_contents($f, $c);
    echo "Replaced syncOfficialAttendance in Siswa.php.\n";
} else {
    echo "Could not find exactly matching string for old_func.\n";
}
