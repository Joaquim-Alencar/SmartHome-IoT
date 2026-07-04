<?php

header('Content-Type: text/html; charset=utf-8');
//-------------Verifica se recebeu um POST------------------
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    //----DEBUG------
    echo "recebi um post<br>";

    echo "<pre>";
    print_r($_FILES);
    print_r($_POST);
    echo "</pre>";

    //--------Verifica se Recebeu uma Imagem-----------
    if (isset($_FILES['imagem'])) {

        //--------Verifica se ouve falha no envio
        $error = $_FILES['imagem']['error'];

        echo "erro upload: " . $error . "<br>";

        if ($error === 0) {

            //---------------Limita o tamanho da Imagem para 1000 KB
            $maxSize = 1000 * 1024;

            //---------------Verifica se a imagem excede o limite
            if ($_FILES['imagem']['size'] > $maxSize) {
                die("Erro: a imagem excede o tamanho máximo de 1000 KB.");
            }

            // Verifica se é uma imagem válida
            if (getimagesize($_FILES['imagem']['tmp_name']) === false) {
                die("Erro: o ficheiro enviado não é uma imagem válida.");
            }

            // Tipos de imagens permitidas
            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            //Verifica e retorna o tipo da imagem
            $mimeType = mime_content_type($_FILES['imagem']['tmp_name']);

            //Verifica se o tipo da imagem é valida
            if (!in_array($mimeType, $allowedTypes)) {
                die("Erro: apenas imagens JPG, PNG e WebP são permitidas.");
            }

            //Define para aonde a imagem vai
            $tmp_name = $_FILES["imagem"]["tmp_name"];
            $target_directory = "../Imagens/";
            $target_filename = "webcam.jpg";

            //Verifica se o diretorio existe
            if (!is_dir($target_directory)) {
                die("Erro: diretório de destino não existe.");
            }

            //Substitui a imagem pela que ta na webcam atualmente
            if (move_uploaded_file($tmp_name, $target_directory . $target_filename)) {
                echo "Upload OK<br>";
            } else {
                echo "Falha ao mover ficheiro<br>";
            }

        } else {
            echo "Upload falhou com erro: " . $error;
        }

    } else {
        echo "imagem não chegou no POST";
    }

} else {
    http_response_code(403);
    echo "Método inválido";
}
?>