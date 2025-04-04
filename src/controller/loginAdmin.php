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
    /* $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    var_dump(password_verify($password, $hash));
    die();
    var_dump($_SESSION);
    die(); */
    try {
      $pdo = ConnexionDb::getPdo();
      $userModel = new User($pdo);
      $user = $userModel->findByEmail($email);
      /*       var_dump($user, $email, $password);
 */
      if (!$user) {
        $_SESSION['login_error'] = "Email non trouvé";
        header('Location: /admin/login');
        echo "Email non trouvé";
        exit();
      }

      if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /admin');
        echo "Mot de passe correct";
        var_dump($password);
        exit();
      } else {
        $_SESSION['login_error'] = "Mot de passe incorrect";
        header('Location: /admin/login');
        exit();
      }
    } catch (PDOException $e) {
      echo "Erreur de connexion à la base de données: " . $e->getMessage();
    }
  }
}
