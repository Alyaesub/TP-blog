<?php

namespace App\Model;

use App\Helpers\Text; // j'importe le namespace Text


// permettra de récupérer les données de la base de données et de créer des objets Post
class Post
{
  private $id;
  private $name;
  private $content;
  private $created_at;
  private $categories = [];
  private $slug;

  // fonction pour récupérer le nom du post
  public function getName(): ?string
  {
    return $this->name;
  }

  // fonction pour récupérer un extrait du contenu du post
  public function getExcerpt(): ?string
  {
    if ($this->content === null) {
      return null;
    }
    return nl2br(htmlentities(Text::excerpt($this->content, 60)));
  }

  // fonction pour récupérer le contenu du post
  public function getContent(): ?string
  {
    if ($this->content === null) {
      return null;
    }
    return nl2br(htmlentities($this->content));
  }

  // fonction pour récupérer la date de création du post
  public function getCreatedAt(): \DateTime
  {
    return new \DateTime($this->created_at);
  }

  // fonction pour récupérer l'id du post 
  public function getId(): ?int
  {
    return $this->id;
  }

  // fonction pour récupérer le slug du post
  public function getSlug(): ?string
  {
    return $this->slug;
  }
}
