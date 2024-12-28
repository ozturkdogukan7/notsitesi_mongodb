<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kayıt Ol</h3>
            </div>
            <div class="card-body">
                <?php if (session()->has('validation')): ?>
                    <div class="alert alert-danger">
                        <?= session()->get('validation')->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/register') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Şifre Tekrar</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-link">Zaten hesabınız var mı? Giriş yapın</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
