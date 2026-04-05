<?php

declare(strict_types=1);

namespace Drupal\entreprise_api\Service;

/**
 * Interface pour le service Entreprise API.
 */
interface EntrepriseServiceInterface {

  /**
   * Retourne les informations du site.
   *
   * @param string|null $langcode
   *   Code langue ISO 2 lettres (ex: fr, en). NULL = langue par defaut.
   *
   * @return array
   *   Tableau avec name, slogan, logo_url.
   */
  public function getSiteInfo(?string $langcode = NULL): array;

  /**
   * Retourne tous les termes d'un vocabulaire, pagines.
   *
   * @param string $vid
   *   Machine name du vocabulaire.
   * @param int $page
   *   Numero de page (1-based).
   * @param int $limit
   *   Nombre d'elements par page.
   * @param string|null $langcode
   *   Code langue ISO.
   *
   * @return array
   *   Tableau pagine avec total, page, total_pages, items.
   *
   * @throws \InvalidArgumentException
   *   Si le vocabulaire n'existe pas.
   */
  public function getTermsByVocabulary(string $vid, int $page = 1, int $limit = 10, ?string $langcode = NULL): array;

}
