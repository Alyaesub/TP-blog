  $password = 'password123';
$hash = '$2y$10$n39QYIYliV9L3sb3f0E0COWJ0GdpJGZjZzyCrqOGiV5mYz4zA3Kme';

if (password_verify($password, $hash)) {
    echo "VALID ✅";
} else {
    echo "NOPE ❌";
}
/* hash de : password123
 */$2y$10$n39QYIYliV9L3sb3f0E0COWJ0GdpJGZjZzyCrqOGiV5mYz4zA3Kme

DELETE FROM user WHERE email = 'alya@test.com';

INSERT INTO user (email, password, role)
VALUES (
  'alya@test.com',
  '$2y$12$pHrfAf.3cuYgEoPC1XZjI.jw9Y2hVgTkQumXxe4o9Yx3N346BPM5y% ',
  'admin'
);