<?php

namespace App\Controller;
//feuille de code pour la connexion administrateur controller qui gere la connexion administrateur 


use App\ConnexionDb;
use App\Model\User;
use PDOException;

class LoginAdmin
{
  public function handle()
  {
    session_start();

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
      $pdo = ConnexionDb::getPdo();
      $userModel = new User($pdo);
      $user = $userModel->findByEmail($email);

      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /admin');
        exit();
      } else {
        $_SESSION['login_error'] = "Identifiants invalides";
        header('Location: /admin/login');
        exit();
      }
    } catch (PDOException $e) {
      echo "Erreur de connexion à la base de données: " . $e->getMessage();
    }
  }
}
?>

<!-- feuille de code pour la page de connexion administrateur -->
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