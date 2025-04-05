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
    // Vérifier que l'email et le mot de passe ne sont pas vide
    try {
      $pdo = ConnexionDb::getPdo();
      $userModel = new User($pdo);
      $user = $userModel->findByEmail($email);

      if (!$user) {
        $_SESSION['login_error'] = "Email non trouvé";
        header('Location: /admin/login');
        echo "Email non trouvé";
        exit();
      }

      // Vérifier que le mot de passe est correct
      // Utiliser password_verify pour comparer le mot de passe saisi avec le mot de passe haché dans la base de données

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
