<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light px-4 flex justify-between items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05); height: 70px;">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link bg-gray-50 hover:bg-gray-100 rounded-full h-10 w-10 flex items-center justify-center transition-colors" data-widget="pushmenu" href="#" role="button" style="color: var(--primary-color);">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item ml-3 hidden md:block">
            <div class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-semibold tracking-wide border border-indigo-100 flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i> TP: <?= isset($tp_active) ? $tp_active->tahun : "Belum di set" ?> &bull; Smt: <?= isset($smt_active) ? $smt_active->nama_smt : "Belum di set" ?>
            </div>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div id="live-clock" class="px-4 py-1.5 bg-white text-gray-700 rounded-full text-sm font-semibold tracking-wide border border-gray-200 shadow-sm flex items-center text-center justify-center" style="min-width: 110px;">
            </div>
        </li>
    </ul>
</nav>
