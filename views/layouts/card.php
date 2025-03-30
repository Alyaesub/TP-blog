<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title"><?= htmlentities($post->getName()) ?></h5> <!-- affiche le nom du post -->
    <p class="text-muted"><?= $post->getCreatedAt()->format('d F Y') ?></p> <!-- affiche la date de création du post -->
    <p class="card-text"><?= $post->getExcerpt() ?></p> <!-- affiche le contenu du post -->
    <a href="/blog/<?= $post->getSlug() ?>-<?= $post->getId() ?>" class="btn btn-primary">Voir plus</a> <!-- genére l'url qui redirige vers chaque post -->
  </div>
</div>