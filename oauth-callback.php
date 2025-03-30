<?php
// filepath: c:\Users\diego\OneDrive\Desktop\NAMESPACEWEB\yposteriormente.com\public_html\oauth-callback.php

if (isset($_GET['access_token'])) {
    $accessToken = $_GET['access_token'];

    // Obtener información del usuario desde Google
    $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=$accessToken";
    $response = file_get_contents($url);
    $userInfo = json_decode($response, true);

    if (isset($userInfo['email'])) {
        // Aquí puedes manejar la lógica de autenticación, como guardar el usuario en la base de datos
        echo "Bienvenido, " . htmlspecialchars($userInfo['email']);
    } else {
        echo "Error al obtener la información del usuario.";
    }
} else {
    echo "No se recibió un token de acceso.";
}
?>