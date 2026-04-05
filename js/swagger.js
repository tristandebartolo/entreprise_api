/**
 * @file
 * Logique interactive pour le Swagger UI de entreprise_api.
 */
(function () {
  'use strict';

  /**
   * Toggle l'affichage du body d'un endpoint au clic sur le header.
   */
  function initToggle() {
    document.querySelectorAll('.swagger-endpoint-header').forEach(function (header) {
      header.addEventListener('click', function () {
        var body = this.nextElementSibling;
        if (body && body.classList.contains('swagger-endpoint-body')) {
          body.style.display = body.style.display === 'none' ? 'block' : 'none';
        }
      });
    });
  }

  /**
   * Envoie une requete de test depuis le Swagger UI.
   */
  function initTryButtons() {
    document.querySelectorAll('.swagger-try-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var endpoint = this.closest('.swagger-endpoint');
        var method = endpoint.dataset.method;
        var urlInput = endpoint.querySelector('.swagger-try-url');
        var resultPre = endpoint.querySelector('.swagger-try-result');

        var url = urlInput.value;
        var headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        };

        var fetchOptions = {
          method: method,
          headers: headers,
          credentials: 'same-origin'
        };

        resultPre.textContent = 'Chargement...';
        resultPre.style.display = 'block';

        fetch(url, fetchOptions)
          .then(function (response) {
            return response.text().then(function (text) {
              return { status: response.status, body: text };
            });
          })
          .then(function (result) {
            var display = 'HTTP ' + result.status + '\n';
            try {
              var json = JSON.parse(result.body);
              display += JSON.stringify(json, null, 2);
            } catch (e) {
              display += result.body;
            }
            resultPre.textContent = display;
          })
          .catch(function (err) {
            resultPre.textContent = 'Erreur : ' + err.message;
          });
      });
    });
  }

  // Initialisation au chargement du DOM.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initToggle();
      initTryButtons();
    });
  } else {
    initToggle();
    initTryButtons();
  }

})();
