document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.platform').forEach(platform => {
      platform.addEventListener('click', function () {
          document.querySelectorAll('.platform').forEach(p => p.classList.remove('platform-selected'));
          this.classList.add('platform-selected'); // Marque la plateforme comme sélectionnée
      });
  });

  // Ajout du jeu au panier
  const addToCartButton = document.querySelector('#add-to-cart-button');
  addToCartButton.addEventListener('click', function () {
      const platformElement = document.querySelector('.platform-selected');
      if (platformElement) {
          const platformId = platformElement.dataset.platformId;

          fetch('', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify({ id: gameId, platformId: platformId }),
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  alert('Jeu ajouté au panier');
                  // Recharger la page pour voir les modifications
                  window.location.reload();
              } else {
                  alert('Erreur: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
          });
      } else {
          alert('Veuillez sélectionner une plateforme.');
      }
  });

  // Mettre à jour le nombre total de jeux dans le panier
  const updateCartCount = () => {
      fetch('../Class/Cart.php', {
          method: 'GET',
          headers: {
              'Content-Type': 'application/json',
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.total) {
              document.querySelector('#total-items').textContent = data.total;
          }
      })
      .catch(error => {
          console.error('Error:', error);
      });
  };

  updateCartCount();
});
