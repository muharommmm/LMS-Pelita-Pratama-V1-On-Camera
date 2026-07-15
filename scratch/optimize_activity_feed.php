<?php
// 1. UPDATE MODEL
$model_file = 'C:\xampp\htdocs\garuda_cbt\application\models\Notifikasi_model.php';
$model_content = file_get_contents($model_file);

// Find getLiveTugasSiswa and inject WHERE conditions
// It currently has:
// $this->db->where('km.status', 1);
// $this->db->where('km.tgl_mulai <=', $now);

$search_model = "\$this->db->where('km.tgl_mulai <=', \$now);";
$replace_model = "\$this->db->where('km.tgl_mulai <=', \$now);
        \$this->db->where('km.created_on >=', date('Y-m-d H:i:s', strtotime('-14 days')));
        \$this->db->where('km.deadline >=', date('Y-m-d H:i:s', strtotime('-1 days')));";

$model_content = str_replace($search_model, $replace_model, $model_content);
file_put_contents($model_file, $model_content);


// 2. UPDATE CONTROLLER SISWA
$ctrl_file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php';
$ctrl_content = file_get_contents($ctrl_file);

// Add dismiss_notifikasi method before the last closing brace
$dismiss_method = <<<PHP

    public function dismiss_notifikasi() {
        if (!\$this->ion_auth->logged_in()) {
            echo json_encode(['status' => 'error']);
            return;
        }
        \$id = (int)\$this->input->post('id_notifikasi');
        if (\$id > 0) {
            // Delete notification to hide it permanently for the user
            \$this->db->where('id', \$id)->delete('dashboard_notifications');
        }
        echo json_encode(['status' => 'success']);
    }
}
PHP;

// Find the last closing brace and replace it
$ctrl_content = preg_replace('/}\s*$/', $dismiss_method, $ctrl_content);
file_put_contents($ctrl_file, $ctrl_content);


// 3. UPDATE VIEW
$view_file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\siswa\dashboard_hermony.php';
$view_content = file_get_contents($view_file);

// A. Update renderSiswaFeedItem to wrap in a div and add the dismiss button
$search_js_render = <<<JS
        return '<a href="' + href + '" class="siswa-feed-item ' + readCls + '" ' + idAttr + '>' +
            '<div class="siswa-feed-icon" style="background:' + getIconBg(item.color) + '">' + (item.icon || 'dY""') + '</div>' +
            '<div class="siswa-feed-meta">' +
                '<h6 class="siswa-feed-title">' + item.title + '</h6>' +
                '<div class="siswa-feed-body">' + (item.body || '') + '</div>' +
                '<div class="siswa-feed-time">' + (item.age_label || '') + badgeHtml + '</div>' +
            '</div>' +
        '</a>';
JS;

$replace_js_render = <<<JS
        var dismissBtn = item.id ? '<button class="absolute top-3 right-3 text-slate-300 hover:text-red-500 z-10 dismiss-notif-btn transition-colors" data-id="' + item.id + '" title="Hapus Notifikasi"><i class="fas fa-times"></i></button>' : '';
        // If there's no icon because of encoding artifact, use a generic one
        var icon = item.icon && !item.icon.includes('dY') ? item.icon : '<i class="fas fa-bell"></i>';

        return '<div class="relative siswa-feed-item-wrapper group">' + dismissBtn + '<a href="' + href + '" class="siswa-feed-item ' + readCls + '" ' + idAttr + '>' +
            '<div class="siswa-feed-icon" style="background:' + getIconBg(item.color) + '">' + icon + '</div>' +
            '<div class="siswa-feed-meta pr-6">' +
                '<h6 class="siswa-feed-title">' + item.title + '</h6>' +
                '<div class="siswa-feed-body">' + (item.body || '') + '</div>' +
                '<div class="siswa-feed-time">' + (item.age_label || '') + badgeHtml + '</div>' +
            '</div>' +
        '</a></div>';
JS;

$view_content = str_replace($search_js_render, $replace_js_render, $view_content);


// B. Add the click listener in loadSiswaFeed()
$search_js_listen = <<<JS
                container.querySelectorAll('.siswa-feed-item[data-notif-id]').forEach(function(el) {
                    el.addEventListener('click', function() {
                        var nid = this.dataset.notifId;
                        if (!nid) return;
                        fetch(baseUrl + 'notifikasi/baca', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'id=' + nid
                        });
                        this.classList.add('read');
                    });
                });
JS;

$replace_js_listen = <<<JS
                container.querySelectorAll('.siswa-feed-item[data-notif-id]').forEach(function(el) {
                    el.addEventListener('click', function() {
                        var nid = this.dataset.notifId;
                        if (!nid) return;
                        fetch(baseUrl + 'notifikasi/baca', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'id=' + nid
                        });
                        this.classList.add('read');
                    });
                });

                // Dismiss Button Event Listener
                $(container).find('.dismiss-notif-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var nid = $(this).data('id');
                    var card = $(this).closest('.siswa-feed-item-wrapper');
                    if (!nid) return;

                    $.ajax({
                        url: baseUrl + 'siswa/dismiss_notifikasi',
                        type: 'POST',
                        data: { id_notifikasi: nid },
                        success: function(res) {
                            card.fadeOut('fast', function(){ 
                                $(this).remove(); 
                                // Update badge
                                var unreadSpan = $('#siswa-feed-badge');
                                var currentUnread = parseInt(unreadSpan.text()) || 0;
                                if(currentUnread > 1) {
                                    unreadSpan.text(currentUnread - 1);
                                } else {
                                    unreadSpan.addClass('hidden');
                                }
                            });
                        }
                    });
                });
JS;

$view_content = str_replace($search_js_listen, $replace_js_listen, $view_content);
file_put_contents($view_file, $view_content);

echo "Fullstack Activity Feed optimization complete.\n";
