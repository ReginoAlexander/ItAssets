<?php

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';

    public function findByUsername($username){
        $stmt = $this->query(
            "SELECT *  FROM {$this->table} WHERE username = ?",
            [$username]
        );
        return $stmt->fetch();
    }

    public function login($username,  $password){
        $user = $this->findByUsername($username);

        if(!$user){
            return false;
        }

        if($user['password'] !== $password){
            return false;
        }

        return $user;
    }
}

?>