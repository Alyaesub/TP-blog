<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Mon Site</title>
</head>

<body class="d-flex flex-column h-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary ">
    <a href="#" class="navbar-brand">Mon site</a>
  </nav>

  <div class="container met-4">
    <?= $content ?? 'Pas de contenu disponible'; ?>
  </div>

  <footer class="bg-light py-4 footer mt-auto">
    <div class="container">
      <!--pour le debug on rajoute le compteur-->
      <?php if (defined('DEBUG_TIME')): ?>
        page généré en <?= round(1000 * (microtime(true) - DEBUG_TIME)) ?> ms
      <?php endif ?>
    </div>
  </footer>
</body>

</html>