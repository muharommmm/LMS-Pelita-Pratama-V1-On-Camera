<?php
$file = 'application/controllers/Kelasstatus.php';
$content = file_get_contents($file);

$search1 = '"kode" => $m->kode_materi, "mapel" => $kode_mapel, "kelas" => unserialize($m->materi_kelas ?? \'\')';
$replace1 = '"kode" => $m->kode_materi, "judul_materi" => isset($m->judul_materi) ? $m->judul_materi : "Materi", "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "nama_guru" => isset($m->nama_guru) ? $m->nama_guru : "--", "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--", "kelas" => isset($m->materi_kelas) && !is_string($m->materi_kelas) ? unserialize($m->materi_kelas ?? \'\') : []';

$search2 = '"kode" => $m->kode_materi, "judul_materi" => $m->judul_materi, "mapel" => $kode_mapel, "guru" => $m->nama_guru, "jenis" => $m->jenis';
$replace2 = '"kode" => $m->kode_materi, "judul_materi" => $m->judul_materi, "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "guru" => $m->nama_guru, "nama_guru" => $m->nama_guru, "jenis" => $m->jenis, "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--"';

$content = str_replace($search1, $replace1, $content);
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
echo "Patched Kelasstatus.php successfully!\n";
