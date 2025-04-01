<?php

namespace App\Model;

// fonction pour récupérer les données de la db et de créer des objets Category
class Category
{
  private $id;
  private $name;
  private $slug;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function getSlug(): ?string
  {
    return $this->slug;
  }
}
