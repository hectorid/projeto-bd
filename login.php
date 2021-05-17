<?php
require_once 'functions.php';

if (check_is_user_logged())
    redirect_to('/home');

?>
<!doctype html>
<html lang='en'>
<head>
    <!-- Required meta tags -->
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6' crossorigin='anonymous'>

    <!-- Custom CSS -->
<!--    <link rel='stylesheet' href='css/style.css'>-->

    <title>Projeto BD - Login</title>
</head>
<body class='d-flex justify-content-center align-items-center bg-light' style='height: 100vh'>
<main class=' p-2 m-auto' style='max-width: 400px'>
    <form action='#' id='formSignIn' novalidate>
        <div class='alert alert-danger visually-hidden' role='alert'></div>
        <fieldset disabled>
            <div class='row row-cols-1 gx-0 gy-3'>
                <div class='form-floating'>
                    <input type='email' name='user' id='formSignInUser' class='form-control' placeholder='.'>
                    <label for='formSignInUser'>Email ou Nome de Usuário</label>
                </div>
                <div class='form-floating'>
                    <input type='password' name='password' id='formSignInPassword' class='form-control' placeholder='.'>
                    <label for='formSignInPassword'>Senha</label>
                </div>
            </div>
            <div class='row row-cols-1 gx-0 gy-3 mt-3'>
                <button type='submit' id='FormSignInSubmit' class='w-100 btn btn-lg btn-primary '>Entrar</button>
                <button type='button' id='btnSignUp' class='w-100 btn btn-lg btn-outline-secondary'>Criar nova conta</button>
            </div>
        </fieldset>
    </form>

    <form action='#' id='formSignUp' class='visually-hidden' novalidate>
        <div class='alert alert-danger visually-hidden' role='alert'></div>
        <fieldset disabled>
            <div class='row row-cols-1 gx-0 gy-3'>
                <div class='form-floating'>
                    <input type='text' name='visible_name' id='formSignUpName' class='form-control' placeholder='.'>
                    <label for='formSignUpName'>Nome</label>
                </div>
                <div class='form-floating'>
                    <input type='text' name='username' id='formSignUpUsername' class='form-control' placeholder='.'>
                    <label for='formSignUpUsername'>Nome de Usuário</label>
                    <div class='form-text'>Não pode conter espaços</div>
                </div>
                <div class='form-floating'>
                    <input type='date' name='birthdate' id='formSignUpBirthdate' class='form-control' placeholder='.'>
                    <label for='formSignUpBirthdate'>Data de Nascimento</label>
                </div>
                <div class='form-floating'>
                    <input type='email' name='email' id='formSignUpEmail' class='form-control' placeholder='.'>
                    <label for='formSignUpEmail'>Email</label>
                </div>
                <div class='form-floating'>
                    <input type='password' name='password' id='formSignUpPassword' class='form-control' placeholder='.'>
                    <label for='formSignUpPassword'>Senha</label>
                </div>
                <div class='form-floating'>
                    <input type='password' id='formSignUpPasswordConfirmation' class='form-control' placeholder='.'>
                    <label for='formSignUpPasswordConfirmation'>Confirme sua Senha</label>
                </div>
            </div>
            <div class='row row-cols-1 gx-0 gy-3 mt-3'>
                <button type='submit' id='FormSignUpSubmit' class='w-100 btn btn-lg btn-primary '>Criar Nova conta</button>
                <button type='button' id='btnSignIn' class='w-100 btn btn-lg btn-outline-secondary'>Voltar</button>
            </div>
        </fieldset>
    </form>
</main>

<div class='modal fade' id='debugModal' tabindex='-1' aria-hidden='true'>
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='staticBackdropLabel'>Debug Output</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'></div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js' integrity='sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf' crossorigin='anonymous'></script>

<!-- Custom JS -->
<script src='js/functions.js'></script>
<script src='js/login.js'></script>
</body>
</html>