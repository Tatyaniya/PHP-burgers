<?php

class Burger
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __conctract()
    {
        $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    }

    /**
     * @param $inputData array
     */
    public function run($inputData)
    {
        // Тут мы проверяем данные и приводим к нудному формату
        $data = $this->valider($inputData);
        // получаем пользователя по электронному адресу
        $userId = $this->getUser($data['email']);
        // Такого пользователя у нас нет
        if (empty($userId)) {
            // Добавляем новго пользователя
            $this->createUser($data);
            // получаем пользователя которого только добавили
            $userId = $this->getUser($data['email']);
            // Добавляем новый заказ
            $this->addOrder($userId, $data);
        }
        // Добавляем новый заказ
        $this->addOrder($userId, $data);
        // обновляем счетчик заказов
        $this->countOrders($userId);
    }


    public function valider($data)
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $data;
        } else {
            return "Некорректный email";
        }
    }

    /**
     * @param $data
     */
    protected function createUser($data)
    {
        $query = ("INSERT INTO `users` (email, `name`, phone) VALUES (:email, :name, :phone)");
        $result = $this->pdo->prepare($query);
        $result->execute([
            'email' => $data['email'],
            'name' => $data['name'],
            'phone' => $data['phone']
        ]);
    }

    /**
     * @param $userId
     * @param array $data
     */
    protected function addOrder($userId, array $data)
    {
        $addressField = ['street', 'home', 'part', 'appt', 'floor'];
        $address = '';

        foreach ($data as $field => $value) {
            if ($value && in_array($field, $addressField)) {
                $address .= $value . ', ';
            }
        }
        $addressString = rtrim($address, ', ');

        $query = "INSERT INTO `orders` (user_id, address) VALUES (:user_id, :address)";
        $result = $this->pdo->prepare($query);
        $result->execute([
            'user_id' => $userId,
            'address' => $addressString
        ]);
    }

    /**
     * @param $email
     * @return mixed
     */
    protected function getUser($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->pdo->prepare($query);
        $result->execute([
            'email' => $email
        ]);

        if ($result) {
            // получить id
            $user = $result->fetch(PDO::FETCH_ASSOC);
            return $userId = $user['id'];
        }
    }

    /**
     * @param $userId
     */
    protected function countOrders($userId)
    {
        $query = "UPDATE users SET count_orders = count_orders +1 WHERE id = $userId";
        $this->pdo->query($query);
    }
}













