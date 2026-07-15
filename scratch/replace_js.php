<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php');

$search1 = <<<'EOD'
              if (arrKelasMateri[kelas] != null && arrKelasMateri[kelas].length > 0) {
                  for (let j = 0; j < arrKelasMateri[kelas].length; j++) {
                      const date = stringToDate(arrKelasMateri[kelas][j].jadwal);
                      tanggalSingkat = dateToString(date);
                      tanggalLengkap = dateToStringDay(date);
                      dropMateri.append('<option value="' + arrKelasMateri[kelas][j].id_kjm + '">' + arrKelasMateri[kelas][j].kode + ' ' + tanggalSingkat + '</option>');
                  }
              } else {
EOD;

$replace1 = <<<'EOD'
              if (arrKelasMateri[kelas] != null && arrKelasMateri[kelas].length > 0) {
                  for (let j = 0; j < arrKelasMateri[kelas].length; j++) {
                      var item = arrKelasMateri[kelas][j];
                      var textOpsi = item.kode + ' - ' + (item.judul_materi || 'Materi');
                      dropMateri.append('<option value="' + item.id_kjm + '">' + textOpsi + '</option>');
                  }
              } else {
EOD;

$search2 = <<<'EOD'
              if (arrKelasTugas[kelas] != null && arrKelasTugas[kelas].length > 0) {
                  for (let k = 0; k < arrKelasTugas[kelas].length; k++) {
                      const date = stringToDate(arrKelasTugas[kelas][k].jadwal);
                      tanggalSingkat = dateToString(date);
                      tanggalLengkap = dateToStringDay(date);
                      dropMateri.append('<option value="' + arrKelasTugas[kelas][k].id_kjm + '">' + arrKelasTugas[kelas][k].kode + ' ' + tanggalSingkat + '</option>');
                  }
              } else {
EOD;

$replace2 = <<<'EOD'
              if (arrKelasTugas[kelas] != null && arrKelasTugas[kelas].length > 0) {
                  for (let k = 0; k < arrKelasTugas[kelas].length; k++) {
                      var item = arrKelasTugas[kelas][k];
                      var textOpsi = item.kode + ' - ' + (item.judul_materi || 'Tugas');
                      dropMateri.append('<option value="' + item.id_kjm + '">' + textOpsi + '</option>');
                  }
              } else {
EOD;

$c = str_replace($search1, $replace1, $c);
$c = str_replace($search2, $replace2, $c);

// Also we need to make sure the detail fields "Jam ke, Dari, Sampai" are '-'
// Look for where jamKe, waktu.dari, waktu.sampai are set in data.php
// The user said: "Pada event $('#materi').on('change', function() {...}), pastikan JS mengisi teks ke elemen detail (Jam ke, Dari, Sampai) dengan nilai bawaan statis '-'"
// Wait, the detail is actually populated by `loadStatus` response!
// `detail.waktu.dari` etc. are from `detail.waktu.dari`. In `Kelasstatus.php`, they are already `-`.
// But let's check `data.php` just in case.

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php', $c);
echo "Replaced dropMateri appending.\n";
