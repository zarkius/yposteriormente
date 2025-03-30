<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="manifest" href="manifest.json" />
    <title>Inicio - www.yposteriormente.com</title>
    <meta
      name="description"
      content="Bienvenido a www.yposteriormente.com, tu sitio web para servicios y notificaciones."
    />
    <meta
      name="keywords"
      content="servicios, notificaciones, hogar, contacto, acerca de,era TRUMP, la era TRUMP ha llegado, TRUMP,AMERICA, GRANDE, DE NUEVO"
    />
    <meta name="author" content="www.yposteriormente.com" />
    <script>
      if ("serviceWorker" in navigator) {
        window.addEventListener("load", function () {
          navigator.serviceWorker.register("/service-worker.js").then(
            function (registration) {
              console.log(
                "ServiceWorker registration successful with scope: ",
                registration.scope
              );
            },
            function (error) {
              console.log("ServiceWorker registration failed: ", error);
            }
          );
        });
      }
    </script>
  </head>
  <body>
    <a href="juego.html"><i class="fa fa-dungeon"></i> Juego</a>
    <div id="login-button" style="width: 250px; margin-top: 20px">
      <button onclick="loginWithGoogle()">Iniciar sesión con Google</button>
    </div>
    <header>
      <h1>www.yposteriormente.com</h1>
      <p>
        La <b>ERA TRUMP</b> ha llegado y con ella el Internet moderno en la
        <b>NUBE</b>
      </p>
      <h2>Quédate para estar informado!</h2>
    </header>

    <!-- Formulario CRUD -->
    <section>
      <h2>Gestión de Usuarios</h2>
      <form id="crud-form">
        <label for="id">ID (solo para actualizar/eliminar):</label>
        <input type="number" id="id" name="id" placeholder="ID del usuario" />

        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" placeholder="Nombre del usuario" />

        <label for="email">Correo:</label>
        <input type="email" id="email" name="email" placeholder="Correo del usuario" />

        <button type="button" onclick="createUser()">Crear</button>
        <button type="button" onclick="readUsers()">Leer</button>
        <button type="button" onclick="updateUser()">Actualizar</button>
        <button type="button" onclick="deleteUser()">Eliminar</button>
      </form>

      <div id="response"></div>
    </section>

    <footer>
      <p>
        Contribuye a la <b>DIASPORA registrandote <a href="https://hostinger.es?REFERRALCODE=S9IDIEGOMG3Y"><b>AQUÍ</b></a></b>
      </p>
      <p>
        &copy; 2025 - Todos los derechos reservados
        <a href="/privacidad.html">Privacidad</a>
      </p>
    </footer>

    <script>
      const apiUrl = "https://www.yposteriormente.com/api.php";

      // Crear usuario
      function createUser() {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;

        fetch(apiUrl, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ name, email }),
        })
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("response").innerText = JSON.stringify(data);
          })
          .catch((error) => console.error("Error:", error));
      }

      // Leer usuarios
      function readUsers() {
        fetch(apiUrl)
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("response").innerText = JSON.stringify(data, null, 2);
          })
          .catch((error) => console.error("Error:", error));
      }

      // Actualizar usuario
      function updateUser() {
        const id = document.getElementById("id").value;
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;

        fetch(apiUrl, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id, name, email }),
        })
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("response").innerText = JSON.stringify(data);
          })
          .catch((error) => console.error("Error:", error));
      }

      // Eliminar usuario
      function deleteUser() {
        const id = document.getElementById("id").value;

        fetch(apiUrl, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id }),
        })
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("response").innerText = JSON.stringify(data);
          })
          .catch((error) => console.error("Error:", error));
      }

      const googleClientId = "483619470669-mj5uaa1j7mh0url8molc7nnv846cli2u.apps.googleusercontent.com";
      const redirectUri = "https://www.yposteriormente.com/oauth-callback.php";

      function loginWithGoogle() {
        google.accounts.id.initialize({
          client_id: "TU_CLIENT_ID",
          callback: handleCredentialResponse,
        });
        google.accounts.id.prompt();
      }

      function handleCredentialResponse(response) {
        console.log("ID Token:", response.credential);
        // Enviar el token al servidor para autenticar
        fetch("https://www.yposteriormente.com/api.php", {
          method: "GET",
          headers: {
            Authorization: "Bearer " + response.credential,
          },
        })
          .then((res) => res.json())
          .then((data) => console.log(data))
          .catch((err) => console.error(err));
      }

      // Capturar el token de acceso de la URL
      if (window.location.hash) {
        const hashParams = new URLSearchParams(window.location.hash.substring(1));
        const accessToken = hashParams.get("access_token");

        if (accessToken) {
          console.log("Access Token:", accessToken);

          // Guardar el token en localStorage o sessionStorage
          localStorage.setItem("accessToken", accessToken);

          // Usar el token para obtener información del usuario
          getUserInfo(accessToken);
        }
      }

      function getUserInfo(accessToken) {
        fetch("https://www.googleapis.com/oauth2/v1/userinfo?access_token=" + accessToken)
          .then((response) => response.json())
          .then((userInfo) => {
            console.log("Información del usuario:", userInfo);

            // Mostrar la información del usuario en la página
            document.body.innerHTML += `<p>Bienvenido, ${userInfo.name} (${userInfo.email})</p>`;
          })
          .catch((error) => console.error("Error al obtener la información del usuario:", error));
      }
    </script>
  </body>
</html>
