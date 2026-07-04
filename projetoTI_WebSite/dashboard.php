<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma IoT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <meta http-equiv="refresh" content="5">
</head>

<body>
<?php
  session_start();

  if (!isset($_SESSION['username'])) {
      header("refresh:5;url=index.php");
      die("Acesso Restrito");
  }

//----------------------------GETS dos Valores e Estados dos Sensores e Atuadores-----------------------------------------
  $valor_temperatura = file_get_contents("api/files/temperatura/valor.txt");
  $hora_temperatura = file_get_contents("api/files/temperatura/hora.txt");
  $nome_temperatura = file_get_contents("api/files/temperatura/nome.txt");

  $valor_humidade = file_get_contents("api/files/humidade/valor.txt");
  $hora_humidade = file_get_contents("api/files/humidade/hora.txt");
  $nome_humidade = file_get_contents("api/files/humidade/nome.txt");

  $estadoled = file_get_contents("api/files/led/valor.txt");
  $hora_led = file_get_contents("api/files/led/hora.txt");
  
  $estado_ventoinha = file_get_contents("api/files/ventoinha/valor.txt");
  $hora_ventoinha = file_get_contents("api/files/ventoinha/hora.txt");

  $estado_flame = file_get_contents("api/files/flame/valor.txt");
  $hora_flame = file_get_contents("api/files/flame/hora.txt");

  $estado_alarme = file_get_contents("api/files/alarme/valor.txt");
  $hora_alarme = file_get_contents("api/files/alarme/hora.txt");

  $estado_pir = file_get_contents("api/files/pir/valor.txt");
  $hora_pir = file_get_contents("api/files/pir/hora.txt");

  $estado_porta = file_get_contents("api/files/porta/valor.txt");
  $hora_porta = file_get_contents("api/files/porta/hora.txt");


//-------CONTROLE E MANIPULAÇÃO "AUTOMATICO" DE IMAGEM E ESTADOS DOS SENSORES/ATUADORES--------------

//---------------------------LED------------------------------------------
  if ($estadoled == 1) {
      $led_img = "./Imagens/light-on.png";  //Caso o Led esteja ligado, ele define a imagem do led para o light-on
      $estadoled = "Ativo";                 //define o estado do led como ativo
      $classe_led = "badge rounded-pill text-bg-success";   //define a badge como sucess
  } else {
      $led_img = "./Imagens/light-off.png";//Caso esteja desativo, define a imagem do led para light-off
      $estadoled = "Desativo";  //define estado para desativo
      $classe_led = "badge rounded-pill text-bg-secondary"; //define a badge como secondary
  }

//---------------------------PIR------------------------------------------
  if ($estado_pir == 1) {
      $pir_img = "./Imagens/light-on.png";
      $estado_pir = "Movimento Detetado";
      $classe_pir = "badge rounded-pill text-bg-success";
  } else {
      $pir_img = "./Imagens/light-off.png";
      $estado_pir = "Sem Movimento";
      $classe_pir = "badge rounded-pill text-bg-secondary";
  }



//---------------------------PORTA------------------------------------------
  if ($estado_porta == 1) {
      $porta_img = "./Imagens/porta-on.PNG";
      $estado_porta = "Porta Aberta";
      $classe_porta = "badge rounded-pill text-bg-success";
  } else {
      $porta_img = "./Imagens/porta-off.PNG";
      $estado_porta = "Porta Fechada";
      $classe_porta = "badge rounded-pill text-bg-secondary";
  }

//---------------------------ventoinha------------------------------------------
if ($estado_ventoinha == 1) {
    $ventoinha_img = "./Imagens/ventoinha-on.PNG";
    $estado_ventoinha = "Ativo";
    $classe_ventoinha = "badge rounded-pill text-bg-success";
} else {
    $ventoinha_img = "./Imagens/ventoinha-of.PNG";
    $estado_ventoinha = "Desativo";
    $classe_ventoinha = "badge rounded-pill text-bg-secondary";
}

//---------------------------TEMPERATURA------------------------------------------
  if ($valor_temperatura < 15) {
      $temp_img = "./Imagens/temperature-low.png";
      $estado_temp = "Baixo";
      $classe_temp = "badge rounded-pill text-bg-primary";
  } elseif ($valor_temperatura < 29) {
      $temp_img = "./Imagens/temperature-high.png";
      $estado_temp = "Média";
      $classe_temp = "badge rounded-pill text-bg-warning";
  } else {
      $temp_img = "./Imagens/temperature-high.png";
      $estado_temp = "Elevada";
      $classe_temp = "badge rounded-pill text-bg-danger";
  }

//---------------------------HUMIDADE------------------------------------------
  if ($valor_humidade < 30) {
      $humidade_img = "./Imagens/humidity-low.png";
      $estado_humidade = "Baixo";
      $classe_humidade = "badge rounded-pill text-bg-primary";
  } elseif ($valor_humidade < 60) {
      $humidade_img = "./Imagens/humidity-high.png";
      $estado_humidade = "Média";
      $classe_humidade = "badge rounded-pill text-bg-warning";
  } else {
      $humidade_img = "./Imagens/humidity-high.png";
      $estado_humidade = "Elevada";
      $classe_humidade = "badge rounded-pill text-bg-danger";
  }

  //---------------------------flame------------------------------------------
    if ($estado_flame == 1) {
        $flame_img = "./Imagens/flame-on.PNG";
        $estado_flame = "Ativo";
        $classe_flame = "badge rounded-pill text-bg-danger";
    } else {
        $flame_img = "./Imagens/flame-off.png";
        $estado_flame = "Desativo";
        $classe_flame = "badge rounded-pill text-bg-secondary";
    }
  //---------------------------Alarme------------------------------------------
    if ($estado_alarme == 1) {
        $alarme=1;
        $alarme_img = "./Imagens/alarme-on.PNG";
        $estado_alarme = "Ativo";
        $classe_alarme = "badge rounded-pill text-bg-danger";
    } else {
        $alarme=0;
        $alarme_img = "./Imagens/alarme-off.PNG";
        $estado_alarme = "Desativo";
        $classe_alarme = "badge rounded-pill text-bg-secondary";
    }
?>


<?php
//----------------------------------TELEGRAM HomeGuardBot-----------------------------------

//-----------Envia Mensagem para o Telegram
function enviarTelegram($mensagem) {
    $token = "8200859841:AAHGrvTQe3sXy9NTVyoS3TCn2LXcyNw3okk"; //Token do meu Chat com o Bot
    $chat_id = "8651161812";        //O ID do meu Chat com o Bot

    $url = "https://api.telegram.org/bot$token/sendMessage";    //URL da conversa (supostamente era para isso ser confidencial)

    $data = [
        'chat_id' => $chat_id,
        'text' => $mensagem
    ];

    //Envia um "POST" da mensagem que o bot vai me enviar
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    //faz get da mensagem que o bot enviou me
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

//-------------Coldown Entre Mensagens
function podeEnviarTelegram($chave, $tempo = 300) {
    $arquivo = "cooldowns/" . $chave . ".txt";
    $agora = time();

    if (file_exists($arquivo)) {
        $ultimo = (int)file_get_contents($arquivo);
    } else {
        $ultimo = 0;
    }

    if (($agora - $ultimo) >= $tempo) {
        file_put_contents($arquivo, $agora);
        return true;
    }

    return false;
}

//---------------Mensagens-----------------------

if ($alarme == 1) {
    if (podeEnviarTelegram("alarme")) {
        enviarTelegram("🚨 O alarme de incêndio está a tocar!");
    }
}

if($estado_flame == "Ativo")
{
    if (podeEnviarTelegram("flame")) {
        enviarTelegram("🔥: chamas detetadas!");
    }
}
/*
if($estado_humidade == "Baixo" && $estado_ventoinha == "Ativo")
{
    enviarTelegram("💧: Humidade baixa detetada, ventoinhas foram ativadas!");
}
if($valor_temperatura == "Elevada" && $estadoled == "Ativo")
{
    enviarTelegram("🌡: Temperatura elevada detetada, ar-condicionados foram ativados!");
}
*/

?>

<!--MENU-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fa-house-signal fw-bold" href="#">Dashboard HomeGuard</a>
        
        <div class="d-flex">
            <!--Só permite ir para pagina enviar dados se o utilizador for a Sofia ou o User-->
            <?php if ($_SESSION['username'] == "Sofia" || $_SESSION['username'] == "User" ) { ?>
                <a href="enviar_dados.php" class="btn btn-outline-light me-2">
                    Enviar Dados
                </a>
            <?php } ?>

            <a href="logout.php" class="btn btn-outline-danger">
                Logout
            </a>

        </div>
    </div>
</nav>


<div class="container py-4">

<!--TITULO-->
    <div id="title-header" class="text-center my-4">
        <h1 class="fw-bold">HomeGuard Server</h1>
        <p class="text-muted">User:<?php echo $_SESSION['username']?></p>
        <img src="./Imagens/estg.png" width="220" class="mt-3 logo" alt="Logo ESTG">
    </div>

    <div class="row text-center g-4 justify-content-center">
<!--SENSORES-->
        <!-- Temperatura -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white fw-bold fs-5">
                    <?php echo $nome_temperatura . ": " . $valor_temperatura . "º"; ?>
                </div>
                

                <img class="card-img-top p-4"
                     src="<?php echo $temp_img?>"
                     style="height:180px; object-fit:contain;" alt="Temperatura">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_temperatura; ?>
                </div>
            </div>
        </div>

        <!-- Humidade -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-info text-white fw-bold fs-5">
                    <?php echo $nome_humidade . ": " . $valor_humidade . "%"; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $humidade_img?>"
                     style="height:180px; object-fit:contain;" alt="Humidade">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_humidade; ?>
                </div>
            </div>
        </div>

        <!-- LED -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success text-white fw-bold fs-5">
                    Ar-Condicionado: <?php echo $estadoled; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $led_img?>"
                     style="height:180px; object-fit:contain;" alt="Arcondicionado">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_led; ?>
                </div>
            </div>
        </div>

        <!-- Webcam -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-secondary text-white fw-bold fs-5">
                    Webcam
                </div>

               <?php
                    echo "<img src='./Imagens/webcam.jpg?id=" . time() . "'
                            class='card-img-top p-4'
                            style='height:180px; object-fit:contain;'". " alt='WebCam'>";
                ?>

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_temperatura; ?>
                </div>
            </div>
        </div>

        <!-- Ventoinha -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white fw-bold fs-5">
                    Ventilação: <?php echo $estado_ventoinha; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $ventoinha_img?>"
                     style="height:180px; object-fit:contain;" alt="Ventilação">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_ventoinha; ?>
                </div>
            </div>
        </div>

        <!-- flame -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning text-white fw-bold fs-5">
                    Sensor Fogo: <?php echo $estado_flame; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $flame_img?>"
                     style="height:180px; object-fit:contain;" alt="Flame">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_flame; ?>
                </div>
            </div>
        </div>

        <!-- alarme -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-danger text-white fw-bold fs-5">
                    Alarme: <?php echo $estado_alarme; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $alarme_img?>"
                     style="height:180px; object-fit:contain;" alt="Alarme">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_alarme; ?>
                </div>
            </div>
        </div>

        <!-- Pir -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white fw-bold fs-5">
                    Porta: <?php echo $estado_porta; ?>
                </div>

                <img class="card-img-top p-4"
                     src="<?php echo $porta_img?>"
                     style="height:180px; object-fit:contain;" alt="Pir">

                <div class="card-footer text-muted">
                    Atualização: <?php echo $hora_porta; ?>
                </div>
            </div>
        </div>

    </div>

    <!--Tabela de sensores-->

    <div class="card shadow-sm border-0 rounded-4 mt-5">
        <div class="card-header bg-dark text-white fw-bold">
            Tabela de Sensores
        </div>

        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead>
                <tr>
                    <th>Dispositivo</th>
                    <th>Valor</th>
                    <th>Última atualização</th>
                    <th>Estado</th>
                </tr>
                </thead>

                <tbody>
              <!--Dados da Temperatura-->
                <tr>
                    <td><?php echo $nome_temperatura; ?></td>
                    <td><?php echo $valor_temperatura . "º"; ?></td>
                    <td><?php echo $hora_temperatura; ?></td>
                    <td>
                        <span class="<?php echo $classe_temp; ?>">
                            <?php echo $estado_temp; ?>
                        </span>
                    </td>
                </tr>
              <!--Dados da Humidade-->
                <tr>
                    <td><?php echo $nome_humidade; ?></td>
                    <td><?php echo $valor_humidade . "%"; ?></td>
                    <td><?php echo $hora_humidade; ?></td>
                    <td>
                        <span class="<?php echo $classe_humidade; ?>">
                            <?php echo $estado_humidade; ?>
                        </span>
                    </td>
                </tr>
              <!--Dados do Led-->
                <tr>
                    <td>Ar-Condicionado</td>
                    <td><?php echo $estadoled; ?></td>
                    <td><?php echo $hora_led; ?></td>
                    <td>
                        <span class="<?php echo $classe_led; ?>">
                            <?php echo $estadoled; ?>
                        </span>
                    </td>
                </tr>

              <!--Dados da Ventoinha-->
                <tr>
                    <td>Ventilação</td>
                    <td><?php echo $estado_ventoinha; ?></td>
                    <td><?php echo $hora_ventoinha; ?></td>
                    <td>
                        <span class="<?php echo $classe_ventoinha; ?>">
                            <?php echo $estado_ventoinha; ?>
                        </span>
                    </td>
                </tr>

                <!--Dados do sensor de chamas-->
                <tr>
                    <td>Sensor de Chamas</td>
                    <td><?php echo $estado_flame; ?></td>
                    <td><?php echo $hora_flame; ?></td>
                    <td>
                        <span class="<?php echo $classe_flame; ?>">
                            <?php echo $estado_flame; ?>
                        </span>
                    </td>
                </tr>

                <!--Dados do Alarme-->
                <tr>
                    <td>Alarme</td>
                    <td><?php echo $estado_alarme; ?></td>
                    <td><?php echo $hora_alarme; ?></td>
                    <td>
                        <span class="<?php echo $classe_alarme; ?>">
                            <?php echo $estado_alarme; ?>
                        </span>
                    </td>
                </tr>

                <!--Dados da Porta-->
                <tr>
                    <td>Porta</td>
                    <td><?php echo $estado_porta; ?></td>
                    <td><?php echo $hora_porta; ?></td>
                    <td>
                        <span class="<?php echo $classe_porta; ?>">
                            <?php echo $estado_porta; ?>
                        </span>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>