<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-plus-circle me-2"></i>Yeni Not Ekle</h3>
            </div>
            <div class="card-body">
                <?php if (session()->has('validation')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session('validation')->listErrors() ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('notes/create') ?>" method="post" id="noteForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label"><i class="fas fa-heading me-2"></i>Başlık</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label"><i class="fas fa-folder me-2"></i>Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Kategori Seçin</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label"><i class="fas fa-file-alt me-2"></i>İçerik</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?= old('content') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_private" name="is_private" value="1" <?= old('is_private') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_private">
                                <i class="fas fa-lock me-2"></i>Özel Not
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Kaydet
                        </button>
                        <a href="<?= base_url('notes') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('noteForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Kaydediliyor...';
});
</script>
<?= $this->endSection() ?>
