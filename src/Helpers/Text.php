<?php

namespace App\Helpers;

class Text
{
  public static function excerpt(string $content, int $limit = 60) /* fonction qui permet de couper les articles en fonction de la limite */
  {
    if (mb_strlen($content) <= $limit) {
      return $content;
    }
    $lastSpace = mb_strrpos(mb_substr($content, 0, $limit), ' '); /* permet de ne pas couper un mot en plein milieu */
    return substr($content, 0, $lastSpace) . '...';
  }

  public static function slugify(string $text): string
  {
    // Convertit en minuscules
    $text = mb_strtolower($text, 'UTF-8');

    // Remplace les caractères accentués par leurs équivalents sans accent
    $text = str_replace(
      ['é', 'è', 'ë', 'ê', 'à', 'â', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ÿ', 'ñ', 'ç'],
      ['e', 'e', 'e', 'e', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'y', 'n', 'c'],
      $text
    );

    // Remplace tout ce qui n'est pas une lettre ou un chiffre par un tiret
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    // Supprime les tirets en début et fin de chaîne
    $text = trim($text, '-');

    return $text;
  }
}
