<?php
require_once 'functions.php';

if (!check_is_user_logged())
    redirect_to('/login.php');

?>
<!doctype html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>Projeto BD</title>

    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css' rel='stylesheet'
          integrity='sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0' crossorigin='anonymous'>

    <!-- Bootstrap Icons CSS -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css'>

    <!-- Custom CSS -->
    <link rel='stylesheet' href='/css/style.css'>
</head>
<body class='w-100'>
<div class='container-xxl'>
    <div class='row'>
        <nav id='sidenav' class='col-3 nav nav-pills flex-column d-flex sticky-top p-2 border-end'
             style='top: 0; height: 100vh'>
            <a href='/home' class='nav-link fs-5 text-nowrap' data-link><i class="bi-house-fill me-3"></i>Início</a>
            <a href='/profile' class='nav-link fs-5 text-nowrap' data-link><i class='bi-person-circle me-3'></i>Meu Perfil</a>
            <a href='/messages' class='nav-link fs-5 text-nowrap' data-link><i class='bi-chat-left-text-fill me-3'></i>Mensagens</a>
            <a href='/settings' class='nav-link fs-5 text-nowrap' data-link><i class='bi-gear-fill me-3'></i>Configurações</a>

            <button id='btnLogout' class='nav-link fs-5 text-nowrap text-start mt-auto'><i class="bi-box-arrow-left me-3"></i>Sair</button>
        </nav>
        <main id='mainView' class='col p-0'></main>
    </div>
</div>

<template id='mainViewLoadingTemplate'>
    <div class='text-center w-100'>
        <div class='spinner-border text-primary mt-5' role='status'>
            <span class='visually-hidden'>Loading...</span>
        </div>
    </div>
</template>

<template id='mainViewErrorTemplate'>
    <div class='d-flex flex-column align-items-center gap-3 w-100'>
        <span class='mt-5'>Não foi possível carregar a página :(</span>
        <button class='btn btn-primary' data-reload>Tentar Novamente</button>
    </div>
</template>

<template id='mainViewNotFoundTemplate'>
    <div class='d-flex flex-column align-items-center gap-3 w-100'>
        <span class='mt-5'>Essa página não existe :(</span>
        <a href='/home' class='btn btn-primary' data-link>Voltar para o início</a>
    </div>
</template>

<div class='modal fade' id='debugModal' tabindex='-1' aria-hidden='true'>
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Debug Output</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'></div>
        </div>
    </div>
</div>


<!-- Bootstrap Bundle with Popper -->
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js'
        integrity='sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8'
        crossorigin='anonymous'></script>

<!-- Custom JS -->
<script> const USER_ID = <?= USER_ID ?>; </script>
<script type='text/javascript' src='/js/functions.js'></script>
<script type='text/javascript' src='/js/index.js'></script>
</body>
</html>