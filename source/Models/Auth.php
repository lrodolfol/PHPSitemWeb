<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Models\User;
use Source\Core\View;
use Source\Support\Email;
use Source\Core\Session;

class Auth extends Model {

    private $linkConfirm;
    private $linkForget;

    public function getLinkForget() {
        return $this->linkForget;
    }

    public function setLinkForget($linkForget): void {
        $this->linkForget = $linkForget;
    }

    public static function user(): ?User {
        $session = new Session();
        if (!$session->has("authUser")) {
            return null;
        }

        return (new User())->findById($session->authUser);
    }

    public static function logout(): void {
        $session = new Session();
        $session->unset("authUser");
    }

    public function setLinkConfirm($linkConfirm): void {
        $this->linkConfirm = $linkConfirm;
    }

    public function getLinkConfirm() {
        return $this->linkConfirm;
    }

    public function __construct() {
        parent::__construct("user", ['id'], ['email', 'password']);
    }

    public function register(User $user): bool {
        if (!$user->save()) {
            $this->message = $user->message();
            return false;
        }

        $view = new View(__DIR__ . '/../../shared/views/email');
        $data = [
            'first_name' => $user->first_name,
            'confirm_link' => url('/obrigado/' . base64_encode($user->email))
        ];
        $this->setLinkConfirm($data['confirm_link']);

        $message = $view->render('confirm', $data);
        /*
          (new Email())->bootstrap(
          "Ative seu conta no " . CONF_SITE_NAME,
          $message, $user->email,
          "{$user->first_name} {$user->last_name}"
          )->send();
         */
        (new Email())->bootstrap(
                "Ative seu conta no " . CONF_SITE_NAME,
                $message, $user->email,
                "{$user->first_name} {$user->last_name}"
        )->sendEmailphp();

        return true;
    }

    public function login(string $email, string $password, bool $save = false): bool {
        if (!is_email($email)) {
            $this->message->warning("Email inválido");
            return false;
        }

        if ($save) {
            setcookie("authEmail", $email, time() + 604800, "/"); //1 SEMANA   60*60*24*7  .  "/" PARA TOSO SITE
        } else {
            setcookie("authEmail", null, time() - 3600, "/"); //1 SEMANA   60*60*24*7  .  "/" PARA TOSO SITE
        }

        if (!is_passwd($password)) {
            $this->message->warning("Senha inválida");
            return false;
        }


        $user = (new User())->findByEmail($email);
        if (!$user) {
            $this->message->error("O email informado não está cadastrado");
            return false;
        }

        if (!passwd_verify($password, $user->password)) {
            $this->message->error("A senha informada não confere");
            return false;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }


        (new Session())->set("authUser", $user->id);
        $this->message->success("Login efetuado com sucesso")->flash();

        return true;
    }

    public function forget(string $email) {
        $user = (new User())->findByEmail($email);
        if (!$user) {
            $this->message->warning("Email não cadastrado");
            return;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $view = new View(__DIR__ . '/../../shared/views/email');
        $data = [
            "first_name" => $user->first_name,
            "forget_link" => url("/recuperar/{$user->email}|{$user->forget}")
        ];

        $this->setLinkForget($data['forget_link']);
            
        $message = $view->render("forget", $data);
        (new Email())->bootstrap("Recupere sua senha", $message, $user->email, $user->first_name)->send();

        return true;
    }
    
    public function reset(string $email,  string $code, string $password, string $passwordRe) : bool {
         $user = (new User())->findByEmail($email);
        if (!$user) {
            $this->message->warning("Email não cadastrado");
            return false;
        }
        
        if ($user->forget != $code) {
            $this->message->error("código incorreto");
            return false;
        }
        
         if (!is_passwd($password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->info("Sua senha deve ter entre {$min} e {$max} caracteres.");
            return false;
        }

        if ($password != $passwordRe) {
            $this->message->warning("Você informou duas senhas diferentes.");
            return false;
        }

        $user->password = $password;
        $user->forget = null;
        $user->save();
        return true;
    }

}
