let username;

function handleCredentialResponse(response) {
    const idToken = response.credential;
    const userInfo = jwt_decode(idToken);
    console.log("Informations de l'utilisateur : ", userInfo);
    username = userInfo.email;

    // Rediriger l'utilisateur après la connexion
    window.location.href = 'http://localhost:80/RightNow-Gaming/index.php';
  }

  window.onload = function () {
    google.accounts.id.initialize({
      client_id: '49695563635-74haunh3mh8gqso5fij3v93lirtuck9o.apps.googleusercontent.com',
      callback: handleCredentialResponse
    });

    document.getElementById('googleSignInButton').onclick = function() {
      google.accounts.id.prompt();
    };

  }


