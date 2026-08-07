<div class="content-wrapper bg-white">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-9">
                    <h1><?= $subjudul ?></h1>
                </div>
                <div class="col-3">
                    <button onclick="window.history.back();" type="button" class="btn btn-sm btn-danger float-right">
                        <i class="fas fa-arrow-circle-left"></i><span
                                class="d-none d-sm-inline-block ml-1">Kembali</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="col-lg-12 p-0">
                <div class="alert alert-danger shadow align-content-center" role="alert">
                    <strong>Catatan!</strong> untuk import data dari file excel/word, silahkan download templatenya
                    terlebih dahulu.
                </div>
            </div>

            <div class="card my-shadow mb-4">
                <div class="card-header">
                    <h6 class="card-title"><b>Upload Soal <?= $bank->nama_mapel . " kelas " . $bank->bank_level ?></b>
                    </h6>
                    <input type="hidden" name="bank_id" id="formInput" class="form-control">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('uploads/import/format/format_soal_akm.docx') ?>"
                               class="btn-success btn mb-1 w-100"
                               download="Template Soal <?= $bank->nama_mapel ?> <?= $bank->bank_kode ?>">
                                <i class="fas fa-download"></i><span class="ml-2">Download Template</span>
                            </a>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div class="row">
                                <div class="col-8">
                                    <?= form_open_multipart('', array('id' => 'formPreviewWord')); ?>
                                    <div class="custom-file">
                                        <input type="file" name="upload_file" class="custom-file-input input-sm"
                                               id="upload-word" accept=".doc, .docx">
                                        <label class="custom-file-label" for="upload-word">Upload Soal</label>
                                    </div>
                                    <?= form_close(); ?>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-primary w-100" onclick="getData()">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="file-preview" class="table-responsive"
                         data-id="<?= $this->security->get_csrf_hash() ?>"
                         data-name="<?= $this->security->get_csrf_token_name() ?>">
                        <div class="alert alert-default-info align-content-center" role="alert">
                            Sebelum upload, pastikan anda telah mengisi format yang telah disediakan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= form_open('create', array('id' => 'grouping')) ?>
<div id="input-soal-word" class="d-none"></div>
<?= form_close() ?>

<script type="text/javascript"
        src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson-cell.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson-row.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson.js"></script>
<script src="<?= base_url() ?>/assets/app/js/mammoth.browser.min.js"></script>
<script>
    var bank_id = '<?= $bank->id_bank ?>';
    const jenjang = '<?= $setting->jenjang ?>';
    $(function () {
        bsCustomFileInput.init();
    });

    var pg;
    var essai;
    var filename = '';
    function normalizeImageUrls(htmlStr) {
        if (!htmlStr) return '';
        return htmlStr.replace(/src=["']([^"']+)["']/gi, function(match, srcVal) {
            var cleanSrc = srcVal.replace(/\\/g, '/');
            if (cleanSrc.startsWith('http') || cleanSrc.startsWith('data:')) {
                return 'src="' + cleanSrc + '"';
            }
            var uploadsMatch = cleanSrc.match(/uploads\/bank_soal\/.*/i);
            if (uploadsMatch) {
                cleanSrc = uploadsMatch[0];
            } else if (cleanSrc.includes('media/')) {
                cleanSrc = 'uploads/bank_soal/' + cleanSrc.substring(cleanSrc.indexOf('media/'));
            } else if (cleanSrc.match(/image\d+\.(png|jpg|jpeg|gif|wmf|emf|svg)/i)) {
                var filenameMatch = cleanSrc.match(/image\d+\.(png|jpg|jpeg|gif|wmf|emf|svg)/i)[0];
                cleanSrc = 'uploads/bank_soal/media/' + filenameMatch;
            } else {
                cleanSrc = 'uploads/bank_soal/media/' + cleanSrc.replace(/^[\.\/]+/, '');
            }
            return 'src="' + base_url + cleanSrc.replace(/^\/+/, '') + '"';
        });
    }

    $(document).ready(function () {
        ajaxcsrf();

        $('#upload-word').on('change', function (e) {
            var files = e.target.files;
            if (!files || !files.length) return;

            var form = new FormData($("#formPreviewWord")[0]);
            filename = files[0].name;

            swal.fire({
                title: "Mempersiapkan upload",
                text: "Mengekstrak soal & konversi rumus...",
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                }
            });

            $.ajax({
                url: base_url + 'cbtbanksoal/previewword/' + bank_id,
                type: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (typeof res === 'string') {
                        try { res = JSON.parse(res); } catch(err) {}
                    }
                    if (res && res.status) {
                        $('#file-preview').html(normalizeImageUrls(res.html));
                        setTimeout(function () {
                            formatPreviewTables('#file-preview');
                        }, 300);
                    } else {
                        console.warn("Pandoc failed, using Mammoth JS fallback:", res ? res.message : '');
                        parseWordDocxFile(files, '#file-preview');
                    }
                },
                error: function (xhr, status, error) {
                    console.warn("AJAX Pandoc error, using Mammoth JS fallback:", error);
                    parseWordDocxFile(files, '#file-preview');
                }
            });
        });
    });

    function formatPreviewTables(showDiv) {
        // Move all tables to root of showDiv and remove instruction text/lists
        var $allTables = $(showDiv).find('table');
        if ($allTables.length > 0) {
            $(showDiv).empty().append($allTables);
        }
        $(showDiv).children().not("table").remove();

        // Normalize image URLs in preview container so browser loads them directly from base_url without 404
        $(showDiv).find('img').each(function () {
            var src = $(this).attr('src');
            if (src && !src.startsWith('http') && !src.startsWith('data:') && !src.startsWith('/')) {
                $(this).attr('src', base_url + src.replace(/^[\.\/]+/, ''));
            }
        });

        $(showDiv).find('table').each(function () {
            // Convert any Pandoc <th> to <td> and unwrap <thead> / <tbody>
            $(this).find('th').each(function () {
                $(this).replaceWith('<td>' + $(this).html() + '</td>');
            });
            $(this).find('thead, tbody').each(function () {
                $(this).replaceWith($(this).html());
            });

            $(this).addClass('table table-bordered w-100 table-soal');

            const $trs = $(this).find('tr'), headers = $trs.splice(0, 1);

            var indexTr = 0;
            let hide = false;
            $trs.each(function (index, tr) {
                var cekTbl = $(tr).parent().closest('td');
                if (cekTbl.length === 0) {
                    $(this).addClass('tr-soal');
                    var $tds = $(this).find('td');
                    $tds.each(function (it, td) {
                        var tdlength = $(td).closest('tr').hasClass('tr-soal');
                        if (tdlength) $(td).addClass('td-soal');
                        $(td).find('table').addClass('table table-bordered table-inner');
                    });

                    var rows = $(this).find("td:eq(0)").attr('rowspan');
                    var soal = $(this).find("td:eq(1)").text().trim();
                    var jenis = $(this).find("td:eq(2)").text().trim();
                    if (rows != null) {
                        const imgSoal = $(this).find("td:eq(1)").find('img').length;
                        indexTr = 1;
                        if (jenis === '1' || jenis === '2') {
                            const noSoal = soal === '' && imgSoal === 0;
                            var jawaban = $(this).find("td:eq(4)").text().trim();
                            hide = jawaban === '' && noSoal;
                            if (hide) {
                                $(this).remove();
                            }
                        } else if (jenis === '3') {
                            const imgBaris = $(this).find("td:eq(4)").find('img').length;
                            const imgkolom = $(this).find("td:eq(6)").find('img').length;

                            var baris = $(this).find("td:eq(4)").text().trim();
                            var kolom = $(this).find("td:eq(6)").text().trim();

                            const noSoal = soal === '' && imgSoal === 0;
                            const noBaris = baris === '' && imgBaris === 0;
                            const noKolom = kolom === '' && imgkolom === 0;

                            hide = noBaris && noKolom && noSoal;
                            if (hide) {
                                $(this).remove();
                            }
                        }
                    } else {
                        indexTr += 1;
                        if (jenis === '4' || jenis === '5') {
                            const imgSoal = $(this).find("td:eq(1)").find('img').length;
                            var jawab = $(this).find("td:eq(3)").text().trim();
                            const noSoal = soal === '' && imgSoal === 0;
                            hide = jawab === '' && noSoal;
                            if (hide) {
                                $(this).remove();
                            }
                        }
                    }
                    if (indexTr > 1 && hide) {
                        $(this).remove();
                    }
                }
            });

            $(this).find('p').each(function () {
                var arabic = /[\u0600-\u06FF]/;
                var string = $(this).text();
                if (arabic.test(string)) {
                    $(this).css({
                        'font-size': '16pt',
                        'font-family': 'Calibri',
                        'direction': 'rtl',
                        'text-align': 'justify'
                    });
                }
            });
        });

        var attrId = document.getElementById("formInput");
        if (attrId) attrId.setAttribute("value", bank_id);

        var previewElem = document.querySelector(showDiv);
        if (previewElem && window.katex && typeof renderMathInElement === 'function') {
            try {
                renderMathInElement(previewElem, {
                    delimiters: [
                        {left: "$$", right: "$$", display: true},
                        {left: "\\[", right: "\\]", display: true},
                        {left: "\\(", right: "\\)", display: false},
                        {left: "$", right: "$", display: false}
                    ],
                    throwOnError: false
                });
            } catch(e) {
                console.warn("KaTeX renderMathInElement error:", e);
            }
        }

        swal.close();
    }

    function getCellText(htmlStr) {
        if (htmlStr === null || htmlStr === undefined) return '';
        return $('<div>' + htmlStr + '</div>').text().trim();
    }

    function hasCellImg(htmlStr) {
        if (!htmlStr) return false;
        return $('<div>' + htmlStr + '</div>').find('img').length > 0;
    }

    function parseWordDocxFile(inputElement, showDiv) {
        var files = inputElement || [];
        if (!files.length) return;

        var file = files[0];
        var reader = new FileReader();

        var options = {
            styleMap: ["u => u", "strike => del"]
        };
        reader.onloadend = function (event) {
            var arrayBuffer = reader.result;
            mammoth.convertToHtml({arrayBuffer: arrayBuffer}, options).then(function (resultObject) {
                $(showDiv).html(normalizeImageUrls(resultObject.value));
                setTimeout(function () {
                    formatPreviewTables(showDiv);
                }, 500);
            });
        };
        reader.readAsArrayBuffer(file);
    }

    function getData() {
        if (filename === '') {
            showDangerToast('Pilih File dulu');
            return;
        }
        swal.fire({
            title: "Import soal ke database",
            text: "Silahkan tunggu....",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
                swal.showLoading();
            }
        });

        var tempJenis = "0";
        var index = 0;
        var $tables = $('.table-soal');
        var tbls = {};

        let formData = new FormData($('#grouping')[0])
        formData.append("id_bank", bank_id)

        $.each($tables, function (i, row) {
            var tbl = $(this).find('.tr-soal').get().map(function (row) {
                return $(row).find('.td-soal').get().map(function (cell) {
                    return $(cell).html();
                });
            });
            if (tbl[0] === undefined) return;

            var rowSoal = 0;
            if (i === index) {
                tempJenis = getCellText(tbl[0][2]);
                rowSoal = 0;

                if (isNaN(parseInt(tempJenis))) {
                    tempJenis = getCellText(tbl[1][2]);
                    rowSoal = 1;

                    if (isNaN(parseInt(tempJenis))) {
                        tempJenis = getCellText(tbl[2][2]);
                        rowSoal = 2;
                    }
                }
            }

            var myRows = {};
            var no1 = 0;
            var no3 = 0;
            for (let j = rowSoal; j < tbl.length; j++) {
                var items = tbl[j];
                myRows[j] = {};
                if (tempJenis == "1" || tempJenis == "2") {
                    if (items.length === 6) {
                        var no = getCellText(items[0]);
                        no1 = no;
                        const imgCheck = hasCellImg(items[1]);
                        var soalCek = getCellText(items[1]);
                        var ops = getCellText(items[3]);
                        var knc = getCellText(items[5]);

                        if (soalCek != "" || imgCheck) {
                            formData.append('soal[' + tempJenis + ']' +'[' + no + '][soal]', encodeURIComponent(removeUrl(items[1])));
                            formData.append('soal[' + tempJenis + ']' + '[' + no + '][opsi][' + ops + ']', encodeURIComponent(removeUrl(items[4])));

                            if (knc != "" && knc.toUpperCase() == "V") {
                                formData.append('soal[' + tempJenis + ']' + '[' + no + '][kunci][]', ops);
                            }
                        }
                    } else {
                        myRows[j]['NO'] = no1;
                        myRows[j]['SOAL'] = '';
                        myRows[j]['JENIS'] = tempJenis;
                        var ops1 = getCellText(items[0]);

                        formData.append('soal[' + tempJenis + ']' + '[' + no1 + '][opsi][' + ops1 + ']', encodeURIComponent(removeUrl(items[1])));

                        var knc1 = getCellText(items[2]);
                        if (knc1 != "" && knc1.toUpperCase() == "V") {
                            formData.append('soal[' + tempJenis + ']' + '[' + no1 + '][kunci][]', ops1);
                        }
                    }
                } else if (tempJenis == '3') {
                    if (items.length === 9) {
                        no3 = getCellText(items[0]);

                        var kd_baris = getCellText(items[3]).toUpperCase();
                        var kd_kolom = getCellText(items[5]).toUpperCase();
                        var kd_kunci = getCellText(items[7]).toUpperCase();

                        formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][soal]', encodeURIComponent(removeUrl(items[1])));

                        const imgBrs = hasCellImg(items[4]);
                        var brs = getCellText(items[4]);
                        if (brs != "" || imgBrs) {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][baris][' + kd_baris + ']', encodeURIComponent(removeUrl(items[4])));
                        }

                        const imgKlm = hasCellImg(items[6]);
                        var klm = getCellText(items[6]);
                        if (klm != "" || imgKlm) {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][kolom][' + kd_kolom + ']', encodeURIComponent(removeUrl(items[6])));
                        }

                        var kncs = getCellText(items[8]).toUpperCase();
                        if (kncs != "") {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][kunci][' + kd_kunci + ']', kncs);
                        }
                    } else {
                        var kd_baris1 = getCellText(items[0]).toUpperCase();
                        var kd_kolom1 = getCellText(items[2]).toUpperCase();
                        var kd_kunci1 = getCellText(items[4]).toUpperCase();

                        const imgBrs1 = hasCellImg(items[1]);
                        var brs1 = getCellText(items[1]);
                        if (brs1 != "" || imgBrs1) {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][baris][' + kd_baris1 + ']', encodeURIComponent(removeUrl(items[1])));
                        }

                        const imgKlm1 = hasCellImg(items[3]);
                        var klm1 = getCellText(items[3]);
                        if (klm1 != "" || imgKlm1) {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][kolom][' + kd_kolom1 + ']', encodeURIComponent(removeUrl(items[3])));
                        }

                        var kncs1 = getCellText(items[5]).toUpperCase();
                        if (kncs1 != "") {
                            formData.append('soal[' + tempJenis + ']' + '[' + no3 + '][kunci][' + kd_kunci1 + ']', kncs1);
                        }
                    }
                } else {
                    var no4 = getCellText(items[0]);
                    formData.append('soal[' + tempJenis + ']' + '[' + no4 + '][soal]', encodeURIComponent(removeUrl(items[1])));
                    if (tempJenis == '4') {
                        formData.append('soal[' + tempJenis + ']' + '[' + no4 + '][kunci]', getCellText(items[3]));
                    } else {
                        formData.append('soal[' + tempJenis + ']' + '[' + no4 + '][kunci]', encodeURIComponent(removeUrl(items[3])));
                    }
                }
                tbls[tempJenis] = myRows;
            }
            index++;
        });

        setTimeout(function () {
            //console.log('old_json', datapost);
            console.log('form', Object.fromEntries(formData))
            sendData(formData);
        }, 500);
    }

    function sendData(datapost) {
        swal.fire({
            text: "Silahkan tunggu....",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
                swal.showLoading();
            }
        });
        $.ajax({
            url: base_url + "cbtbanksoal/uploadsoal",
            method: "POST",
            processData: false,
            contentType: false,
            data: datapost,
            success: function (result) {
                console.log("result", result);
                swal.fire({
                    title: "Sukses",
                    html: "Total isi soal: <b>" + result.total + "</b><br>Total soal diimport: <b>" + result.insert + "</b>",
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                }).then(result => {
                    if (result.value) {
                        window.history.back()
                        //window.location.href = base_url + 'cbtbanksoal';
                    }
                });
            }, error: function (xhr, status, error) {
                console.log("error", xhr.responseText);
                let errMsg = "Terjadi kesalahan server.";
                try {
                    const err = JSON.parse(xhr.responseText);
                    if (err && err.Message) errMsg = err.Message;
                    else if (err && err.message) errMsg = err.message;
                } catch(e) {
                    errMsg = xhr.statusText || "Gagal menyimpan data ke database.";
                }
                swal.fire({
                    title: "Error",
                    text: errMsg,
                    icon: "error"
                });
            }
        });
    }

    function appendFileAndSubmit(id, imageURL, fileName) {
        var datapost = $('#grouping').serialize() + "&src=" + imageURL.replace(/\+/g, "%2B") + "&name=" + fileName;
        $.ajax({
            url: base_url + "cbtbanksoal/uploadsoalimage",
            data: datapost,
            type: "POST",
            error: function (err) {
                console.error(err);
            },
            success: function (data) {
                $(id).attr('src', base_url + data.src);
                //$(id).attr('data-imgsrc', data.src);
                //console.log(data);
            },
            complete: function () {
                //console.log("Request finished.");
            }
        });
    }

    function removeUrl(str) {
        if (str === null || str === undefined) return '';
        if (typeof str !== 'string') str = String(str);
        
        var result = str.replace(base_url, '');
        result = result.replace(/src=["'](?:[a-zA-Z]:[\\\/])?([^"']+)["']/gi, function(match, srcVal) {
            var cleanSrc = srcVal.replace(/\\/g, '/');
            if (cleanSrc.startsWith('http') || cleanSrc.startsWith('data:')) {
                return 'src="' + cleanSrc + '"';
            }
            var uploadsMatch = cleanSrc.match(/uploads\/bank_soal\/.*/i);
            if (uploadsMatch) {
                cleanSrc = uploadsMatch[0];
            } else if (cleanSrc.includes('media/')) {
                cleanSrc = 'uploads/bank_soal/' + cleanSrc.substring(cleanSrc.indexOf('media/'));
            } else if (cleanSrc.match(/image\d+\.(png|jpg|jpeg|gif|wmf|emf|svg)/i)) {
                var filenameMatch = cleanSrc.match(/image\d+\.(png|jpg|jpeg|gif|wmf|emf|svg)/i)[0];
                cleanSrc = 'uploads/bank_soal/media/' + filenameMatch;
            } else {
                cleanSrc = 'uploads/bank_soal/media/' + cleanSrc.replace(/^[\.\/]+/, '');
            }
            return 'src="/' + cleanSrc.replace(/^\/+/, '') + '"';
        });
        
        return result;
    }

</script>
