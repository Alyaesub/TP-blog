<div class="card mb-3">
  <div class="card-body">
    <?php global $router; ?>
    <h5 class="card-title"><?= htmlentities($post->getName()) ?></h5> <!-- affiche le nom du post -->
    <p class="text-muted"><?= $post->getCreatedAt()->format('d F Y') ?></p> <!-- affiche la date de création du post -->
    <p class="card-text"><?= $post->getExcerpt() ?></p> <!-- affiche l'extrait du contenu du post -->
    <a href="<?= $router->url('post', [
                'slug' => $post->getSlug(),
                'id' => $post->getId()
              ]) ?>" class="btn btn-primary">Voir plus</a>
  </div>
</div>