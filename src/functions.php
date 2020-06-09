<?php

class Burger
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param $inputData array
     */
    public function run($inputData)
    {
        $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

        // Тут мы проверяем данные и приводим к нудному формату
        $data = $this->valider($inputData);

        if (isset($data['email'])) {
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
                // выводим сообщение
                return $message = $this->descOrder($userId);
            }
            // Добавляем новый заказ
            $this->addOrder($userId, $data);
            // обновляем счетчик заказов
            $this->countOrders($userId);
            // выводим сообщение
            return $message = $this->descOrder($userId);
            //var_dump($message);
        }

    }

    /**
     * @param $data
     * @return string
     */
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

        $query = ("INSERT INTO `users` (email, `name`, phone) VALUES (:email, :name, :phone)");
        $result = $this->pdo->prepare($query);
        $result->execute([
            'email' => $email,
            'name' => $name,
            'phone' => $phone
        ]);
    }

    /**
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

        $addressString = "Улица $street, дом $home, корпус $part, кв. $appt , этаж $floor";

        $query = "INSERT INTO `orders` (user_id, create_time, address) VALUES (:user_id, :create_time, :address)";
        $result = $this->pdo->prepare($query);
        $result->execute([
            'user_id' => $userId,
            'create_time' => date('Y-m-d H:i:s'),
            'address' => $addressString
        ]);
    }

    /**
     * @param $email
     * @return mixed
     */
    protected function getUser($email)
    {
        if (isset($email)) {
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
    }

    /**
     * @param $userId
     */
    protected function countOrders($userId)
    {
        $query = "UPDATE users SET count_orders = count_orders +1 WHERE id = $userId";
        $this->pdo->query($query);
    }

    /**
     * @param $userId
     * @return string
     */
    protected function descOrder($userId) {
        $queryUser = $this->pdo->query("SELECT * FROM users WHERE id = $userId");
        $queryOrder = $this->pdo->query("SELECT * FROM orders WHERE user_id = $userId");

        $user = $queryUser->fetch(PDO::FETCH_ASSOC);
        $order = $queryOrder->fetch(PDO::FETCH_ASSOC);


        $address = $order['address'];
        $countOrder = $user['count_orders'];

        $idOrder = $order['id'];

        return "Спасибо, ваш заказ будет доставлен по адресу: $address <br>
        Номер вашего заказа: #$idOrder <br>
        Это ваш $countOrder-й заказ!";
    }
}













