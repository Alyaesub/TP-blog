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


    //teste pour debug le proble de de connexion !!!!!! c'était le % du hash car on est sur mac..... fin de la blague



    // $hash = '$2y$12$aTN5J/9oh7pimnku9M9NmuRjGhHeN0VrrlZnmK58llVBjbtzSeN4u';
    // $originalPassword = 'password123';

    // // 1er test
    // var_dump(password_verify($originalPassword, $hash));

    // // 2ème test
    // var_dump('Session : ', $_SESSION);

    // // 3ème test
    // var_dump('Mot de passe envoyé === mot de passe rentré dans la commande pour hasher', $password === $originalPassword);

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
      // Vérifier que le mot de passe en BDD est le même que le hash généré dans le terminal
      // var_dump('Mot de passe hashé via commande === mot de passe en BDD', $hash === $user['password']);

      if (!$user) {
        die('Bizarre ...');
        $_SESSION['login_error'] = "Email non trouvé";
        header('Location: /admin/login');
        echo "Email non trouvé";
        exit();
      }

      // dd(password_verify($password, $user['password']));

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
