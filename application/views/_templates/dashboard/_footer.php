</div>
<!-- /.content-wrapper -->

<!-- Main Footer -->
<footer class="main-footer">
    <strong>GarudaCBT</strong> v.<?= APP_VERSION ?>
    <div class="float-right d-none d-sm-inline-block">
        <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
        <b>Version</b> 3.0.5
    </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

</div>

<!-- Required JS -->
<!-- v3 -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- DataTables -->
<script src="<?= base_url() ?>/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Datatables Buttons -->
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="<?= base_url() ?>/assets/plugins/pace-progress/pace.min.js"></script>
<!-- Sparkline -->
<script src="<?= base_url() ?>/assets/plugins/sparklines/sparkline.js"></script>

<!-- Bootstrap 4 -->
<script src="<?= base_url() ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?= base_url() ?>/assets/plugins/chart.js/Chart.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/chart.js/chartjs-plugin-labels.min.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url() ?>/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- moment -->
<script src="<?= base_url() ?>/assets/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/moment/moment-with-locales.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url() ?>/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url() ?>/assets/plugins/summernote/summernote-bs4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/audio/summernote-audio.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/file/summernote-file.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/gallery/dist/summernote-gallery.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/math/summernote-math.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url() ?>/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>/assets/plugins/toastr/toastr.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url() ?>/assets/plugins/select2/js/select2.full.min.js"></script>
<!-- multi select -->
<script src="<?= base_url() ?>/assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/multiselect/js/jquery.quicksearch.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="<?= base_url() ?>/assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="<?= base_url() ?>/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>

<!-- Bootstrap Switch -->
<script src="<?= base_url() ?>/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/app/js/jquery.toast.min.js"></script>

<script src="<?= base_url() ?>/assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- AdminLTE App -->
<script src="<?= base_url() ?>/assets/adminlte/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?= base_url() ?>/assets/adminlte/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url() ?>/assets/adminlte/dist/js/demo.js"></script>
<!-- /v3 -->
<!-- datetimepicker -->
<script src="<?= base_url() ?>/assets/plugins/jquery-datetimepicker/jquery.datetimepicker.full.js"></script>
<!-- TimeAgo -->
<script src="<?= base_url() ?>/assets/plugins/jquery-timeago/jquery.timeago.js" type="text/javascript"></script>
<!-- App JS -->
<script src="<?= base_url() ?>/assets/app/js/show.toast.js"></script>

<script src="<?= base_url() ?>/assets/app/js/jquery-thumbnail-cut.js"></script>

<!-- Custom JS -->
<script type="text/javascript">
    $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
        return {
            "iStart": oSettings._iDisplayStart,
            "iEnd": oSettings.fnDisplayEnd(),
            "iLength": oSettings._iDisplayLength,
            "iTotal": oSettings.fnRecordsTotal(),
            "iFilteredTotal": oSettings.fnRecordsDisplay(),
            "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
            "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
        };
    };

    function ajaxcsrf() {
        var csrfname = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfhash = '<?= $this->security->get_csrf_hash() ?>';
        var csrf = {};
        csrf[csrfname] = csrfhash;
        $.ajaxSetup({
            "data": csrf
        });
    }

    function reload_ajax() {
        table.ajax.reload();
    }

    var initDestroyTimeOutPace = function () {
        var counter = 0;

        var refreshIntervalId = setInterval(function () {
            var progress;

            if (typeof $('.pace-progress').attr('data-progress-text') !== 'undefined') {
                progress = Number($('.pace-progress').attr('data-progress-text').replace("%", ''));
            }

            if (progress === 99) {
                counter++;
            }

            if (counter > 50) {
                clearInterval(refreshIntervalId);
                Pace.stop();
            }
        }, 100);
    };
    initDestroyTimeOutPace();

    function logout() {
        swal.fire({
            title: "Logout",
            text: "Anda yakin ingin logout?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Logout!'
        }).then((result) => {
            if (result.value) {
                location.href = base_url + "logout";
            }
        });
    }

    <?php if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) : ?>
    $(document).ready(function() {
        let lastNotifCount = 0;
        let isFirstLoad = true;

        function fetchAdminNotifications() {
            $.ajax({
                url: base_url + 'dashboard/get_admin_notifications_ajax',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        const total = response.total_notif;
                        
                        // Render badge & header
                        if (total > 0) {
                            $('#admin-notif-badge').text(total).removeClass('d-none');
                            $('#admin-notif-header').text(total + ' Notifikasi Baru');
                        } else {
                            $('#admin-notif-badge').addClass('d-none');
                            $('#admin-notif-header').text('0 Notifikasi');
                        }

                        // Render list items
                        let html = '';
                        if (response.items && response.items.length > 0) {
                            response.items.forEach(function(item) {
                                const clickAttr = item.id ? `onclick="markNotifRead(${item.id}, '${item.url}')"` : `href="${item.url}"`;
                                html += `
                                    <div class="dropdown-divider"></div>
                                    <a ${item.id ? 'href="javascript:void(0)"' : ''} ${clickAttr} class="dropdown-item d-flex align-items-center py-2" style="white-space: normal;">
                                        <div class="mr-3">
                                            <div class="icon-circle">
                                                <i class="${item.icon} fa-fw"></i>
                                            </div>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div class="text-xs font-weight-bold text-dark">${item.title}</div>
                                            <div class="text-[10px] text-muted text-truncate" style="max-width: 190px;">${item.body}</div>
                                            <span class="float-right text-[9px] text-muted"><i class="far fa-clock mr-1"></i> ${item.time}</span>
                                        </div>
                                    </a>
                                `;
                            });
                        } else {
                            html = '<span class="dropdown-item text-center text-xs text-muted py-3">Tidak ada notifikasi baru</span>';
                        }
                        $('#admin-notif-list').html(html);

                        // Render ke Dashboard Panel Utama (jika sedang membuka halaman dashboard)
                        if ($('#admin-panel-notif-badge').length) {
                            if (total > 0) {
                                $('#admin-panel-notif-badge').text(total + ' Baru').removeClass('d-none');
                            } else {
                                $('#admin-panel-notif-badge').addClass('d-none');
                            }
                        }

                        if ($('#admin-panel-notif-list').length) {
                            let panelHtml = '';
                            if (response.items && response.items.length > 0) {
                                response.items.forEach(function(item) {
                                    const clickAttr = item.id ? `onclick="markNotifRead(${item.id}, '${item.url}')"` : `href="${item.url}"`;
                                    panelHtml += `
                                        <a ${item.id ? 'href="javascript:void(0)"' : ''} ${clickAttr} class="list-group-item list-group-item-action d-flex align-items-start p-3 border-bottom border-light">
                                            <div class="mr-3 mt-1">
                                                <i class="${item.icon} fa-lg"></i>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="font-weight-bold text-xs text-dark">${item.title}</span>
                                                    <span class="text-[10px] text-muted">${item.time}</span>
                                                </div>
                                                <div class="text-xs text-muted leading-relaxed">${item.body}</div>
                                            </div>
                                        </a>
                                    `;
                                });
                            } else {
                                panelHtml = `
                                    <div class="text-center py-4 text-muted small">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="mb-0">Semua aman! Tidak ada aduan atau chat pending.</p>
                                    </div>
                                `;
                            }
                            $('#admin-panel-notif-list').html(panelHtml);
                        }

                        // Munculkan popup toast jika ada penambahan notifikasi
                        if (!isFirstLoad && total > lastNotifCount) {
                            toastr.warning('Ada aduan insiden baru atau chat masuk yang memerlukan perhatian Anda!', 'Notifikasi Dashboard', {
                                "closeButton": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "timeOut": "8000"
                            });
                        }

                        lastNotifCount = total;
                        isFirstLoad = false;
                    }
                }
            });
        }

        window.markNotifRead = function(id, redirectUrl) {
            ajaxcsrf();
            $.ajax({
                url: base_url + 'dashboard/mark_admin_notif_read_ajax',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function() {
                    window.location.href = redirectUrl;
                },
                error: function() {
                    window.location.href = redirectUrl;
                }
            });
        };

        // Jalankan fetch pertama kali dan pasang interval 30 detik
        fetchAdminNotifications();
        setInterval(fetchAdminNotifications, 30000);
    });
    <?php endif; ?>

    function globalRenderMath(elem) {
        var target = elem || document.body;
        if (window.katex && typeof renderMathInElement === 'function') {
            try {
                renderMathInElement(target, {
                    delimiters: [
                        {left: "$$", right: "$$", display: true},
                        {left: "\\[", right: "\\]", display: true},
                        {left: "\\(", right: "\\)", display: false},
                        {left: "$", right: "$", display: false}
                    ],
                    throwOnError: false
                });
            } catch(e) { console.warn(e); }
        }
    }
    $(document).ready(function() {
        globalRenderMath();
        setTimeout(globalRenderMath, 500);
        setTimeout(globalRenderMath, 1500);
    });
    $(document).ajaxComplete(function() {
        setTimeout(globalRenderMath, 200);
    });
</script>

</body>

</html>
