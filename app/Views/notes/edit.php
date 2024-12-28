<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Not Düzenle</h3>
            </div>
            <div class="card-body">
                <?= form_open(base_url('notes/edit/' . $note['id'])) ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık</label>
                        <input type="text" class="form-control <?= session('errors.title') ? 'is-invalid' : '' ?>" 
                               id="title" name="title" value="<?= old('title', $note['title']) ?>" required>
                        <?php if (session('errors.title')): ?>
                            <div class="invalid-feedback"><?= session('errors.title') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select <?= session('errors.category_id') ? 'is-invalid' : '' ?>" 
                                id="category_id" name="category_id" required>
                            <option value="">Kategori Seçin</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= old('category_id', $note['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session('errors.category_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.category_id') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">İçerik</label>
                        <textarea class="form-control <?= session('errors.content') ? 'is-invalid' : '' ?>" 
                                  id="content" name="content" rows="5" required><?= old('content', $note['content']) ?></textarea>
                        <?php if (session('errors.content')): ?>
                            <div class="invalid-feedback"><?= session('errors.content') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_private" name="is_private" value="1" 
                                   <?= old('is_private', $note['is_private']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_private">Özel Not</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                        <?= anchor(base_url('notes'), 'İptal', ['class' => 'btn btn-secondary']) ?>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
