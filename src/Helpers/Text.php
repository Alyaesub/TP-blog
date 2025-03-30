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
}
