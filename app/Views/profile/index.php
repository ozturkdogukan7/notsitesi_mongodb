<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>
                <h4 class="card-title"><?= esc($user['username']) ?></h4>
                <p class="text-muted"><?= esc($user['email']) ?></p>
                <div class="row text-center mt-4">
                    <div class="col">
                        <h5><?= $totalNotes ?></h5>
                        <small class="text-muted">Toplam Not</small>
                    </div>
                    <div class="col">
                        <h5><?= $privateNotes ?></h5>
                        <small class="text-muted">Özel Not</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('profile/change-password') ?>" class="btn btn-primary btn-block w-100">
                    <i class="fas fa-key me-1"></i>Şifre Değiştir
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Son Notlarım</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notes)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-sticky-note fa-3x mb-3"></i>
                        <p>Henüz not eklenmemiş.</p>
                        <a href="<?= base_url('notes/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Not Ekle
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach (array_slice($notes, 0, 5) as $note): ?>
                            <a href="<?= base_url('notes/view/' . $note['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($note['title']) ?></h6>
                                    <small class="text-muted">
                                        <?php 
                                        $date = new DateTime($note['created_at']);
                                        echo $date->format('d.m.Y H:i');
                                        ?>
                                    </small>
                                </div>
                                <p class="mb-1 text-muted"><?= character_limiter(strip_tags($note['content']), 100) ?></p>
                                <?php if ($note['is_private']): ?>
                                    <small class="text-primary">
                                        <i class="fas fa-lock me-1"></i>Özel Not
                                    </small>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($notes) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('notes') ?>" class="btn btn-link">Tüm Notları Görüntüle</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
