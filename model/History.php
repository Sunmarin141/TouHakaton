<?php

require_once 'Database.php';

class History extends Database{

    //получаем все поля истории запросов для отображение
    public function getHistory($user_id){
        $stmt = $this->conn->prepare('SELECT id,file_path,file_type,original_text,created_at,title
        FROM history
        WHERE user_id = ?
        ORDER BY created_at DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentHistory($user_id,$history_id){
        $stmt = $this->conn->prepare('SELECT * FROM history WHERE user_id = ? AND id = ?');
        $stmt->execute([$user_id,$history_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveData($user_id,$file_path,$file_type,$original,$ru,$kz,$en,$title){
        $stmt = $this->conn->prepare('INSERT INTO history (user_id,file_path,file_type,original_text,translated_ru,
        translated_kz,translated_en,title) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$user_id,$file_path,$file_type,$original,$ru,$kz,$en,$title]);
        $post_id = $this->conn->lastInsertId();
        return $this->getCurrentHistory($user_id,$post_id);
    }
}