<?php
// Ensure $setting and $guru have the necessary data for sidebar
$CI =& get_instance();
if (!isset($setting)) {
    $CI->load->model('Dashboard_model', 'dashboard');
    $setting = $CI->dashboard->getSetting();
}
if (!isset($guru) || !isset($guru->id_jabatan) || !isset($guru->nama_guru)) {
    $CI->load->model('Dashboard_model', 'dashboard');
    $user = $CI->ion_auth->user()->row();
    $tp = $CI->dashboard->getTahunActive();
    $smt = $CI->dashboard->getSemesterActive();
    $guru = $CI->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
}
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-teal bg-white border-r border-gray-100 flex flex-col" style="box-shadow: 4px 0 24px rgba(0,0,0,0.02) !important;">
    <!-- Brand Logo -->
    <a href="<?= base_url(); ?>" class="brand-link bg-white border-b border-gray-50 flex items-center px-4" style="height: 70px;">
        <?php $logo_app = $setting->logo_kiri == null ? base_url() . 'assets/img/favicon.png' : base_url() . $setting->logo_kiri . '?t=' . time(); ?>
        <img src="<?= $logo_app ?>" alt="App Logo" class="brand-image drop-shadow-sm" style="opacity: 1; max-height: 40px; margin-left:0;">
        <span class="brand-text font-bold text-gray-800 ml-3 tracking-tight" style="font-size: 1.1rem;"><?= $setting->nama_aplikasi ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar px-3">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-4 pb-4 mb-4 flex items-center border-b border-gray-100">
            <div class="image pl-1">
                <img src="<?= $guru->foto != null ? base_url() . $guru->foto : base_url('assets/img/user.jpg') ?>"
                     class="h-12 w-12 rounded-full object-cover shadow-sm ring-2 ring-gray-50" alt="User Image">
            </div>
            <div class="info ml-3">
                <a href="#" class="block text-gray-800 font-semibold text-sm hover:text-indigo-600 transition-colors">
                    <?= $guru->nama_guru ?>
                </a>
                <span class="text-xs text-gray-500 font-medium">Pengajar</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 mb-5">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent space-y-1" data-widget="treeview" role="menu"
                id="tree-menus" data-accordion="false">
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    const page = '<?= $this->uri->segment(1)?>';
    const jabatan = '<?=$guru->id_jabatan?>';
    const pageact = '<?= $this->uri->segment(2); ?>';
    const menus = [
        {
            'header': 'HOME', 'cbt': '1',
            'menu': [
                {'name': 'Beranda', 'link': 'dashboard', 'icon': 'fas fa-desktop', 'cbt': '1'},
                {'name': 'Profile', 'link': 'guruview', 'icon': 'fas fa-user', 'cbt': '1'},
                {'name': 'Chat Internal', 'link': 'chat', 'icon': 'fas fa-comments', 'cbt': '1'},
                {'name': 'Pengumuman', 'link': 'pengumuman', 'icon': 'fas fa-bullhorn', 'cbt': '1'},
                {
                    'name': 'Wali Kelas', 'icon': 'fas fa-chart-pie', 'cbt': '1', 'wali': true,
                    'submenu': [
                        {'name': 'Siswa', 'link':"walisiswa", 'icon': 'fas fa-users'},
                        {'name': 'Struktur', 'link':"walistruktur", 'icon': 'far fa-circle'},
                        {'name': 'Catatan', 'link':"walicatatan", 'icon': 'fa fa-pencil-alt'}
                    ]
                },
                {
                    'name': 'E-Learning', 'icon': 'fas fa-chalkboard', 'cbt': '0',
                    'submenu': [
                        // {'name': "Jadwal Pelajaran", 'link': "kelasjadwal", 'icon': 'fa fa-calendar-alt'},
                        {'name': "Materi", 'link': "kelasmateri/materi", 'icon': 'fa fa-pencil-ruler'},
                        {'name': "Tugas", 'link': "kelasmateri/tugas", 'icon': 'fa fa-drafting-compass'},
                        {'name': "Jadwal Materi/Tugas", 'link': "kelasmaterijadwal", 'icon': 'fa fa-calendar-alt'},
                        {'name': 'Nilai Harian', 'link':"kelasstatus", 'icon': 'far fa-clipboard'},
                        {'name': 'Kehadiran Harian', 'link':"kelasabsensiharian", 'icon': 'fa fa-user-check'},
                        {'name': 'Kehadiran Bulanan', 'link':"kelasabsensibulanan", 'icon': 'fa fa-tasks'},
                        {'name': 'Rekap Nilai', 'link':"kelasnilai", 'icon': 'fa fa-trophy'},
                        {'name': 'Catatan Guru', 'link':"kelascatatan", 'icon': 'fa fa-pencil-alt'},
                        {'name': "E-Book Pelajaran", 'link': "ebooks", 'icon': 'fa fa-book'},
                        {'name': "Kehadiran & Absensi", 'link': "absensi", 'icon': 'fa fa-user-check'},
                        {'name': "Honorarium Saya", 'link': "honor", 'icon': 'fa fa-hand-holding-usd'},
                    ]
                },
                {
                    'name': 'Ulangan / Ujian', 'icon': 'fa fa-user-graduate', 'cbt': '1',
                    'submenu': [
                        {'name':"Bank Soal", 'link':"cbtbanksoal", 'icon': 'far fa-folder-open'},
                        {'name':"Jadwal", 'link':"cbtjadwal", 'icon': 'far fa-calendar-alt'},
                        {'name': 'Cetak', 'link':"cbtcetak", 'icon': 'fa fa-print'},
                        {'name': 'Status Siswa', 'link':"cbtstatus", 'icon': 'fa fa-user-clock'},
                        {'name': 'Hasil Ujian', 'link':"cbtnilai", 'icon': 'fa fa-file-alt'},
                        {'name': 'Analisis Soal', 'link':"cbtanalisis", 'icon': 'fa fa-chart-line'},
                        {'name': 'Rekap Nilai', 'link':"cbtrekap", 'icon': 'fas fa-trophy'},
                    ]
                },
            ]
        },
        {
            'header': 'PENILAIAN', 'cbt': '0',
            'menu': [
                {
                    'name': 'Data Rapor', 'icon': 'fas fa-chart-pie', 'cbt': '0',
                    'submenu': [
                        {'name': 'KKM dan Bobot', 'link': 'rapor/raporkkm', 'icon': 'fa fa-balance-scale-right', 'cbt': '0'},
                        {'name': 'Indikator Nilai', 'link': 'rapor/raporkikd', 'icon': 'fas fa-book', 'cbt': '0'},
                    ]
                },
                {'name': 'Input Nilai', 'link': 'rapor/rapornilai', 'icon': 'fa fa-users', 'cbt': '0'},
                {'name': 'Periksa Nilai', 'link': 'rapor/rapornilaiguru', 'icon': 'fa fa-users', 'cbt': '0', 'wali': true},
                {
                    'name': 'Input Wali Kelas', 'icon': 'fas fa-chart-pie', 'cbt': '0', 'wali': true,
                    'submenu': [
                        {'name': 'Sikap Spiritual', 'link': 'rapor/raporspiritual', 'icon': 'fas fa-book', 'cbt': '0'},
                        {'name': 'Sikap Sosial', 'link': 'rapor/raporsosial', 'icon': 'fas fa-book', 'cbt': '0'},
                        {'name': 'Prestasi', 'link': 'rapor/raporprestasi', 'icon': 'fa fa-users', 'cbt': '0'},
                        {'name': 'Kehadiran', 'link': 'rapor/raporcatatan', 'icon': 'fa fa-users', 'cbt': '0'},
                        {'name': 'Kenaikan', 'link': 'rapor/rapornaik', 'icon': 'fa fa-users', 'cbt': '0'},
                    ]
                },
            ]
        },
        {
            'header': 'CETAK', 'cbt': '0', 'wali': true,
            'menu': [
                {'name': 'Rapor PTS', 'link': 'rapor/cetakpts', 'icon': 'fas fa-book', 'cbt': '0'},
                {'name': 'Rapor Akhir', 'link': 'rapor/cetakakhir', 'icon': 'fas fa-book', 'cbt': '0'},
                {'name': 'Ledger', 'link': 'rapor/cetakleger', 'icon': 'fa fa-users', 'cbt': '0'},
                {'name': 'DKN', 'link': 'rapor/dkn', 'icon': 'fa fa-users', 'cbt': '0'},
            ]
        },
        {
            'header': 'ARSIP', 'cbt': '0', 'wali': true,
            'menu': [
                {'name': 'Arsip Rapor', 'link': 'bukurapor', 'icon': 'fas fa-university', 'cbt': '0',},
            ]
        },
        {'name': 'LOGOUT', 'link': '', 'icon': 'fas fa-sign-out-alt', 'cbt': '1'},
    ];

    const isLogin = localStorage.getItem('garudaCBT.login')
    const isCbtMode = isLogin ? isLogin === '1' : false
    let htmlMenu = '';
    menus.forEach(function (header) {
        console.log(header)
        if (isCbtMode && header.cbt === '0') {
            return
        }
        if (jabatan !== '4' && header.wali) {
            return
        }
        if (header.header) {
            htmlMenu += `<li class="nav-header text-xs font-bold text-gray-400 uppercase tracking-wider mt-4 mb-1 px-3">${header.header}</li>`;
            header.menu.forEach(function (menu) {
                if (isCbtMode && menu.cbt === '0') {
                    return
                }
                if (jabatan !== '4' && menu.wali) {
                    return
                }
                if (menu.submenu) {
                    var subs = menu.submenu.map(function(item) {
                        if (item['link'].includes('/')) {
                            return item['link'].split('/')[1]
                        } else return item['link'];
                    });
                    htmlMenu += `<li class="nav-item has-treeview ${subs.includes(pageact) || subs.includes(page) ? "menu-open" : ""}">
                    <a href="#" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-indigo-50 hover:text-indigo-600 ${subs.includes(pageact) || subs.includes(page) ? "bg-indigo-50 text-indigo-700" : "text-gray-600"}">
                        <i class="nav-icon ${menu.icon} mr-3 text-lg opacity-80"></i>
                        <p class="flex-1">${menu.name}<i class="fas fa-angle-left right text-xs"></i></p>
                    </a><ul class="nav nav-treeview ml-4 mt-1 border-l-2 border-indigo-50 pl-2">`;
                    menu.submenu.forEach(function (sub) {
                        htmlMenu += `<li class="nav-item">
                            <a href="${base_url + sub.link}"
                               class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-50 hover:text-indigo-600 ${page+'/'+pageact === sub.link || page === sub.link ? "text-indigo-600 font-semibold" : "text-gray-500"}">
                                <i class="${sub.icon} nav-icon mr-3 text-sm opacity-70"></i>
                                <p>${sub.name}</p>
                            </a>
                        </li>`;
                    })
                    htmlMenu += `</ul></li>`;
                } else {
                    htmlMenu += `<li class="nav-item"><a href="${base_url + menu.link}"
                       class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-indigo-50 hover:text-indigo-600 ${page === menu.link ? "bg-indigo-50 text-indigo-700" : "text-gray-600"}">
                        <i class="nav-icon ${menu.icon} mr-3 text-lg opacity-80"></i>
                        <p>${menu.name}</p>
                    </a></li>`
                }
            })
        } else {
            htmlMenu += `<hr /><li class="nav-item">
                    <a href="#" onclick="logout()" class="nav-link">
                        <i class="${header.icon} nav-icon"></i>
                        <p>${header.name}</p>
                    </a>
                </li>`;
        }
    })

    // Custom menus for Guru new features
    htmlMenu += `<li class="nav-header">FITUR BARU</li>`;
    htmlMenu += `<li class="nav-item">
        <a href="${base_url}ebooks" class="nav-link ${page === 'ebooks' ? 'active' : ''}">
            <i class="nav-icon fas fa-book"></i>
            <p>Modul E-Book</p>
        </a>
    </li>`;
    htmlMenu += `<li class="nav-item">
        <a href="${base_url}absensi" class="nav-link ${page === 'absensi' ? 'active' : ''}">
            <i class="nav-icon fas fa-user-check"></i>
            <p>Input Absensi</p>
        </a>
    </li>`;
    htmlMenu += `<li class="nav-item">
        <a href="${base_url}honor" class="nav-link ${page === 'honor' ? 'active' : ''}">
            <i class="nav-icon fas fa-hand-holding-usd"></i>
            <p>Laporan Honor</p>
        </a>
    </li>`;
    htmlMenu += `<li class="nav-item">
        <a href="${base_url}jadwal_fleksibel/tutor" class="nav-link ${page === 'jadwal_fleksibel' ? 'active' : ''}">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>Jadwal Mengajar</p>
        </a>
    </li>`;

    $('#tree-menus').html(htmlMenu)
</script>