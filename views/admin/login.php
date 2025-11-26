<!-- feuille de code pour la page de connexion administrateur -->
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<div class="container mt-5">
  <h1>Connexion administrateur</h1>
  <form method="POST" action="/admin/login">
    <div class="mb-3">
      <label for="email" class="form-label">Adresse e-mail</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Se connecter</button>
  </form>
</div>

<?php if (!empty($_SESSION['login_error'])): ?>
  <div class="alert alert-danger"><?= $_SESSION['login_error'] ?></div>
  <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>
