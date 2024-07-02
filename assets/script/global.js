let username;

function handleCredentialResponse(response) {
    const idToken = response.credential;
    const userInfo = jwt_decode(idToken);
    console.log("Informations de l'utilisateur : ", userInfo);
    username = userInfo.email;

    // Rediriger l'utilisateur après la connexion
    window.location.href = 'http://localhost:80/RightNow-Gaming/index.php';
}

// CHANGEMENT DE HEADER AU SCROLL

document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('nav');

    function handleScroll() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll);

    // Initialisation du dropdown

    document.getElementsByClassName('userButton')[0].addEventListener('click', function() {
        var dropdown = document.getElementsByClassName('profil-dropdown')[0];
        dropdown.classList.toggle('showDropdown');
    });

    // Fonction pour mettre à jour le nombre total d'articles dans le panier
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

    // Recharger le nombre d'articles dans le panier après l'ajout d'un article
    document.querySelector('#add-to-cart-button').addEventListener('click', () => {
        // Recharger le nombre total d'articles dans le panier
        updateCartCount();
    });
});

//-------------
//-------------PANEL ADMIN
//-------------

//Page params getter
function getParamPage(){
    //On récupère l'url active
    const params = new URLSearchParams(window.location.search);

    const url = [];
    for (let paramName of params.keys()) {
        url.push(paramName);
    }

    return(url[0]);
}

const active = getParamPage();

function showActivePage(active) {
    //On récupère les boutons
    const userButton = document.getElementById('userPage');
    const productButton = document.getElementById('productPage');
    const orderButton = document.getElementById('orderPage');

    if (active === 'users') {
        userButton.classList.add('activeButton');
    } else if (active === 'products') {
        productButton.classList.add('activeButton');
    } else if (active === 'orders') {
        orderButton.classList.add('activeButton');
    }
}

showActivePage(active);

function showNotification(message) {
    var notification = document.getElementById("notification");
    notification.innerHTML = message;
    notification.style.display = "block";
    notification.style.opacity = 1;

    setTimeout(function() {
        var fadeEffect = setInterval(function () {
            if (!notification.style.opacity) {
                notification.style.opacity = 1;
            }
            if (notification.style.opacity > 0) {
                notification.style.opacity -= 0.1;
            } else {
                clearInterval(fadeEffect);
                notification.style.display = "none";
            }
        }, 50);
    }, 3000); // 3-second delay before fading out
}

