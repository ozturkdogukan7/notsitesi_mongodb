<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h2>Not Yönetimi</h2>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-<?= session()->getFlashdata('type') ?>">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>İçerik</th>
                    <th>Kullanıcı</th>
                    <th>Kategori</th>
                    <th>Oluşturma Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?= $note['id'] ?></td>
                    <td><?= esc($note['title']) ?></td>
                    <td><?= character_limiter(esc($note['content']), 50) ?></td>
                    <td><?= esc($note['username']) ?></td>
                    <td><?= esc($note['category_name']) ?></td>
                    <td><?= $note['created_at'] ?></td>
                    <td>
                        <a href="<?= base_url('admin/deleteNote/' . $note['id']) ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Bu notu silmek istediğinizden emin misiniz?')">
                            Sil
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
