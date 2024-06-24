document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('nav');
    console.log(nav); // Vérifie si nav est null ou non

    function handleScroll() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll);
});
