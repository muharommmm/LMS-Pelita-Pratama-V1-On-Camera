<?php
$view_file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\siswa\dashboard_hermony.php';
$content = file_get_contents($view_file);

// Fix renderSiswaFeedItem using regex to be completely safe
$pattern_render = '/function renderSiswaFeedItem\(item\)\s*{.*?return\s*\'<a href="\' \+ href \+ \'" class="siswa-feed-item \' \+ readCls \+ \'" \' \+ idAttr \+ \'>\' \+.*?\n\s*\'<\/a>\';\s*}/s';

$replacement_render = <<<JS
    function renderSiswaFeedItem(item) {
        var href   = item.url ? baseUrl + item.url : '#';
        var idAttr = item.id ? 'data-notif-id="' + item.id + '"' : '';
        var readCls = item.is_read ? 'read' : '';
        var badgeHtml = item.badge
            ? '<span class="siswa-feed-badge-item ' + getBadgeClass(item.color) + '">' + item.badge + '</span>'
            : '';

        var dismissBtn = item.id ? '<button class="absolute top-2 right-2 text-slate-300 hover:text-red-500 z-10 dismiss-notif-btn transition-colors" data-id="' + item.id + '" title="Hapus Notifikasi"><i class="fas fa-times"></i></button>' : '';

        return '<div class="relative siswa-feed-item-wrapper group">' + dismissBtn + 
        '<a href="' + href + '" class="siswa-feed-item ' + readCls + '" ' + idAttr + '>' +
            '<div class="siswa-feed-icon" style="background:' + getIconBg(item.color) + '">' + (item.icon || '🔔') + '</div>' +
            '<div class="siswa-feed-meta pr-6">' +
                '<div class="flex items-start">' +
                    '<span class="siswa-feed-title">' + (item.title || '') + '</span>' +
                    badgeHtml +
                '</div>' +
                (item.body ? '<p class="siswa-feed-body">' + item.body + '</p>' : '') +
                (item.age_label ? '<div class="siswa-feed-age">' + item.age_label + '</div>' : '') +
            '</div>' +
            '<span class="material-symbols-outlined text-sm text-gray-300 self-center ml-2">chevron_right</span>' +
        '</a></div>';
    }
JS;

$content = preg_replace($pattern_render, $replacement_render, $content);

// Replace the jQuery event listener with Vanilla JS
$search_js_listen = <<<JS
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

$replace_js_listen = <<<JS
                // Dismiss Button Event Listener
                container.querySelectorAll('.dismiss-notif-btn').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var nid = this.getAttribute('data-id');
                        var card = this.closest('.siswa-feed-item-wrapper');
                        if (!nid) return;

                        fetch(baseUrl + 'siswa/dismiss_notifikasi', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'id_notifikasi=' + nid
                        }).then(function() {
                            card.style.transition = 'opacity 0.3s ease';
                            card.style.opacity = '0';
                            setTimeout(function() {
                                card.remove();
                                var unreadSpan = document.getElementById('siswa-feed-badge');
                                var currentUnread = parseInt(unreadSpan.textContent) || 0;
                                if(currentUnread > 1) {
                                    unreadSpan.textContent = currentUnread - 1;
                                } else {
                                    unreadSpan.classList.add('hidden');
                                }
                            }, 300);
                        });
                    });
                });
JS;

$content = str_replace($search_js_listen, $replace_js_listen, $content);

file_put_contents($view_file, $content);
echo "dashboard_hermony.php JS fixed.\n";
