<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($judul) ?> - Garuda Reader</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE Style -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/adminlte/dist/css/adminlte.min.css">
    <!-- Custom styling for distraction-free reading -->
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #2c3034;
            color: #f8f9fa;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }
        .reader-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .reader-navbar {
            background-color: #1a1d20;
            border-bottom: 1px solid #343a40;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .reader-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 40%;
        }
        .reader-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .reader-btn {
            background: none;
            border: none;
            color: #adb5bd;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .reader-btn:hover {
            color: #ffffff;
            background-color: #343a40;
        }
        .reader-btn:disabled {
            color: #495057;
            cursor: not-allowed;
            background: none;
        }
        .page-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            background-color: #2b3035;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #495057;
        }
        .page-input {
            width: 45px;
            background: none;
            border: none;
            border-bottom: 1px dashed #ced4da;
            color: #fff;
            text-align: center;
            font-size: 0.95rem;
            padding: 0;
        }
        .page-input:focus {
            outline: none;
            border-bottom: 1px solid #28a745;
        }
        .viewer-viewport {
            flex-grow: 1;
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            position: relative;
        }
        #pdf-canvas {
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            background-color: #fff;
            max-width: 100%;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(44,48,52,0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 999;
            transition: opacity 0.3s ease;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.1);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border-left-color: #28a745;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="reader-container">
    <!-- Reader Navbar -->
    <nav class="reader-navbar">
        <a href="<?= base_url('ebooks') ?>" class="reader-btn" title="Kembali ke Daftar">
            <i class="fas fa-arrow-left mr-2"></i> <span class="d-none d-sm-inline">Kembali</span>
        </a>
        
        <div class="reader-title" title="<?= htmlspecialchars($ebook->title) ?>">
            <?= htmlspecialchars($ebook->title) ?>
        </div>

        <div class="reader-controls">
            <!-- Zoom Controls -->
            <button class="reader-btn" id="zoom-out" title="Perkecil">
                <i class="fas fa-search-minus"></i>
            </button>
            <button class="reader-btn" id="zoom-in" title="Perbesar">
                <i class="fas fa-search-plus"></i>
            </button>
            
            <!-- Navigation -->
            <button class="reader-btn" id="prev-page" title="Halaman Sebelumnya" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="page-indicator">
                <input type="number" id="page-input" class="page-input" value="<?= $last_page ?>" min="1">
                <span>/</span>
                <span id="page-count">-</span>
            </div>

            <button class="reader-btn" id="next-page" title="Halaman Selanjutnya" disabled>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </nav>

    <!-- Viewport -->
    <div class="viewer-viewport" id="viewport">
        <div class="loading-overlay" id="loading-overlay">
            <div class="spinner"></div>
            <div>Memuat E-Book...</div>
        </div>
        <canvas id="pdf-canvas"></canvas>
    </div>
</div>

<!-- jQuery -->
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

<script>
    var pdfDoc = null,
        pageNum = parseInt(<?= $last_page ?>) || 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.3,
        canvas = document.getElementById('pdf-canvas'),
        ctx = canvas.getContext('2d'),
        ebookId = <?= $ebook->id_ebook ?>;

    // Load PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';

    // Render page function
    function renderPage(num) {
        pageRendering = true;
        $('#loading-overlay').removeClass('d-none').css('opacity', 1);

        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);

            // Wait for rendering to finish
            renderTask.promise.then(function() {
                pageRendering = false;
                $('#loading-overlay').addClass('d-none');
                
                // Update controls
                $('#prev-page').prop('disabled', num <= 1);
                $('#next-page').prop('disabled', num >= pdfDoc.numPages);
                $('#page-input').val(num);
                
                // Save progress to server
                saveProgress(num, pdfDoc.numPages);

                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });
    }

    // Queue page rendering
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    // Save reading progress to database
    function saveProgress(currentPage, totalPageCount) {
        $.ajax({
            url: '<?= base_url("ebooks/save_progress") ?>',
            method: 'POST',
            dataType: 'json',
            data: {
                ebook_id: ebookId,
                last_page: currentPage,
                total_pages: totalPageCount,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                console.log('Progress saved:', response);
            },
            error: function(err) {
                console.error('Failed to save progress:', err);
            }
        });
    }

    // Load Document
    pdfjsLib.getDocument('<?= base_url($ebook->file_path) ?>').promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        $('#page-count').text(pdfDoc.numPages);
        $('#page-input').attr('max', pdfDoc.numPages);
        
        // Ensure starting page doesn't exceed total pages
        if (pageNum > pdfDoc.numPages) {
            pageNum = pdfDoc.numPages;
        }
        
        renderPage(pageNum);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        $('#loading-overlay').html('<i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i><div>Gagal memuat dokumen PDF. Hubungi administrator.</div>');
    });

    // Control events
    $('#prev-page').on('click', function() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    });

    $('#next-page').on('click', function() {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    });

    $('#page-input').on('change', function() {
        var val = parseInt($(this).val());
        if (isNaN(val) || val < 1 || val > pdfDoc.numPages) {
            $(this).val(pageNum);
            return;
        }
        pageNum = val;
        queueRenderPage(pageNum);
    });

    // Zoom events
    $('#zoom-in').on('click', function() {
        if (scale >= 3.0) return;
        scale += 0.2;
        queueRenderPage(pageNum);
    });

    $('#zoom-out').on('click', function() {
        if (scale <= 0.6) return;
        scale -= 0.2;
        queueRenderPage(pageNum);
    });
</script>

</body>
</html>
