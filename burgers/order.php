<?php

include '../src/config.php';
include '../src/functions.php';


$email = $_POST['email'];
$name = $_POST['name'];
$phone = $_POST['phone'];

$addressField = ['street', 'home', 'part', 'appt', 'floor'];
$address = '';

foreach ( $_POST as $field => $value) {
    if ($value && in_array($field, $addressField)) {
        $address .= $value . ', ';
    }
}
$addressString = rtrim($address, ', ');

$data = ['address' => $address];

//var_dump($email);
//var_dump($name);
//var_dump($phone);
//var_dump($addressString);

createUser($email, $name, $phone);

$db = getConnection();


$ret = $db->query("SELECT * FROM users");

$user = $ret->fetchAll($db::FETCH_ASSOC);
echo '<pre>' . print_r($user, 1) . '</pre>';

//$orderId = addOrder($userId, $data);

//createUser($email, $name, $phone);
//$userEmail = getUserEmail($email);


//if ($email) { // если такое мыло уже существует
//    $userId = $user['id']; // если пользователь есть, то id берется из данных пользователя
//    countOrders($user['id']); // увеличиваем кол-во заказов
//    $orderNumber = $user['count_orders'] + 1;
//} else { // если пользователя нет, он создается
//    $orderNumber = 1;
//    $userId = createUser($email, $name, $phone);
//}
//
//
//createOrder($userId, $data, $phone);

//echo "Спасибо, ваш заказ будет доставлен по адресу: $addressString<br>";








