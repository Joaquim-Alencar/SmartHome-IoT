<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HomeGuard - Login</title>

    <link rel="stylesheet" href="style_login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>

<?php
session_start();
//---------------Lista de usuarios-----------------------
//Deveria está dentro de uma base de dados mas okay
$users = [
    "Sofia" => '$2y$10$M08m.NvUuiJJYZtyOvPe6u/9sju5uccMNJmc3.J8THji/p1b4SI6y',
    "User" => password_hash("User", PASSWORD_DEFAULT),
    "Visita" => password_hash("123", PASSWORD_DEFAULT),
];

//-----------------verifica o login--------------------
if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username])) {
        if (password_verify($password, $users[$username])) {
            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            echo '<div class="alert alert-danger text-center m-3">Password incorreta</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center m-3">Utilizador não existe</div>';
    }
}
?>
<!-- -----------------NAVBAR-------------------------------- -->
<nav class="navbar navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fa-solid fa-house-signal"></i> HomeGuard IoT
        </a>
    </div>
</nav>

<!-- "Bloco" do login -->
<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="row login-wrapper shadow-lg rounded-4 overflow-hidden">

        <!-- Lado esquerdo -->
        <div class="col-lg-6 d-none d-lg-flex login-image">
            <div class="overlay-text">
                <h1>Smart Home Security</h1>
                <p>Monitorização em tempo real da sua casa.</p>
            </div>
        </div>

        <!-- Lado direito -->
        <div class="col-lg-6 bg-white p-5">

            <div class="text-center mb-4">
                <img src="estg.png" width="220" class="logo mb-4" alt="Logo ESTG">
                <h2 class="fw-bold">Bem-vindo</h2>
                <p class="text-muted">Aceda à sua plataforma IoT</p>
            </div>
            
        <!-- Faz o LOGIN -->
            <form method="post">

                <div class="mb-4 input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input 
                        name="username" 
                        type="text" 
                        class="form-control form-control-lg"
                        placeholder="Username"
                        required>
                </div>

                <div class="mb-4 input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input 
                        name="password" 
                        type="password" 
                        class="form-control form-control-lg"
                        placeholder="Password"
                        required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login btn-lg text-white">
                        Entrar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>