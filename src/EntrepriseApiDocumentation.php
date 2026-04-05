<?php

declare(strict_types=1);

namespace Drupal\entreprise_api;

/**
 * Generateur de specification API pour le Swagger UI Entreprise.
 *
 * Retourne un tableau PHP structurant tous les endpoints,
 * leurs parametres, methodes et exemples de reponse.
 */
class EntrepriseApiDocumentation {

  /**
   * Genere la specification complete de l'API Entreprise.
   *
   * @return array
   *   Tableau structurant l'API par groupes d'endpoints.
   */
  public static function getSpec(): array {
    return [
      'info' => [
        'title' => 'Entreprise API',
        'version' => 'v1',
        'description' => 'API REST pour les donnees entreprise : informations du site et taxonomies.',
      ],
      'basePath' => '/api/entreprise',
      'auth' => [
        'cookie' => 'Session Drupal (navigateur).',
        'basic_auth' => 'Basic Auth HTTP (utilisateur avec permission adequate).',
      ],
      'groups' => [
        self::siteInfoGroup(),
        self::taxonomyGroup(),
      ],
    ];
  }

  /**
   * Groupe Site Info.
   *
   * @return array
   *   Definition du groupe.
   */
  protected static function siteInfoGroup(): array {
    return [
      'name' => 'Site Info',
      'description' => 'Informations generales du site (nom, slogan, logo).',
      'endpoints' => [
        [
          'method' => 'GET',
          'path' => '/api/entreprise/site-info',
          'summary' => 'Recupere les informations du site.',
          'permission' => 'entreprise api read public',
          'parameters' => [
            [
              'name' => 'langcode',
              'in' => 'query',
              'required' => FALSE,
              'description' => 'Code langue ISO 2 lettres (ex: fr, en). Charge la traduction du nom/slogan si disponible.',
            ],
          ],
          'response_example' => [
            'ok' => TRUE,
            'data' => [
              'name' => 'Mon Entreprise',
              'slogan' => 'Solutions innovantes',
              'logo_url' => 'https://example.com/sites/default/files/logo.svg',
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Groupe Taxonomy.
   *
   * @return array
   *   Definition du groupe.
   */
  protected static function taxonomyGroup(): array {
    return [
      'name' => 'Taxonomy',
      'description' => 'Termes de taxonomie : listing pagine par vocabulaire.',
      'endpoints' => [
        [
          'method' => 'GET',
          'path' => '/api/entreprise/terms/{vid}',
          'summary' => 'Liste tous les termes d\'un vocabulaire (pagine).',
          'permission' => 'entreprise api read public',
          'parameters' => [
            [
              'name' => 'vid',
              'in' => 'path',
              'required' => TRUE,
              'description' => 'Machine name du vocabulaire (ex: categories, tags).',
            ],
            [
              'name' => 'ipp',
              'in' => 'query',
              'required' => FALSE,
              'description' => 'Nombre de resultats par page (defaut: 10, max: 50).',
            ],
            [
              'name' => 'page',
              'in' => 'query',
              'required' => FALSE,
              'description' => 'Numero de page (defaut: 1).',
            ],
            [
              'name' => 'langcode',
              'in' => 'query',
              'required' => FALSE,
              'description' => 'Code langue ISO 2 lettres (ex: fr, en). Charge la traduction si disponible.',
            ],
          ],
          'response_example' => [
            'ok' => TRUE,
            'data' => [
              [
                'tid' => 1,
                'vid' => 'categories',
                'name' => 'Technologie',
                'description' => 'Articles lies a la tech.',
                'url' => 'https://example.com/fr/categories/technologie',
                'weight' => 0,
                'field_color' => '#2196F3',
              ],
              [
                'tid' => 2,
                'vid' => 'categories',
                'name' => 'Marketing',
                'description' => '',
                'url' => 'https://example.com/fr/categories/marketing',
                'weight' => 1,
              ],
            ],
            'meta' => [
              'vid' => 'categories',
              'page' => 1,
              'ipp' => 10,
              'total' => 25,
              'total_pages' => 3,
              'langcode' => 'fr',
            ],
          ],
          'notes' => 'Retourne tous les termes publies du vocabulaire, tries par poids puis par nom. Les champs field_* varient selon le vocabulaire et ne sont inclus que s\'ils sont remplis.',
        ],
      ],
    ];
  }

}
