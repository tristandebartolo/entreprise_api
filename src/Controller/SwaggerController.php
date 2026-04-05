<?php

declare(strict_types=1);

namespace Drupal\entreprise_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\entreprise_api\EntrepriseApiDocumentation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controleur pour l'interface Swagger UI de l'API Entreprise.
 *
 * Sert deux routes :
 *   - GET /entreprise-api/swagger      : page HTML interactive
 *   - GET /entreprise-api/swagger/spec : specification JSON brute
 *
 * Les deux routes requierent la permission 'entreprise api administer'.
 */
class SwaggerController extends ControllerBase {

  /**
   * Page Swagger UI — documentation interactive.
   */
  public function ui(): array {
    $spec = EntrepriseApiDocumentation::getSpec();

    return [
      '#theme' => 'entreprise_swagger',
      '#spec' => $spec,
      '#attached' => [
        'library' => ['entreprise_api/swagger'],
      ],
    ];
  }

  /**
   * Specification JSON brute.
   */
  public function spec(): JsonResponse {
    return new JsonResponse(EntrepriseApiDocumentation::getSpec());
  }

}
