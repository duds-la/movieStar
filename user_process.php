<?php

    require_once("globals.php");
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);

    $userDao = new UserDAO($conn, $BASE_URL);

    // Resgata o tipo do formulário
    $type = filter_input(INPUT_POST, "type");

    //Atualizar user
    if($type === "update"){
        //Resgata dados do usuário
        $userData = $userDao->verifyToken();

        //Receber dados do post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        //Criar um novo objeto de usuário
        $user = new User();

        //Preencher os dados do user
        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

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

        $userDao->update($userData);


        //Atualizar senha
    } else if($type ==="changepassword") {
        //Receber dados do post
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
        $id = filter_input(INPUT_POST, "id");

        if($password === $confirmpassword){

            //Criar um novo objeto de usuário
            $user = new User();

            $finalPassword = $user->generatePassword($password);

            $user->password = $finalPassword;
            $user->id = $id;

            $userDao->changePassword($user);
            

        } else {
            $message->setMessage("Senhas nã são iguais!", "error", "index.php");
        }
        


    } else {
        $message->setMessage("Informações inválidas!", "error", "index.php");
    }