<?php
require_once 'Database.php';

class User extends Database{
    //регистрация аккаунта
    public function registrationUser ($username,$password){
        $stmt = $this->conn->prepare('INSERT INTO users (username,password) VALUES (?,?)');
        $stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT)]);
        $id_user = $this->conn->lastInsertId();
        return $this->getUserById($id_user);
    }

    //вход в аккаунт
    public function loginUser($username,$password){
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($password,$user['password'])){
            return $user;
        }else{
            return false;
        }
    }

    //получать пользователя по айди  
    private function getUserById($id){
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

}