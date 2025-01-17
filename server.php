<?php
date_default_timezone_set('America/Sao_Paulo');
function loginCheck($mycon, $email, $pass, $ip){
    $emailCheckQuery = $mycon->prepare("SELECT Id, Email, Password FROM client WHERE Email = :email");
    $emailCheckQuery->bindParam(':email', $email);
    $emailCheckQuery->execute();
    if($emailCheckQuery->rowCount() > 0){
        $emailCheckData = $emailCheckQuery->fetch();
        if($pass == $emailCheckData['Password']){
            http_response_code(201);
        }else{
            http_response_code(400);
        }
    }else{
        http_response_code(400);
    }
}
function requestCheck($mycon, $email, $pass, $ip){
    $request_selectQuery = $mycon->prepare("SELECT * FROM request WHERE Ip = :ip AND RequestDate >= NOW() - INTERVAL 5 MINUTE");
    $request_selectQuery->bindParam(":ip", $ip);
    $request_selectQuery->execute();
    if($request_selectQuery->rowCount() >= 10){
        $UnbanDate = time() + (15 * 60);
        $UnbanDate = date("Y-m-d H:i:s", $UnbanDate);
        $banQuery = $mycon->prepare("INSERT INTO blacklist (Ip, UnbanDate) VALUES (:ip, :UnbanDate)");
        $banQuery->bindParam(':ip', $ip); 
        $banQuery->bindParam(':UnbanDate', $UnbanDate);
        $banQuery->execute();
        http_response_code(429);
    }else{
        $insertRequest = $mycon->prepare("INSERT INTO request (Ip) VALUES (:ip)");
        $insertRequest->bindParam(":ip", $ip);
        $insertRequest->execute();
        loginCheck($mycon, $email, $pass, $ip);
    }
}
if(!isset($_SESSION)){
    session_start();
}

if(isset($_POST['email'])){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    try{
        $mycon = new PDO("mysql:host=localhost;dbname=loginratelimiting", 'root', '');
        $isban = $mycon->prepare("SELECT UnbanDate FROM blacklist WHERE Ip = :ip");
        $isban->bindParam(":ip", $ip);
        $isban->execute();
        if($isban->rowCount() > 0){
            $isbanData = $isban->fetch();
            $isbanDate = $isbanData['UnbanDate'];
            $isbanDate = strtotime($isbanDate);
            if($isbanDate <= time()){
                $banremove = $mycon->prepare("DELETE FROM blacklist WHERE Ip = :ip");
                $banremove->bindParam(':ip', $ip);
                $banremove->execute();
                requestCheck($mycon, $email, $pass, $ip);
            }else{
                http_response_code(429);
            }
        }else{
            requestCheck($mycon, $email, $pass, $ip);
        }
    }catch(PDOException){
        http_response_code(500);
    }
}