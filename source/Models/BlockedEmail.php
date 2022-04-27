<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Models\User;

class BlockedEmail extends Model {

    public function __construct() {
        parent::__construct('blocked_emails', ['id'], ['email']);
    }
    
    public function findBlocked($email) : bool {
        $find = $this->find("email = :email", "email={$email}");
        $all = $find->fetch();
        
        return $all ? true : false;
    }

    public function block(string $email): bool {
        $this->email = $email;
        $this->create($this->safe());

        return true;
    }

}
