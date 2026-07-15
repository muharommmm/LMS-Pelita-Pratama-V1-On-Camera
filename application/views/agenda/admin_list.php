<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addAgendaModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Agenda
                </button>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check text-white"></i> <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-ban text-white"></i> <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="agendaTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Kegiatan / Agenda</th>
                                    <th>Target Peran</th>
                                    <th>Target Kelas</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($agendas)) : ?>
                                    <?php $no = 1; foreach ($agendas as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-primary"><?= htmlspecialchars($row->title) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($row->description) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info text-capitalize"><?= htmlspecialchars($row->target_role) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?= $row->nama_kelas ? htmlspecialchars($row->nama_kelas) : '<span class="badge badge-secondary">Semua Kelas</span>' ?>
                                            </td>
                                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($row->start_date)) ?></td>
                                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($row->end_date)) ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('agendas/delete/' . $row->id_agenda) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus agenda ini?');">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada agenda sekolah terdaftar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Agenda Modal -->
<div class="modal fade" id="addAgendaModal" tabindex="-1" role="dialog" aria-labelledby="addAgendaModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAgendaModalLabel">Tambah Agenda Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('agendas/create') ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Nama / Judul Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Tryout Akbar Semester Ganjil" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi Kegiatan</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsikan agenda..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="target_role">Target Peran <span class="text-danger">*</span></label>
                            <select name="target_role" id="target_role" class="form-control" required>
                                <option value="all">Semua Pengguna</option>
                                <option value="admin">Administrator</option>
                                <option value="guru">Tutor / Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="target_class_id">Target Kelas (Khusus Siswa)</label>
                            <select name="target_class_id" id="target_class_id" class="form-control">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                    <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Agenda</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#agendaTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "language": {
                    "url": "<?= base_url('assets/plugins/datatables/i18n/Indonesian.json') ?>"
                }
            });
        }
    });
</script>
