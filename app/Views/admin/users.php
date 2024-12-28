<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h2>Kullanıcı Yönetimi</h2>
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
                    <th>Kullanıcı Adı</th>
                    <th>E-posta</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= $user['created_at'] ?></td>
                    <td>
                        <?php if (!$user['is_admin']): ?>
                        <a href="<?= base_url('admin/deleteUser/' . $user['id']) ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                            Sil
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
