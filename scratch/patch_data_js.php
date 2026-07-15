<?php
$file = 'application/views/kelas/status/data.php';
$content = file_get_contents($file);

$search1 = <<<EOT
                    var detail = data.detail || {};
                    var waktu = detail.waktu || {};
                    $('#info-judul').text(': ' + (detail.judul || '-'));
                    $('#info-mapel').text(': ' + (detail.mapel || '-'));
                    $('#info-guru').text(': ' + (detail.guru || '-'));
                    $('#info-kelas').text(': ' + (detail.kelas || '-'));
                    $('#info-jam').text(': ' + (detail.jam_ke || '-'));
                    $('#info-dari').text(': ' + (waktu.dari || '-'));
                    $('#info-sampai').text(': ' + (waktu.sampai || '-'));
EOT;

$replace1 = <<<EOT
                    var item = null;
                    if (label === 'Materi' && arrKelasMateri[selKelas]) {
                        for(var i=0; i<arrKelasMateri[selKelas].length; i++) {
                            if (arrKelasMateri[selKelas][i].id_kjm == selMateri) {
                                item = arrKelasMateri[selKelas][i];
                                break;
                            }
                        }
                    } else if (label === 'Tugas' && arrKelasTugas[selKelas]) {
                        for(var i=0; i<arrKelasTugas[selKelas].length; i++) {
                            if (arrKelasTugas[selKelas][i].id_kjm == selMateri) {
                                item = arrKelasTugas[selKelas][i];
                                break;
                            }
                        }
                    }

                    if (item) {
                        $('#info-judul').text(': ' + (item.judul_materi || '-'));
                        $('#info-mapel').text(': ' + (item.nama_mapel || '-'));
                        $('#info-guru').text(': ' + (item.nama_guru || '-'));
                        $('#info-kelas').text(': ' + (item.materi_kelas || 'Tidak diketahui'));
                        $('#info-jam').text(': ' + (item.jam_ke || '-'));
                        $('#info-dari').text(': ' + (item.tgl_mulai || '-'));
                        $('#info-sampai').text(': ' + (item.tgl_selesai || '-'));
                    } else {
                        var detail = data.detail || {};
                        var waktu = detail.waktu || {};
                        $('#info-judul').text(': ' + (detail.judul || '-'));
                        $('#info-mapel').text(': ' + (detail.mapel || '-'));
                        $('#info-guru').text(': ' + (detail.guru || '-'));
                        $('#info-kelas').text(': ' + (detail.kelas || 'Tidak diketahui'));
                        $('#info-jam').text(': ' + (detail.jam_ke || '-'));
                        $('#info-dari').text(': ' + (waktu.dari || '-'));
                        $('#info-sampai').text(': ' + (waktu.sampai || '-'));
                    }
EOT;

$content = str_replace($search1, $replace1, $content);

$search2 = 'var textOpsi = item.kode + " - " + (item.judul_materi || "Materi");';
$replace2 = 'var textOpsi = item.kode + " - " + (item.judul_materi || "");';
$content = str_replace($search2, $replace2, $content);

$search3 = 'var textOpsi = item.kode + " - " + (item.judul_materi || "Tugas");';
$replace3 = 'var textOpsi = item.kode + " - " + (item.judul_materi || "");';
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
echo "Patched data.php JS successfully!\n";
