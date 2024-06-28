const items = document.querySelectorAll('.games-grid-item');

items.forEach(item => {
    const img = item.querySelector('.games-grid-item-img');
    const promo = item.querySelector('.promo');

    if (img && promo) {
        img.addEventListener('mouseover', () => {
          promo.style.top = '2px';
          promo.style.left = '2px';
          promo.style.borderTopLeftRadius = '2px';
          
        });

        img.addEventListener('mouseout', () => {
          promo.style.top = '0px';
          promo.style.left = '0px';        
          promo.style.borderTopLeftRadius = '5px';
        });
    }
});
