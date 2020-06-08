<?php

function getConnection()
{
    $host = DB_HOST;
    $dbName = DB_NAME;
    $dbUser = DB_USER;
    $dbPassword = DB_PASSWORD;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPassword);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die;
    }

    return $pdo;
}


//function exec($query, $params = []) {
//    $db = getConnection();
//    $prepared = $db->prepare($query);
//    return $prepared->execute($params);
//}

function createUser($email, $name, $phone) {
    $db = getConnection();
    $query = ("INSERT INTO `users` (email, `name`, phone) VALUES (:email, :`name`, :phone)");
    $result = $db->prepare($query);
    $result->execute(["user_email" => $email]);


//    $result = $db->exec($query, [
//        ':email' => $email,
//        ':name' => $name,
//        ':phone' => $phone
//    ]);
//    if (!$result) {
//        return false;
//    }
}



function addOrder($userId, array $data) {
    $db = getConnection();
    $query = "INSERT INTO users(user_id, address, create_time) VALUES (:address)";

}

















