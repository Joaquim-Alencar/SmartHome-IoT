<?php
session_start();

if (!isset($_SESSION['username']) ||($_SESSION['username']==="Visita")) {
    header("refresh:5;url=index.php");
    die("Acesso Restrito");
}
//-------------------------GETS------------------------------------------
$modo_manual = file_exists("api/files/manual/valor.txt")
    ? file_get_contents("api/files/manual/valor.txt")
    : 0;

$estado_ar = file_exists("api/files/led/valor.txt")
    ? file_get_contents("api/files/led/valor.txt")
    : 0;

$estado_ventoinha = file_exists("api/files/ventoinha/valor.txt")
    ? file_get_contents("api/files/ventoinha/valor.txt")
    : 0;

$estado_porta = file_exists("api/files/porta/valor.txt")
    ? file_get_contents("api/files/porta/valor.txt")
    : 0;

//------------------------POSTS DOS FORMULARIOS-------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['modo_manual'])) {
        $modo_manual = $_POST['modo_manual'];
        file_put_contents("api/files/manual/valor.txt", $modo_manual);
    }

    if ($modo_manual == 1) {
        //SO MUDA O VALOR SE O MODO MANUAL FOR IGUAL A 1 (ESTEJA ATIVADO)
        if (isset($_POST['arcondicionado'])) {
            file_put_contents("api/files/led/valor.txt", $_POST['arcondicionado']);
            file_put_contents("api/files/led/hora.txt", date("H:i:s"));
            $estado_ar = $_POST['arcondicionado'];
        }

        if (isset($_POST['ventoinha'])) {
            file_put_contents("api/files/ventoinha/valor.txt", $_POST['ventoinha']);
            file_put_contents("api/files/ventoinha/hora.txt", date("H:i:s"));
            $estado_ventoinha = $_POST['ventoinha'];
        }

    }
    //É SEMPRE PERMITIDO MUDAR O VOLOR DA PORTA
    if (isset($_POST['porta'])) {
            file_put_contents("api/files/porta/valor.txt", $_POST['porta']);
            file_put_contents("api/files/porta/hora.txt", date("H:i:s"));
            $estado_porta = $_POST['porta'];
    }
}
?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Controlo Manual</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
</head>

<body>


 
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Dashboard EI-TI</a>

        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-outline-primary me-2">Voltar</a>
            <a href="logout.php" class="btn btn-outline-danger">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- HEADER -->
    <div class="text-center mb-5">
        <h1 class="fw-bold">Painel de Controlo Manual</h1>
        <p class="text-muted">Gerir dispositivos manualmente</p>
    </div>

    <!-- MODO MANUAL -->
    <div class="card main-card p-4 mb-5">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2 class="mb-2">Modo Manual</h2>
                <!-- Se o Modo Manual Tiver a 1 Aparece que Esta Ativado -->
                <?php if ($modo_manual == 1): ?>
                    <span class="badge bg-success status-badge">
                        <i class="bi bi-check-circle"></i> Ativo
                    </span>
                 <!-- Se o Modo Manual Tiver a 0 Aparece que Esta Desativado -->   
                <?php else: ?>
                    <span class="badge bg-secondary status-badge">
                        <i class="bi bi-x-circle"></i> Desativado
                    </span>
                <?php endif; ?>
            </div>
            <!-- Faz o Post do Modo Manual -->
            <form method="POST">
                <!-- Verifica se O modo Manual ta a 0, se sim aparece o botao para ativar -->
                 <!-- Apenas permite mudar se o utilizador for a Sofia -->
                <?php if ($modo_manual == 0): ?> 
                    <button type="submit" name="modo_manual" value="1"
                        class="btn btn-success btn-lg"
                        <?php if ($_SESSION['username'] != "Sofia") echo "disabled"; ?>>
                        Ativar
                    </button>
                <!-- Verifica se o modo Manual ta a 1, se sim aparece o botao para desativar -->
                 <!-- Apenas permite mudar se o utilizador for a Sofia -->
                <?php else: ?>
                    <button type="submit" name="modo_manual" value="0"
                        class="btn btn-danger btn-lg"
                        <?php if ($_SESSION['username'] != "Sofia") echo "disabled"; ?>>
                        Desativar
                    </button>
                <?php endif; ?>
            </form>

        </div>
    </div>

    <!-- DISPOSITIVOS -->
    <div class="row g-4">
   
        <!-- AR CONDICIONADO -->
        <div class="col-md-4">
            <div class="card device-card p-4 text-center">
                <i class="bi bi-snow icon-big"></i>
                <h3 class="mt-3">Ar-Condicionado</h3>
                  <!-- Se o arcondicionado Tiver a 1 Aparece que Esta Ativado -->
                <?php if ($estado_ar == 1): ?>
                    <span class="badge bg-success status-badge mt-2">
                        <i class="bi bi-check-circle"></i> Ativo
                    </span>
                <!-- Se o arcondicionado Tiver a 0 Aparece que Esta Ativado -->
                <?php else: ?>
                    <span class="badge bg-secondary status-badge mt-2">
                        <i class="bi bi-x-circle"></i> Desativado
                    </span>
                <?php endif; ?>

                <!-- Utiliza metodo post para Muda o Estado do Arcondicionado -->
                 <!-- Apenas permite mudar se o utilizador for a Sofia e o Modo Manual for igual a 1 -->
                <form method="POST">
                    <button type="submit" name="arcondicionado" value="1"
                        class="btn btn-success control-btn mt-3"
                        <?php if ($modo_manual == 0 || $_SESSION['username'] != "Sofia" ) echo "disabled"; ?>>
                        Ativar
                    </button>

                    <button type="submit" name="arcondicionado" value="0"
                        class="btn btn-danger control-btn mt-2"
                        <?php if ($modo_manual == 0 || $_SESSION['username'] != "Sofia") echo "disabled"; ?>>
                        Desativar
                    </button>
                </form>
            </div>
        </div>

        <!-- VENTOINHA -->
        <div class="col-md-4">
            <div class="card device-card p-4 text-center">
                <i class="bi bi-fan icon-big"></i>
                <h3 class="mt-3">Ventilação</h3>
                <!-- Se a ventilação Tiver a 1 Aparece que Esta Ativado -->
                <?php if ($estado_ventoinha == 1): ?>
                    <span class="badge bg-success status-badge mt-2">
                        <i class="bi bi-check-circle"></i> Ativa
                    </span>
                <!-- Se a ventilação Tiver a 0 Aparece que Esta Desativado -->
                <?php else: ?>
                    <span class="badge bg-secondary status-badge mt-2">
                        <i class="bi bi-x-circle"></i> Desativada
                    </span>
                <?php endif; ?>

                <!-- Utiliza metodo post para Muda o Estado da Ventilação -->
                 <!-- Apenas permite mudar se o utilizador for a Sofia e o Modo Manual for igual a 1 -->
                <form method="POST">
                    <button type="submit" name="ventoinha" value="1"
                        class="btn btn-success control-btn mt-3"
                        <?php if ($modo_manual == 0 || $_SESSION['username'] != "Sofia") echo "disabled"; ?>>
                        Ativar
                    </button>

                    <button type="submit" name="ventoinha" value="0"
                        class="btn btn-danger control-btn mt-2"
                        <?php if ($modo_manual == 0 || $_SESSION['username'] != "Sofia") echo "disabled"; ?>>
                        Desativar
                    </button>
                </form>
            </div>
        </div>

        <!-- PORTA -->
        <div class="col-md-4">
            <div class="card device-card p-4 text-center">
                <i class="bi bi-door-open icon-big"></i>
                <h3 class="mt-3">Porta</h3>

                <!-- Se a Porta Tiver a 1 Aparece que Esta aberta -->
                <?php if ($estado_porta == 1): ?>
                    <span class="badge bg-primary status-badge mt-2">
                        <i class="bi bi-door-open"></i> Aberta
                    </span>
                <!-- Se a Porta Tiver a 0 Aparece que Esta Fechada -->
                <?php else: ?>
                    <span class="badge bg-dark status-badge mt-2">
                        <i class="bi bi-door-closed"></i> Fechada
                    </span>
                <?php endif; ?>

                 <!-- Utiliza metodo post para Muda o Estado da Porta -->
                 <!-- Apenas permite mudar se o utilizador não for o visitante, entretanto o vistante não consegue aceder a essa pagina -->
                <form method="POST">
                    <button type="submit" name="porta" value="1"
                        class="btn btn-primary control-btn mt-3"
                        <?php if ($_SESSION['username'] === "Visita" ) echo "disabled"; ?>>
                        Abrir
                    </button>

                    <button type="submit" name="porta" value="0"
                        class="btn btn-secondary control-btn mt-2"
                        <?php if ($_SESSION['username'] === "Visita") echo "disabled"; ?>>
                        Fechar
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
