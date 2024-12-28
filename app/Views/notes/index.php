
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <?php if (session()->has('message')): ?>
        <div class="alert alert-<?= session('type') ?> alert-dismissible fade show">
            <i class="fas fa-<?= session('type') === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= session('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-sticky-note me-2"></i>Notlarım</h2>
        </div>
        <div class="col text-end">
            <a href="<?= base_url('notes/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Yeni Not
            </a>
        </div>
    </div>

    <?php if (empty($notes)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Henüz not eklenmemiş.
            <a href="<?= base_url('notes/create') ?>" class="alert-link">Hemen bir not ekleyin!</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($notes as $note): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center <?= $note['is_private'] ? 'bg-warning bg-opacity-10' : '' ?>">
                            <h5 class="card-title mb-0 text-truncate" title="<?= esc($note['title']) ?>">
                                <?= esc($note['title']) ?>
                            </h5>
                            <?php if ($note['is_private']): ?>
                                <i class="fas fa-lock text-warning" title="Özel Not"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <p class="card-text" style="min-height: 4.5rem;">
                                <?= character_limiter(esc($note['content']), 100) ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-folder me-1"></i>
                                    <?= esc($note['category_name'] ?? 'Kategorisiz') ?>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100">
                                <a href="<?= base_url('notes/edit/' . $note['id']) ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete(<?= $note['id'] ?>, '<?= esc($note['title']) ?>')">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="showComments(<?= $note['id'] ?>)">
                                    <i class="fas fa-comment"></i> Yorum Yap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Yorum Modalı -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Yorumlar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="comments-list" class="mb-3">
                    <!-- Yorumlar buraya yüklenecek -->
                </div>
                <form id="comment-form">
                    <input type="hidden" id="note_id" name="note_id">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Yorum</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Yorum Ekle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// CSRF token ayarı
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="<?= csrf_token() ?>"]').attr('content')
    }
});

function confirmDelete(id, title) {
    if (confirm(`"${title}" notunu silmek istediğinizden emin misiniz?`)) {
        window.location.href = `<?= base_url('notes/delete/') ?>/${id}`;
    }
}

function showComments(noteId) {
    $('#note_id').val(noteId);
    $('#commentModal').modal('show');
    loadComments(noteId);
}

function loadComments(noteId) {
    $.get(`<?= base_url('notes/getComments') ?>/${noteId}`, function(response) {
        if (response.status === 'success') {
            let html = '';
            response.data.forEach(comment => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">${comment.username}</h6>
                            <p class="card-text">${comment.comment}</p>
                            <small class="text-muted">${comment.created_at}</small>
                        </div>
                    </div>
                `;
            });
            $('#comments-list').html(html || '<p>Henüz yorum yapılmamış.</p>');
        } else {
            $('#comments-list').html('<div class="alert alert-danger">Yorumlar yüklenirken bir hata oluştu.</div>');
        }
    }).fail(function() {
        $('#comments-list').html('<div class="alert alert-danger">Yorumlar yüklenirken bir hata oluştu.</div>');
    });
}

$(document).ready(function() {
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...');
        
        let formData = $(this).serialize();
        formData += '&<?= csrf_token() ?>=<?= csrf_hash() ?>';
        
        $.ajax({
            url: '<?= base_url('notes/addComment') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    loadComments($('#note_id').val());
                    $('#comment').val('');
                    // Başarı mesajı göster
                    $('#comments-list').prepend(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Yorum başarıyla eklendi
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                } else {
                    alert(response.message || 'Yorum eklenirken bir hata oluştu.');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Yorum eklenirken bir hata oluştu.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Yorum Ekle');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
