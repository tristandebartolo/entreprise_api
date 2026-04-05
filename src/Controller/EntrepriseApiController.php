<?php

declare(strict_types=1);

namespace Drupal\entreprise_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\entreprise_api\Service\EntrepriseServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controleur des endpoints de l'API Entreprise.
 *
 * Format de reponse : { ok: true, data: ..., meta: ... }
 * Format d'erreur :   { ok: false, error: "message" }
 */
class EntrepriseApiController extends ControllerBase {

  public function __construct(
    protected EntrepriseServiceInterface $entrepriseService,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entreprise_api.service'),
    );
  }

  /**
   * Informations du site (nom, slogan, logo).
   *
   * Route : GET /api/entreprise/site-info
   */
  public function siteInfo(Request $request): JsonResponse {
    $langcode = $request->query->get('langcode');
    if ($langcode !== NULL && !preg_match('/^[a-z]{2}$/', $langcode)) {
      return $this->error('Parametre langcode invalide (attendu : code 2 lettres).', 400);
    }

    $data = $this->entrepriseService->getSiteInfo($langcode);
    return $this->success($data, $langcode !== NULL ? ['langcode' => $langcode] : NULL);
  }

  /**
   * Termes d'un vocabulaire.
   *
   * Route : GET /api/entreprise/terms/{vid}
   */
  public function termsByVocabulary(string $vid, Request $request): JsonResponse {
    $langcode = $request->query->get('langcode');
    if ($langcode !== NULL && !preg_match('/^[a-z]{2}$/', $langcode)) {
      return $this->error('Parametre langcode invalide (attendu : code 2 lettres).', 400);
    }

    $page = max(1, (int) $request->query->get('page', 1));
    $limit = max(1, min((int) $request->query->get('ipp', 10), 50));

    try {
      $data = $this->entrepriseService->getTermsByVocabulary($vid, $page, $limit, $langcode);
      return $this->success($data['items'], [
        'vid' => $vid,
        'page' => $data['page'],
        'ipp' => $limit,
        'total' => $data['total'],
        'total_pages' => $data['total_pages'],
        'langcode' => $langcode,
      ]);
    }
    catch (\InvalidArgumentException $e) {
      return $this->error($e->getMessage(), 404);
    }
  }

  /**
   * Reponse JSON de succes.
   */
  protected function success(mixed $data, ?array $meta = NULL, int $status = 200): JsonResponse {
    $response = ['ok' => TRUE, 'data' => $data];
    if ($meta !== NULL) {
      $response['meta'] = $meta;
    }
    return new JsonResponse($response, $status);
  }

  /**
   * Reponse JSON d'erreur.
   */
  protected function error(string $message, int $status = 400): JsonResponse {
    return new JsonResponse([
      'ok' => FALSE,
      'error' => $message,
    ], $status);
  }

}
