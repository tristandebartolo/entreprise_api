# Entreprise API

Module Drupal 11 d'exemple illustrant la creation d'une API REST custom avec documentation Swagger integree, sans dependance externe.

Ce module accompagne l'article technique :
**[Creer une API REST custom Drupal avec documentation Swagger integree](../../../../doc/003-creer-une-api-rest-custom-drupal-avec-documentation-swagger-integree.md)**

## Endpoints

| Methode | Route | Description |
|---------|-------|-------------|
| GET | `/api/entreprise/site-info` | Informations du site (nom, slogan, logo) |
| GET | `/api/entreprise/terms/{vid}` | Termes d'un vocabulaire (pagine) |

## Documentation Swagger

| Route | Description |
|-------|-------------|
| `/entreprise-api/swagger` | Interface Swagger UI interactive |
| `/entreprise-api/swagger/spec` | Specification JSON brute |

## Installation

```bash
drush en entreprise_api
drush cr
```

## Permissions

| Permission | Description |
|------------|-------------|
| `entreprise api read public` | Lecture des endpoints publics |
| `entreprise api read private` | Lecture des endpoints prives |
| `entreprise api administer` | Acces Swagger et configuration |

## Architecture

```
src/
  Controller/
    EntrepriseApiController.php   # Endpoints API (validation, delegation, reponse JSON)
    SwaggerController.php         # Page Swagger UI + spec JSON
  Service/
    EntrepriseServiceInterface.php # Contrat du service metier
    EntrepriseService.php          # Logique d'acces aux donnees
  EntrepriseApiDocumentation.php   # Specification des endpoints pour Swagger
```

Le controleur valide les parametres et delegue au service. Le service encapsule les requetes Drupal. La classe de documentation alimente le Swagger UI via un template Twig.

## Format de reponse

Succes :

```json
{
  "ok": true,
  "data": { ... },
  "meta": { "langcode": "fr" }
}
```

Erreur :

```json
{
  "ok": false,
  "error": "Vocabulaire 'xyz' introuvable."
}
```

## Authentification

Les endpoints acceptent deux modes :
- **Cookie** : session Drupal (navigateur)
- **Basic Auth** : header `Authorization: Basic` (clients externes)

