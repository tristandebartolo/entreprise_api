# Entreprise API

Module Drupal 11 d'exemple illustrant la création d'une API REST custom avec documentation Swagger integrée, sans dépendance externe.

Ce module accompagne l'article technique :
**[Creer une API REST custom Drupal avec documentation Swagger integree](https://communication.baarr.fr/drupal/creer-une-api-rest-custom-drupal-11-avec-documentation-swagger-integree)**

## Endpoints

| Méthode | Route | Description |
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
    EntrepriseApiController.php   # Endpoints API (validation, délégation, reponse JSON)
    SwaggerController.php         # Page Swagger UI + spec JSON
  Service/
    EntrepriseServiceInterface.php # Contrat du service metier
    EntrepriseService.php          # Logique d'accès aux données
  EntrepriseApiDocumentation.php   # Spécification des endpoints pour Swagger
```

Le controleur valide les paramètres et délégue au service. Le service encapsule les requêtes Drupal. La classe de documentation alimente le Swagger UI via un template Twig.



## Authentification

Les endpoints acceptent deux modes :
- **Cookie** : session Drupal (navigateur)
- **Basic Auth** : header `Authorization: Basic` (clients externes)

