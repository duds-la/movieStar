<?php

    require_once("globals.php");
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);

    $userDao = new UserDAO($conn, $BASE_URL);

    // Resgata o tipo do formulário
    $type = filter_input(INPUT_POST, "type");

    $userData = $userDao->verifyToken();

    if($type ==="create"){
        //Receber os dados dos inputs
        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $lenght = filter_input(INPUT_POST, "lenght");


        $movie = new Movie();

        //Validação min. dados
        if(!empty($title) && !empty($description) && !empty($category)){

            $movie->title = $title;
            $movie->description = $description;
            $movie->trailer = $trailer;
            $movie->category = $category;
            $movie->lenght = $lenght;
            $movie->users_id = $userData->id;

            //Upload de imagem do filme 
            //upload da imagem
        if(isset($_FILES['image']) && !empty($_FILES["image"]["tmp_name"])){

            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpeg", "image/jpg"];

            //Checagem de tipo de imagem
            if(in_array($image["type"], $imageTypes)) {

                //Checar se JPG
                if(in_array($image, $jpgArray)) {

                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    //Se for Png
                } else {
                    $imageFile = imagecreatefrompng($image["tmp_name"]);
                }

                $imageName = $user->imageGenerateName();

                imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                $userData->image = $imageName;

            } else {
                $message->setMessage("Formato de imagem inválido!", "error", "back");
            }

        }

        $movieDao->create($movie);

        } else {

            $message->setMessage("Informações inválidas!", "error", "back");

        }

    } else {
        $message->setMessage("Informações inválidas!", "error", "index.php");
    }