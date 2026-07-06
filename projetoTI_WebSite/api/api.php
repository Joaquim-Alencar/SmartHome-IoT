<?php

    header('Content-Type: text/html; charset=utf-8');
//---------------------------------Verifica se Recebeu POST-----------------------------
    if ($_SERVER['REQUEST_METHOD']=="POST"){

        //---------------------------------Verifica se tem os parametros permitidos
        if(isset($_POST['valor']) && isset($_POST['hora']) && isset($_POST['nome'])){
        
        $valor = $_POST['valor'];
        $hora = $_POST['hora'];
        $log = $hora. ";" .$valor;
        $nome=$_POST['nome'];

        //---------------------Guarda os parametros 
        file_put_contents("files/".$_POST['nome']."/valor.txt", $nome);
        file_put_contents("files/".$_POST['nome']."/valor.txt", $valor);
        file_put_contents("files/".$_POST['nome']."/hora.txt", $hora);
        
        //---------------------Cria e guarda o Log
        file_put_contents("files/".$_POST['nome']."/log.txt",$log."\n".PHP_EOL, FILE_APPEND);
        
            
        }
    }else{
    //---------------------------------Verifica se Recebeu GET-----------------------------
    
        if($_SERVER['REQUEST_METHOD']=="GET"){
            if (isset( $_GET["nome"]) ){
                //Devolve o valor do sensor
                echo file_get_contents("files/".$_GET['nome']."/valor.txt");
           
                }else{
                http_response_code(400);
                echo "Erro: faltam parâmetros";
                http_response_code(403);
                echo "Erro: sensor não existe";
            }
        }else{
            echo ("Metodo nao permitido");
        }
    }
    
?>