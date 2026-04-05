<?php

declare(strict_types=1);

namespace Drupal\entreprise_api\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Service principal de l'API Entreprise.
 */
class EntrepriseService implements EntrepriseServiceInterface {

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected FileUrlGeneratorInterface $fileUrlGenerator,
    protected ThemeHandlerInterface $themeHandler,
    protected LanguageManagerInterface $languageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getSiteInfo(?string $langcode = NULL): array {
    if ($langcode) {
      $language = $this->languageManager->getLanguage($langcode);
      if ($language) {
        $config = $this->languageManager
          ->getLanguageConfigOverride($langcode, 'system.site');
        $name = $config->get('name') ?: $this->configFactory->get('system.site')->get('name');
        $slogan = $config->get('slogan') ?: $this->configFactory->get('system.site')->get('slogan');
      }
    }

    if (!isset($name)) {
      $siteConfig = $this->configFactory->get('system.site');
      $name = $siteConfig->get('name');
      $slogan = $siteConfig->get('slogan');
    }

    // Logo du theme actif.
    $theme = $this->themeHandler->getDefault();
    $themeConfig = $this->configFactory->get($theme . '.settings');
    $logoPath = $themeConfig->get('logo.path');
    $logoUrl = $logoPath
      ? $this->fileUrlGenerator->generateAbsoluteString($logoPath)
      : '';

    return [
      'name' => $name,
      'slogan' => $slogan,
      'logo_url' => $logoUrl,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTermsByVocabulary(string $vid, int $page = 1, int $limit = 10, ?string $langcode = NULL): array {
    $storage = $this->entityTypeManager->getStorage('taxonomy_vocabulary');
    if (!$storage->load($vid)) {
      throw new \InvalidArgumentException("Vocabulaire '$vid' introuvable.");
    }

    $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');

    // Compter le total.
    $total = (int) $termStorage->getQuery()
      ->condition('vid', $vid)
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;
    $offset = ($page - 1) * $limit;

    // Charger les termes pagines.
    $tids = $termStorage->getQuery()
      ->condition('vid', $vid)
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->sort('weight')
      ->sort('name')
      ->range($offset, $limit)
      ->execute();

    $terms = $termStorage->loadMultiple($tids);
    $items = [];

    foreach ($terms as $term) {
      if ($langcode && $term->hasTranslation($langcode)) {
        $term = $term->getTranslation($langcode);
      }

      $item = [
        'tid' => (int) $term->id(),
        'vid' => $vid,
        'name' => $term->label(),
        'description' => $term->getDescription() ?: '',
        'url' => $term->toUrl()->setAbsolute()->toString(),
        'weight' => (int) $term->getWeight(),
      ];

      // Inclure les champs field_* remplis.
      $fieldDefinitions = $term->getFieldDefinitions();
      foreach ($fieldDefinitions as $fieldName => $definition) {
        if (str_starts_with($fieldName, 'field_') && !$term->get($fieldName)->isEmpty()) {
          $item[$fieldName] = $term->get($fieldName)->value;
        }
      }

      $items[] = $item;
    }

    return [
      'total' => $total,
      'page' => $page,
      'total_pages' => $totalPages,
      'items' => $items,
    ];
  }

}
