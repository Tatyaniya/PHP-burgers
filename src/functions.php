<?php

class Burger
{
    /**
     * @var PDO
     */
    private $pdo;
    public $message;

    /**
     * геттер для сообщения
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * запускаем весь процесс
     * @param $inputData array
     */
    public function run($inputData)
    {

        $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

        // Тут мы проверяем данные и приводим к нужному формату
        $data = $this->valider($inputData);

        if (!is_array($data)) {
            echo 'Некорректно указан емейл';
            exit();
        }
        // получаем пользователя по электронному адресу
        $userId = $this->getUser($data['email']);


        // Такого пользователя у нас нет
        if (empty($userId)) {
            // Добавляем новго пользователя
            $this->createUser($data);
            // получаем пользователя которого только добавили
            $userId = $this->getUser($data['email']);
        }
        // Добавляем новый заказ
        $this->addOrder($userId, $data);
        // обновляем счетчик заказов
        $this->countOrders($userId);
        // выводим сообщение
        $info = $this->descOrder($userId);
        $this->message = $this->sendMessage($info);
    }

    /**
     * проверяем емейл на корректность
     * @param $data
     * @return string
     */
    public function valider($data)
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $data;
        }
        return "Некорректный email";
    }

    /**
     * создаем пользователя
     * @param $data
     */
    protected function createUser($data)
    {
        if (isset($data['email'])) {
            $email = $data['email'];
        } else {
            $email = 0;
        }
        if (isset($data['name'])) {
            $name = $data['name'];
        } else {
            $name = 0;
        }
        if (isset($data['phone'])) {
            $phone = $data['phone'];
        } else {
            $phone = 0;
        }

        $query = ("INSERT INTO `users` (email, count_orders, `name`, phone) VALUES (:email, :count_orders, :name, :phone)");
        $result = $this->pdo->prepare($query);
        $result->execute([
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'count_orders' => 0
        ]);
    }

    /**
     * добавляем заказ
     * @param $userId
     * @param array $data
     */
    protected function addOrder($userId, $data)
    {
        if (isset($data['street'])) {
            $street = $data['street'];
        } else {
            $street = 0;
        }
        if (isset($data['home'])) {
            $home = $data['home'];
        } else {
            $home = 0;
        }
        if (isset($data['part'])) {
            $part = $data['part'];
        } else {
            $part = 0;
        }
        if (isset($data['appt'])) {
            $appt = $data['appt'];
        } else {
            $appt = 0;
        }
        if (isset($data['floor'])) {
            $floor = $data['floor'];
        } else {
            $floor = 0;
        }

        $addressString = " ул. $street, дом $home, корпус $part, кв. $appt , этаж $floor";

        $query = "INSERT INTO orders (user_id, create_time, address) VALUES (:user_id, :create_time, :address)";
        $result = $this->pdo->prepare($query);
        $result->execute([
            'user_id' => $userId,
            'create_time' => date('Y-m-d H:i:s'),
            'address' => $addressString
        ]);
    }

    /**
     * получаем пользователя по емейл
     * @param $email
     * @return mixed
     */
    protected function getUser($email)
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $result = $this->pdo->prepare($query);
        $result->execute([
            'email' => $email
        ]);

        $user = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($user['id'])) {
            return $user['id'];
        }
        return null;
    }

    /**
     * добавляем к счетчику заказов еще 1
     * @param $userId
     */
    protected function countOrders($userId)
    {
        $query = "UPDATE users SET count_orders = count_orders + 1 WHERE id = $userId";
        $this->pdo->query($query);
    }

    /**
     * берем из базы последнего пользователя, который добавил заказ
     * @param $userId
     * @return string
     */
    protected function descOrder($userId)
    {
        $sql = "SELECT * FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE user_id = :user_id ORDER BY orders.id DESC LIMIT 1";

        $result = $this->pdo->prepare($sql);
        $result->execute([
            'user_id' => $userId
        ]);

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * выводим сообщение
     * @param array $info
     * @return string
     */
    protected function sendMessage(array $info)
    {
        $countOrder = $info['count_orders'];

        $address = $info['address'];
        $idOrder = $info['id'];

        return "Спасибо, ваш заказ будет доставлен по адресу: $address <br>
        Номер вашего заказа: #$idOrder <br>
        Это ваш $countOrder-й заказ!";
    }
}
