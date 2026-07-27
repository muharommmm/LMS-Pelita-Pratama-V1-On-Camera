<!-- Content Wrapper. Contains page content -->
<main class="mt-16 lg:ml-64 p-4 md:p-6 w-full lg:w-[calc(100%-16rem)] overflow-x-hidden min-h-screen bg-slate-50">
    
    <!-- 4 PANEL WIDGET -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start pb-12">
        
        <!-- PANEL 1: AKTIVITAS & PENGINGAT -->
                    <div id="guru-activity-panel" class="mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between py-3 px-5 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="bg-indigo-100 text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center text-sm">
                                <i class="fas fa-bolt text-indigo-500"></i>    
                            </div>
                            <h6 class="mb-0 text-gray-800 font-bold tracking-tight text-sm">
                                AKTIVITAS & TUGAS ANDA
                            </h6>
                            <span id="feed-badge" class="bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <small id="feed-time" class="text-gray-400 font-medium text-xs"></small>
                            <button id="btn-baca-semua" class="hidden text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1 rounded-full bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                Tandai Semua Dibaca
                            </button>
                            <button id="btn-toggle-feed" class="text-gray-400 hover:text-gray-600 transition-colors" title="Sembunyikan/Tampilkan">
                                <i class="fas fa-chevron-up" id="feed-chevron"></i>
                            </button>
                        </div>
                    </div>
                    <div id="feed-body" class="transition-all duration-300 ease-in-out">
                        <div id="feed-loading" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mb-2"></div>
                            <div class="text-gray-400 text-sm font-medium">Memuat aktivitas...</div>
                        </div>
                        <div id="feed-items" class="hidden max-h-[320px] overflow-y-auto custom-scrollbar"></div>
                        <div id="feed-empty" class="text-center py-10 hidden">
                            <div class="text-4xl mb-3 opacity-50">Semua tenang</div>
                            <p class="text-gray-500 font-medium text-sm">Semua tugas sudah beres! Tidak ada notifikasi baru.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Styles untuk activity feed modern -->
            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

                .feed-item {
                    display: flex; align-items: flex-start;
                    padding: 12px 20px;
                    border-bottom: 1px solid #f8fafc;
                    transition: all 0.2s ease;
                    cursor: pointer;
                    text-decoration: none;
                    background: #ffffff;
                }
                .feed-item:hover { background: #f8fafc; text-decoration: none; }
                .feed-item.is-read { opacity: 0.7; }
                .feed-item:last-child { border-bottom: none; }
                
                .feed-icon {
                    font-size: 1.25rem; margin-right: 16px; flex-shrink: 0;
                    width: 40px; height: 40px; border-radius: 50%;
                    display: flex; align-items: center; justify-content: center;
                }
                .feed-content { flex: 1; min-width: 0; }
                .feed-title {
                    font-weight: 600; font-size: 0.875rem;
                    color: #1e293b; margin-bottom: 2px;
                    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                }
                .feed-body-text { font-size: 0.8125rem; color: #64748b; margin: 0; line-height: 1.4; }
                .feed-age { font-size: 0.7rem; color: #94a3b8; margin-top: 4px; font-weight: 500; }
                .feed-arrow { color: #cbd5e1; font-size: 0.875rem; margin-left: 12px; flex-shrink: 0; align-self: center; transition: transform 0.2s; }
                .feed-item:hover .feed-arrow { transform: translateX(3px); color: #94a3b8; }
                
                .feed-color-warning .feed-icon { background: #fef3c7; color: #d97706; }
                .feed-color-danger  .feed-icon { background: #fee2e2; color: #dc2626; }
                .feed-color-info    .feed-icon { background: #e0f2fe; color: #0284c7; }
                .feed-color-success .feed-icon { background: #dcfce7; color: #16a34a; }
                .feed-color-primary .feed-icon { background: #e0e7ff; color: #4f46e5; }
            </style>


        <!-- PANEL 2: JADWAL HARI INI -->
                            <?php
                    $CI =& get_instance();
                    $CI->load->model('Jadwal_fleksibel_model', 'jf_helper');
                    $php_day = date('w'); 
                    $db_day = ($php_day == 0) ? 7 : $php_day;
                    $flex_schedules = $CI->jf_helper->get_schedules_by_tutor($guru->id_guru, $tp_active->id_tp, $smt_active->id_smt, $db_day);
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3">
                        <div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-500"></i> JADWAL MENGAJAR HARI INI</div>
                        <?php if (!empty($flex_schedules)) : ?>
                            <div class="card-body p-2">
                                <div class="overflow-x-auto w-full pb-4">
                                    <table class="w-full text-sm text-left [&_tr]:border-b [&_tr]:border-slate-100">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>Kelas</th>
                                                <th>Mapel</th>
                                                <th class="text-center">Link</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($flex_schedules as $row) : ?>
                                                <tr>
                                                    <td class="align-middle font-weight-bold text-teal">
                                                        <?= date('H:i', strtotime($row->start_time)) ?> - <?= date('H:i', strtotime($row->end_time)) ?>
                                                    </td>
                                                    <td class="align-middle"><?= htmlspecialchars($row->nama_kelas) ?></td>
                                                    <td class="align-middle"><?= htmlspecialchars($row->nama_mapel) ?></td>
                                                    <td class="align-middle text-center">
                                                        <?php if ($row->jenis_kegiatan == 'Tugas') : ?>
                                                            <span class="badge badge-warning font-weight-bold"><i class="fas fa-pencil-alt mr-1"></i> Tugas</span>
                                                        <?php elseif (!empty($row->learning_link)) : ?>
                                                            <a href="<?= htmlspecialchars($row->learning_link) ?>" target="_blank" class="btn btn-xs btn-success font-weight-bold">
                                                                <i class="fas fa-video mr-1"></i> Mulai Kelas (Link)
                                                            </a>
                                                        <?php else : ?>
                                                            <span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Kelas Offline</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ($row->jenis_kegiatan == 'Tugas') : ?>
                                                    <tr>
                                                        <td colspan="4" class="p-1">
                                                            <div class="alert alert-info mb-0 p-2 small font-weight-bold">
                                                                <i class="fas fa-info-circle mr-1"></i> Jadwal Tugas: Harap unggah/periksa tugas untuk kelas ini melalui menu E-Learning (Tugas).
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="px-4 py-3 bg-slate-50/50 rounded-b-xl border-t border-slate-100">
                                <p class="text-xs text-slate-400 italic text-center m-0">Tidak ada jadwal mengajar hari ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>


        <!-- PANEL 4: PENGUMUMAN -->
                            <div id="panel-pengumuman-wrapper" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3">
                        <div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> PENGUMUMAN</div>
                        <div class="konten-pengumuman">
                            <div id="pengumuman">
                            </div>
                            <p id="loading-post" class="text-center d-none pb-4">
                                <br/><i class="fa fa-spin fa-circle-o-notch"></i> Loading....
                            </p>
                            <div id="loadmore-post"
                                 onclick="getPosts()"
                                 class="text-center pb-4 mt-4 loadmore d-none">
                                <div class="btn btn-default">Muat Pengumuman lainnya ...</div>
                            </div>
                        </div>
                    </div>

        <!-- PANEL 3: AGENDA TERDEKAT -->
                            <?php
                    $CI =& get_instance();
                    $CI->load->model('Agenda_model', 'agenda_helper');
                    $active_agendas = $CI->agenda_helper->get_agendas_by_role('guru');
                    ?>
                    <?php if (!empty($active_agendas)) : ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3">
                        <div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> AGENDA TERDEKAT</div>
                            <div class="card-body p-2">
                                <ul class="list-group list-group-flush small">
                                    <?php foreach ($active_agendas as $agenda) : ?>
                                        <li class="list-group-item p-2">
                                            <strong class="text-primary"><?= htmlspecialchars($agenda->title) ?></strong><br>
                                            <span class="text-xs text-muted">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?= date('d M Y H:i', strtotime($agenda->start_date)) ?> s.d. <?= date('d M Y H:i', strtotime($agenda->end_date)) ?>
                                            </span>
                                            <p class="m-0 text-muted"><?= htmlspecialchars($agenda->description) ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                    </div>
                    <?php endif; ?>

    </div>

    <hr class="border-slate-200 my-8">
    <h5 class="text-lg font-bold text-slate-700 mb-6"><i class="fas fa-th-large text-indigo-500 mr-2"></i> Jelajahi Fitur Aplikasi</h5>
    <!-- APP HUB: Menu Pintasan -->
    <div class="mb-8 bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100">
        <h6 class="text-sm font-bold text-slate-800 mb-2 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-th-large text-indigo-500"></i> Menu Pintasan
        </h6>
        
        <?php 
        $hub_categories = [
            'Akademik & E-Learning' => [
                ['title' => 'Jadwal Mengajar', 'icon' => 'fas fa-calendar-alt', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'link' => 'jadwal_fleksibel/tutor'],
                ['title' => 'Materi', 'icon' => 'fas fa-pencil-ruler', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'link' => 'kelasmateri/materi'],
                ['title' => 'Tugas', 'icon' => 'fas fa-drafting-compass', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'link' => 'kelasmateri/tugas'],
                ['title' => 'Penilaian Harian', 'icon' => 'far fa-clipboard', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'link' => 'kelasstatus'],
                ['title' => 'Modul E-Book', 'icon' => 'fas fa-book', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'link' => 'ebooks'],
            ],
            'Evaluasi & Ujian' => [
                ['title' => 'Bank Soal', 'icon' => 'far fa-folder-open', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'link' => 'cbtbanksoal'],
                ['title' => 'Jadwal Ujian', 'icon' => 'far fa-calendar-alt', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'link' => 'cbtjadwal'],
                ['title' => 'Hasil Ujian Siswa', 'icon' => 'fas fa-file-alt', 'color' => 'text-cyan-500', 'bg' => 'bg-cyan-50', 'link' => 'cbtnilai'],
            ],
            'Administrasi Tutor' => [
                ['title' => 'Input Absensi', 'icon' => 'fas fa-user-check', 'color' => 'text-teal-500', 'bg' => 'bg-teal-50', 'link' => 'absensi'],
                ['title' => 'Laporan Honor', 'icon' => 'fas fa-hand-holding-usd', 'color' => 'text-green-600', 'bg' => 'bg-green-50', 'link' => 'honor'],
            ],
            'Komunikasi & Sistem' => [
                ['title' => 'Pengumuman', 'icon' => 'fas fa-bullhorn', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'link' => 'pengumuman'],
                ['title' => 'Chat Internal', 'icon' => 'fas fa-comments', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'link' => 'chat'],
                ['title' => 'Logout', 'icon' => 'fas fa-sign-out-alt', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'link' => 'logout', 'is_danger' => true],
            ]
        ];

        foreach ($hub_categories as $category_name => $apps) : 
        ?>
            <h6 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 mt-6"><?= $category_name ?></h6>
            <div class="grid grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3 lg:gap-4">
                <?php 
                foreach ($apps as $app) : 
                    $bg_hover = isset($app['is_danger']) ? 'hover:bg-red-50 border-red-100 hover:border-red-200' : 'hover:bg-slate-50 border-transparent hover:border-slate-200';
                ?>
                <a href="<?= base_url($app['link']) ?>" class="flex flex-col items-center justify-center bg-white border rounded-xl p-2 md:p-3 shadow-sm transition-all duration-300 <?= $bg_hover ?> hover:-translate-y-1 group">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl <?= $app['bg'] ?> group-hover:scale-110 transition-transform duration-300 mb-2">
                        <i class="<?= $app['icon'] ?> text-xl md:text-2xl <?= $app['color'] ?>"></i>
                    </div>
                    <span class="text-[10px] md:text-xs font-semibold text-slate-600 text-center leading-tight <?php if(isset($app['is_danger'])) echo 'text-red-600'; ?>"><?= $app['title'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modals -->
<div class="modal fade" id="komentarModal" tabindex="-1" role="dialog" aria-labelledby="komentarLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="komentarLabel">Tulis Komentar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid img-circle img-sm" src="<?= base_url('assets/img/siswa.png') ?>" alt="Alt Text">
                <div class="img-push">
                    <?= form_open('create', array('id' => 'komentar')) ?>
                    <input type="hidden" id="id-post" name="id_post" value="">
                    <div class="input-group">
                        <input type="text" name="text" placeholder="Tulis komentar ..."
                               class="form-control form-control-sm" required>
                        <span class="input-group-append">
                                <button type="submit" class="btn btn-success btn-sm">Komentari</button>
                            </span>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="balasanModal" tabindex="-1" role="dialog" aria-labelledby="balasanLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balasanLabel">Tulis Balasan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid img-circle img-sm" src="<?= base_url('assets/img/siswa.png') ?>" alt="Alt Text">
                <div class="img-push">
                    <?= form_open('create', array('id' => 'balasan')) ?>
                    <input type="hidden" id="id-comment" name="id_comment" value="">
                    <div class="input-group">
                        <input type="text" name="text" placeholder="Tulis balasan ...."
                               class="form-control form-control-sm" required>
                        <span class="input-group-append">
                                <button type="submit" class="btn btn-success btn-sm">Balas</button>
                            </span>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="<?= base_url() ?>/assets/app/js/jquery.rowspanizer.js"></script>
<script>
    let timerTokenView;
    let timerTokenRemaining, timerTokenOnGoing;
    var halaman = 0;
    var idGuru = "<?=$guru->id_guru?>";

    function createTime(d) {
        var date = new Date(d);

        var jam = date.getHours();
        var menit = date.getMinutes();
        var sJam;
        var sMenit;

        if (jam < 10) sJam = '0' + jam;
        else sJam = '' + jam;

        if (menit < 10) sMenit = '0' + menit;
        else sMenit = '' + menit;

        var hari = daysdifference(d);
        var time;

        if (hari === 0) {
            time = sJam + ':' + sMenit;
        } else if (hari === 1) {
            time = 'kemarin ' + sJam + ':' + sMenit;
        } else {
            time = jQuery.timeago(d) + ', ' + sJam + ':' + sMenit;
        }
        return time;
    }

    function daysdifference(last) {
        var startDay = new Date(last);
        var endDay = new Date();

        var millisBetween = startDay.getTime() - endDay.getTime();
        var days = millisBetween / (1000 * 3600 * 24);

        return Math.round(Math.abs(days));
    }

    function addComments(id, comments, append) {
        var comm = '';
        $.each(comments, function (i, v) {
            var dari, foto, avatar;
            if (v.dari == '0') {
                dari = 'Admin';
                avatar = v.foto != null ? '<img class="img-circle border" src="' + v.foto + '" alt="Img" width="40px" height="40px">' :
                    '<div class="btn-circle-sm btn-success media-left pt-1" style="width: 43px; height: 40px">A</div>'
            } else {
                if (v.dari_group == '2') {
                    dari = v.nama_guru;
                    foto = v.foto != null ? base_url + v.foto : base_url + 'assets/img/siswa.png';
                    avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="40px" height="40px">';
                } else {
                    dari = v.nama_siswa;
                    foto = v.foto_siswa != null ? base_url + v.foto_siswa : base_url + 'assets/img/siswa.png';
                    avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="40px" height="40px">';
                }
            }

            comm += '<div class="media mt-1" id="parent-reply' + v.id_comment + '">'
                + avatar +
                '    <div class="w-100 ml-2">' +
                '        <div class="media-body border pl-3 bg-light" style="border-radius: 20px">' +
                '            <span class="text-xs text-muted"><b>' + dari + '</b></span>' +
                '            <div class="comment-text pb-1">' + v.text + '</div>' +
                '        </div>' +
                '        <div class="ml-2">' +
                '            <span class="btn-sm mr-2 text-muted">' + createTime(v.tanggal) + '</span>' +
                '            <span id="trigger-reply' + v.id_comment + '" class="btn btn-sm mr-2 text-muted action-collapse" data-toggle="collapse" aria-expanded="true"' +
                '                              aria-controls="collapse-reply' + v.id_comment + '"' +
                '                              href="#collapse-reply' + v.id_comment + '"><b>' + v.jml + ' balasan</b></span>' +
                '            <span class="btn btn-sm mr-2 text-muted btn-toggle-reply"' +
                '                  data-id="' + v.id_comment + '" data-toggle="modal" data-target="#balasanModal">' +
                '                <i class="fas fa-reply"></i> <b>Balas</b></span>';
            if (v.dari_group === '2' && v.dari === idGuru) {
                comm += '            <span class="btn btn-sm text-muted" data-id="' + v.id_comment + '">' +
                    '                <i class="fa fa-trash mr-1"></i> Hapus' +
                    '            </span>';
            }
            comm += '        </div>' +
                '<div id="collapse-reply' + v.id_comment + '" class="p-2 collapse toggle-reply" data-id="' + v.id_comment + '" data-parent="#parent-reply' + v.id_comment + '">';
            if (v.jml != '0') {
                comm += '<div id="konten-reply' + v.id_comment + '"></div>' +
                    '<div id="loadmore-reply' + v.id_comment + '" onclick="getReplies(' + v.id_comment + ')" class="text-center mb-3 loadmore-reply">' +
                    '       <div class="btn btn-default">Muat balasan lainnya ...</div>' +
                    '</div>';
            }
            comm += '    <div id="loading-reply' + v.id_comment + '" class="text-center d-none">' +
                '        <div class="spinner-grow"></div>' +
                '    </div>' +
                '</div>' +
                '    </div>' +
                '</div>';
        });

        if (append) {
            $(`#konten${id}`).append(comm);
        } else {
            $(`#konten${id}`).prepend(comm);
        }

        $('.toggle-reply').on('shown.bs.collapse', function (e) {
            var konten = $(this);
            var id = konten.data('id');
            var list = $(this).find('.media').length;
            if (list === 0) $(`#loadmore-reply${id}`).click();
        });
    }

    function addReplies(id, replies, append) {
        console.log('replies', replies);
        var repl = '';
        $.each(replies, function (i, v) {
            var sudahAda = $(`.media${v.id_reply}`).length;
            if (!sudahAda) {
                var dari, foto, avatar;
                if (v.dari == '0') {
                    dari = 'Admin';
                    avatar = v.foto != null ? '<img class="img-circle border" src="' + v.foto + '" alt="Img" width="35px" height="35px">' :
                        '<div class="btn-circle-sm btn-success media-left pt-1 mr-2" style="width: 37px">A</div>';
                } else {
                    if (v.dari_group == '2') {
                        dari = v.nama_guru;
                        foto = v.foto != null ? base_url + v.foto : base_url + 'assets/img/siswa.png';
                        avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="35px" height="35px">';
                    } else {
                        dari = v.nama_siswa;
                        foto = v.foto_siswa != null ? base_url + v.foto_siswa : base_url + 'assets/img/siswa.png';
                        avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="35px" height="35px">';
                    }
                }

                repl +=
                    '<div class="media mt-1 media' + v.id_reply + '">'
                    + avatar +
                    '    <div class="w-100">' +
                    '        <div class="media-body border pl-3" style="border-radius: 17px; background-color: #dee2e6">' +
                    '            <span class="text-xs text-muted"><b>' + dari + '</b></span>' +
                    '            <div class="comment-text">' + v.text +
                    '            </div>' +
                    '        </div>' +
                    '        <div class="ml-2">' +
                    '            <small class="btn-sm mr-2 text-muted">' + createTime(v.tanggal) + '</small>';
                if (v.dari_group === '2' && v.dari === idGuru) {
                    repl += '            <span class="btn btn-sm text-muted" data-id="' + v.id_reply + '">' +
                        '                <i class="fa fa-trash mr-1"></i> Hapus' +
                        '            </span>';
                }
                repl += '        </div>' +
                    '    </div>' +
                    '</div>';
            }
        });

        if (append) {
            $(`#konten-reply${id}`).append(repl);
        } else {
            $(`#konten-reply${id}`).prepend(repl);
        }
        console.log('added', 'reply' + id);
    }

    function getComments(id) {
        $(`#loading${id}`).removeClass('d-none');
        $(`#loadmore${id}`).addClass('d-none');
        var $count = $(`#loadmore${id}`), page = $count.data('count');
        if (!page) page = 0;

        setTimeout(function () {
            $.ajax({
                url: base_url + "pengumuman/getcomment/" + id + "/" + page,
                type: "GET",
                success: function (response) {
                    //console.log('page', page);
                    console.log("result", response);
                    page += 1;
                    currentPage = page;
                    $count.data('count', page);

                    if (response.length === 5) {
                        $(`#loadmore${id}`).removeClass('d-none');
                    }
                    $(`#loading${id}`).addClass('d-none');
                    addComments(id, response, true)
                }, error: function (xhr, status, error) {
                    console.log("error", xhr.responseText);
                }
            });
        }, 500);
    }

    function getReplies(id) {
        $(`#loading-reply${id}`).removeClass('d-none');
        $(`#loadmore-reply${id}`).addClass('d-none');
        var $count = $(`#loadmore-reply${id}`), page = $count.data('count');
        if (!page) page = 0;

        setTimeout(function () {
            $.ajax({
                url: base_url + "pengumuman/getreplies/" + id + "/" + page,
                type: "GET",
                success: function (response) {
                    //console.log('page', page);
                    console.log("result", response);
                    page += 1;
                    currentPage = page;
                    $count.data('count', page);

                    //n >= start && n <= end
                    if (response.length === 5) {
                        $(`#loadmore-reply${id}`).removeClass('d-none');
                    }
                    $(`#loading-reply${id}`).addClass('d-none');
                    addReplies(id, response, true)
                }, error: function (xhr, status, error) {
                    console.log("error", xhr.responseText);
                }
            });
        }, 500);
    }

    function addPosts(response) {
        var card = '';

        if (response.length > 0) {
            $.each(response, function (i, v) {
                var dari, foto, avatar;
                if (v.dari == '0') {
                    dari = 'Admin';
                    avatar = v.foto != null ? '<img class="img-circle border" src="' + v.foto + '" alt="Img" width="50px" height="50px">' :
                        '<div class="btn-circle btn-success media-left pt-1">A</div>';
                } else {
                    if (v.dari_group == '2') {
                        dari = v.nama_guru;
                        foto = v.foto != null ? base_url + v.foto : base_url + 'assets/img/siswa.png';
                        avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="50px" height="50px">';
                    } else {
                        dari = v.nama_siswa;
                        foto = v.foto_siswa != null ? base_url + v.foto_siswa : base_url + 'assets/img/siswa.png';
                        avatar = '<img class="img-circle border" src="' + foto + '" alt="Img" width="50px" height="50px">';
                    }
                }

                card += '<div class="card">' +
                    '    <div class="card-body" id="parent' + v.id_post + '">' +
                    '        <div class="media">' +
                    avatar +
                    '                <div class="media-body ml-3">' +
                    '                    <span class="font-weight-bold"><b>' + dari + '</b></span>' +
                    '                    <br/>' +
                    '                    <span class="text-gray">' + createTime(v.tanggal) + '</span>' +
                    '                </div>' +
                    '        </div>' +
                    '        <div class="mt-2">' + v.text + '</div>' +
                    '        <div class="text-muted">' +
                    '            <button type="button" class="btn btn-default btn-sm mr-2 btn-toggle"' +
                    '                    data-id="' + v.id_post + '" data-toggle="modal"' +
                    '                    data-target="#komentarModal"><i class="fas fa-reply mr-1"></i> Tulis komentar' +
                    '            </button>' +
                    '            <button type="button" id="trigger' + v.id_post + '" class="btn btn-default btn-sm mr-2 action-collapse"' +
                    '                    data-toggle="collapse" aria-expanded="true"' +
                    '                    aria-controls="collapse-' + v.id_post + '"' +
                    '                    href="#collapse-' + v.id_post + '">' +
                    '                <i class="fa fa-commenting-o mr-1"></i>' + v.jml + ' komentar' +
                    '            </button>';
                if (v.dari_group === '2' && v.dari === idGuru) {
                    card += '            <button type="button" class="btn btn-default btn-sm" data-id="' + v.id_post + '">' +
                        '                <i class="fa fa-trash mr-1"></i> Hapus' +
                        '            </button>';
                }
                card += '        </div>' +
                    '    </div>' +
                    '    <div id="collapse-' + v.id_post + '" class="p-2 collapse toggle-comment"' +
                    '         data-id="' + v.id_post + '" data-parent="#parent' + v.id_post + '">' +
                    '        <hr class="m-0">' +
                    '        <div id="konten' + v.id_post + '" class="p-4">' +
                    '        </div>' +
                    '        <div id="loading' + v.id_post + '" class="text-center d-none">' +
                    '            <div class="spinner-grow"></div>' +
                    '        </div>';
                if (v.jml == '0') {
                    card += '<div class="text-center">Tidak ada komentar</div>';
                } else {
                    card += '<div id="loadmore' + v.id_post + '"' +
                        '     onclick="getComments(' + v.id_post + ')"' +
                        '     class="text-center mt-4 loadmore">' +
                        '    <div class="btn btn-default">Muat komentar lainnya ...</div>' +
                        '</div>';
                }
                card += '</div>' +
                    '</div>';
            })
        } else {
            $('#panel-pengumuman-wrapper').addClass('hidden');
        }

        $('#pengumuman').html(card);

        $('.toggle-comment').on('shown.bs.collapse', function (e) {
            var konten = $(this);
            var id = konten.data('id');
            var list = $(this).find('.media').length;
            if (list === 0) $(`#loadmore${id}`).click();
        });

        $('#komentarModal').on('show.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $("#id-post").val(id);

            var isVisible = $(`#collapse-${id}`).hasClass('show');
            if (!isVisible) {
                $(`#trigger${id}`).click();
            }
        });

        $('#balasanModal').on('show.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $("#id-comment").val(id);

            var isVisible = $(`#collapse-reply${id}`).hasClass('show');
            if (!isVisible) {
                $(`#trigger-reply${id}`).click();
            }
        });

        $('#komentar').on('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            console.log("data", $(this).serialize());
            var id = $(this).find('input[name=id_post]').val();

            $.ajax({
                url: base_url + "pengumuman/savekomentar",
                data: $(this).serialize(),
                method: 'POST',
                dataType: "JSON",
                success: function (response) {
                    console.log("result", response);
                    $('#komentarModal').modal('hide').data('bs.modal', null);
                    $('#komentarModal').on('hidden', function () {
                        $(this).data('modal', null);
                    });
                    addComments(id, response, false)
                    //window.location.href = base_url + 'pengumuman';
                },
                error: function (xhr, status, error) {
                    $('#komentarModal').modal('hide').data('bs.modal', null);
                    $('#komentarModal').on('hidden', function () {
                        $(this).data('modal', null);
                    });
                    showDangerToast('Error, komentar tidak terkirim');
                }
            });
        });

        $('#balasan').on('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            console.log("data", $(this).serialize());
            var id = $(this).find('input[name=id_comment]').val();

            $.ajax({
                url: base_url + "pengumuman/savebalasan",
                data: $(this).serialize(),
                method: 'POST',
                dataType: "JSON",
                success: function (response) {
                    console.log("result", response);
                    $('#balasanModal').modal('hide').data('bs.modal', null);
                    $('#balasanModal').on('hidden', function () {
                        $(this).data('modal', null);
                    });
                    //window.location.href = base_url + 'pengumuman';
                    addReplies(id, response, false)
                },
                error: function (xhr, status, error) {
                    $('#balasanModal').modal('hide').data('bs.modal', null);
                    $('#balasanModal').on('hidden', function () {
                        $(this).data('modal', null);
                    });
                    showDangerToast('Error, balasan tidak terkirim');
                }
            });
        });

    }

    function getPosts() {
        $(`#loading-post`).removeClass('d-none');
        $(`#loadmore-post`).addClass('d-none');

        setTimeout(function () {
            $.ajax({
                url: base_url + "pengumuman/getpost/" + halaman,
                type: "GET",
                success: function (response) {
                    console.log("result", response);
                    halaman += 1;

                    if (response.length === 5) {
                        $(`#loadmore-post`).removeClass('d-none');
                    }
                    $(`#loading-post`).addClass('d-none');
                    addPosts(response)
                }, error: function (xhr, status, error) {
                    console.log("error", xhr.responseText);
                }
            });
        }, 500);
    }

    $(document).ready(function () {
        $("#tbl-penilaian").rowspanizer({
            columns: [0, 1, 2],
            vertical_align: "middle"
        });

        var colorBg = ['success', 'success', 'secondary', 'primary', 'warning', 'danger',
            'primary', "warning", "primary", "success", "primary", "success", "warning"
        ];

        function load_log() {
            $.ajax({
                url: base_url + "dashboard/getlogsiswa/10",
                method: "GET",
                success: function (data) {
                    //console.log(data);
                    var ul = '<ul class="products-list product-list-in-card pl-2 pr-2">';
                    $.each(data, function (key, value) {
                        var nama = value.id_group === '1' ? value.first_name : value.first_name + ' ' + value.last_name; //value.id_group === '1' ? value.name : (value.id_group === '2' ? value.nama_guru : value.nama);
                        var clr = colorBg[value.log_type];
                        var tgl = formatTanggal(value.log_time);//new Date('02/12/2018');
                        ul += '  <li class="item">' +
                            '    <div class="media" style="line-height: 1">' +
                            '      <button class="btn btn-circle-sm btn-' + clr + ' media-left">' +
                            nama.charAt(0).toUpperCase() +
                            '      </button>' +
                            '      <div class="media-body ml-2">' +
                            '        <span class="float-right text-xs text-muted">' + tgl + '</span>' +
                            '        <span>' + nama + '</span>' +
                            '        <br />' +
                            '        <span class="text-' + clr + ' text-sm">' + value.log_desc + '</span class="product-description">' +
                            '      </div>' +
                            '    </div>' +
                            '  </li>';

                    });
                    ul += '</ul>';
                    $('#log-list').html(ul);
                }
            });
        }

        load_log();

        getPosts();

        getToken(function (result) {
            getGlobalToken();
        });

        $('#refresh-token').click(function () {
            getToken(function (result) {
                getGlobalToken();
            });
        });
    });

    function getGlobalToken() {
        if (globalToken != null) {
            const viewToken = $('#token-view');
            if (viewToken.length) viewToken.text(globalToken.token);
            if (globalToken.auto == '1' && adaJadwalUjian != '0') {
                $('#refresh-token').removeClass('d-none')
            }
        }
    }

    // ==========================================================
    // ACTIVITY FEED - GURU
    // Polling AJAX setiap 30 detik ke GET notifikasi/guru
    // ==========================================================
    var feedInterval = null;
    var feedCollapsed = false;

    function renderFeedItem(item) {
        var colorClass = 'feed-color-' + (item.color || 'primary');
        var readClass  = item.is_read ? 'is-read' : '';
        var href       = item.url ? base_url + item.url : 'javascript:void(0)';
        var idAttr     = item.id ? 'data-notif-id="' + item.id + '"' : '';

        return '<a href="' + href + '" class="feed-item ' + colorClass + ' ' + readClass + '" ' + idAttr + '>' +
            '<div class="feed-icon">' + (item.icon || '<i class="fas fa-bell text-slate-400"></i>') + '</div>' +
            '<div class="feed-content">' +
                '<div class="feed-title">' + (item.title || '') + '</div>' +
                (item.body ? '<p class="feed-body-text">' + item.body + '</p>' : '') +
                (item.age_label ? '<div class="feed-age">' + item.age_label + '</div>' : '') +
            '</div>' +
            '<i class="fas fa-chevron-right feed-arrow"></i>' +
        '</a>';
    }

    function loadFeedGuru() {
        $.ajax({
            url: base_url + 'dashboard/get_aktivitas_ajax',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#feed-loading').addClass('hidden');

                if (!res || !res.status) return;

                var items = res.data;
                var unread = items.length;

                // Update timestamp
                var now = new Date();
                $('#feed-time').text('Update: ' + now.getHours() + ':' + (now.getMinutes()<10?'0':'') + now.getMinutes());

                if (items.length === 0) {
                    $('#feed-items').addClass('hidden');
                    $('#feed-empty').removeClass('hidden');
                } else {
                    $('#feed-empty').addClass('hidden');
                    var html = '';
                      items.forEach(function(item) { 
                          var title = '', body = '', icon = '', url = '';
                          if (item.tipe === 'tugas') {
                              title = 'Tugas: ' + item.judul;
                              body = item.nama_siswa + ' mengumpulkan tugas.';
                              icon = '<i class="fas fa-book-open text-blue-500"></i>';
                              url = 'kelasstatus?id_materi=' + (item.id_referensi || '') + '&id_mapel=' + (item.id_mapel || '') + '&id_siswa=' + (item.id_siswa || '') + '&id_kelas=' + (item.id_kelas || '');
                          } else if (item.tipe === 'materi') {
                              title = 'Materi Selesai: ' + item.judul;
                              body = item.nama_siswa + ' telah menyelesaikan materi/catatan.';
                              icon = '<i class="fas fa-book-reader text-emerald-500"></i>';
                              url = 'materi/nilai/' + item.id_referensi;
                          } else if (item.tipe === 'ujian') {
                              title = 'Ujian: ' + item.judul;
                              body = item.nama_siswa + ' butuh koreksi esai.';
                              icon = '<i class="fas fa-check-circle text-green-500"></i>';
                              url = 'cbtnilai';
                          } else if (item.tipe === 'chat') {
                              title = item.judul;
                              body = 'Pesan dari: ' + item.nama_siswa;
                              icon = '<i class="fas fa-comments text-indigo-500"></i>';
                              url = 'chat?user=' + (item.id_referensi || '');
                          }
                          var feedItem = {
                              title: title,
                              body: body,
                              icon: icon,
                              color: 'primary',
                              url: url,
                              age_label: item.waktu
                          };
                          html += renderFeedItem(feedItem); 
                      });
                    $('#feed-items').html(html).removeClass('hidden');
                }
            },
            error: function() {
                $('#feed-loading').addClass('hidden');
                $('#feed-items').html('<p class="text-muted small text-center py-2">Gagal memuat aktivitas.</p>').removeClass('hidden');
            }
        });
    }

    // Mark All Read (dummy for now)
    $('#btn-baca-semua').on('click', function() {
        $('#feed-badge').addClass('hidden');
        $('#btn-baca-semua').addClass('hidden');
        $('#feed-items .feed-item').addClass('is-read');
    });

    // Toggle collapse
    $('#btn-toggle-feed').on('click', function() {
        feedCollapsed = !feedCollapsed;
        if (feedCollapsed) {
            $('#feed-body').slideUp(200);
            $('#feed-chevron').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $('#feed-body').slideDown(200);
            $('#feed-chevron').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    // Load pertama kali saat halaman ready
    $(document).ready(function() {
        loadFeedGuru();
        // Polling setiap 30 detik
        feedInterval = setInterval(loadFeedGuru, 30000);
    });

</script>

