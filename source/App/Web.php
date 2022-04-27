<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Support\Pager;
use Source\Models\User;
use Source\Models\Auth;
use Source\Models\Faq\Channel;
use Source\Models\Faq\Question;
use Source\Models\Post;
use Source\Models\Category;
use Source\Models\BlockedEmail;
use Source\Core\Session;
use Source\Models\Report\{
    Online,
    Access
};

class Web extends Controller {

    public function __construct() {
        /* PARA MANUTENÇÃO DO SITE
         * \Source\Core\Connect::getInstance();
         * redirect("/ops/manutencao");
         */

        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");

        /* $block = (new BlockedEmail())->block("rodolfo0ti@gmail.com");
          var_dump($block); */

        (new \Source\Models\Report\Access())->report();
        (new Online())->report();

        /*
          $online = new Online();
          var_dump($online->findByActive(true), $online->findByActive()); */

        /* $mail =new \Source\Support\Email();
          $mail->bootstrap("teste de fila de email . " . time(),
          "teste de envio de email",
          "rodolf0ti@gmail.com",
          "rodolfo j.silva")->send();

          var_dump($mail); */
    }

    public function home(): void {


        echo 'OLA MUNDO!';

        //$user->bootstrap("Rodolfo", "J.Silva", "ceo@tinosnegocios.com.br", "12345678");

        /* $user = (new User())->findByEmail("ceo@tinosnegocios.com.br");
          $user->document = 13458245200;
          $user->save();

          var_dump($user);
         */
        $head = $this->seo->render(
                "Descubra o " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/home"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("home", [
            "head" => $head,
            "video" => "lDZGl9Wdc7Y",
            "blog" => (new Post())
                    ->find()
                    ->order("post_at DESC")
                    ->limit(6)->fetch(true)
        ]);
    }

    public function about(): void {
        /*
          $model = (new Question())->find()->fetch(true);
          var_dump($model);
         */
        $head = $this->seo->render(
                "Descubra o " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/sobre"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("about", [
            "head" => $head,
            "video" => "lDZGl9Wdc7Y",
            "faq" => (new Question())
                    ->find("channel_id = :id", "id=1", "question, response")
                    ->order("order_by")
                    ->fetch(true)
        ]);
    }

    public function teste(): void {
        //echo 'ola mundo';
        //$this->view->render("teste", []);



        echo $this->view->render('teste', ["head" => "cabeçalho"]);
    }

    public function testePost(array $data): void {

        var_dump($data);
    }

    public function testeTwo() {
        echo 'ola mundo';
    }

    public function blog(?array $data): void {
        $head = $this->seo->render(
                "Blog - " . CONF_SITE_NAME,
                "Confira em nosso blog dicas e sacadas de como controlar melhorar suas contas. Vamos tomar um café?",
                url("/blog"),
                theme("/assets/images/share.jpg")
        );

        $blog = (new Post())->find();
        $pager = new Pager(url("/blog/p/"));
        $pager->pager($blog->count(), 9, ($data['page'] ?? 1));

        echo $this->view->render("blog", [
            "head" => $head,
            "blog" => $blog->limit($pager->limit())->offset($pager->offset())->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    public function blogPost(?array $data): void {
        $post = (new Post())->findByUri($data['uri']);
        if (!$post) {
            redirect("/404");
        }

        $post->views += 1;
        $post->save();

        $head = $this->seo->render(
                "{$post->title} - " . CONF_SITE_NAME,
                "{$post->subtitle}",
                url("/blog/{$post->uri}"),
                "null"
        );

        echo $this->view->render("blog-post", [
            "head" => $head,
            "post" => $post,
            "related" => (new Post())
                    ->find("category =:c AND id != :i", "c={$post->category}&i={$post->id}")
                    ->order("rand()")
                    ->limit(3)
                    ->fetch(true)
        ]);
    }

    /*
     * ESSE METODOS ESTA CARREGANDO TANTO PARA METODOS POST COMO GET. 
     * TINHA QUE SER UM METODO PARA O GET E UM METODOS PARA O POST. SEPARADOS!!
     * NESSA O ROBSON PECOU FEIO.
     */

    public function blogSearch(array $data): void {
        /*
         * ESSE METODOS ESTA CARREGANDO TANTO PARA METODOS POST COMO GET. 
         * TINHA QUE SER UM METODO PARA O GET E UM METODOS PARA O POST. SEPARADOS!!
         * NESSA O ROBSON PECOU FEIO.
         */
        /* if (!empty($data['s'])) {
          $search = filter_var($data['s'], FILTER_SANITIZE_STRIPPED);
          redirect(url("/blog/buscar/{$search}/1"));
          return;
          } */
        if (!empty($data['s'])) {
            $search = filter_var($data['s'], FILTER_SANITIZE_STRIPPED);
            echo json_encode(["redirect" => url("/blog/buscar/{$search}/1")]);
            return;
        }

        if (empty($data['terms'])) {
            redirect("/blog");
        }

        $search = filter_var($data['terms'], FILTER_SANITIZE_STRIPPED);
        $page = (filter_var($data['page'], FILTER_VALIDATE_INT) >= 1 ? $data['page'] : 1);

        $head = $this->seo->render(
                "Pesquisa por {$search} - " . CONF_SITE_NAME,
                "Confira os resultados de sua pesquisa para {$search}",
                url("/blog/buscar/{$search}/{$page}"),
                theme("/assets/images/share.jpg")
        );

        //PARA QUE O MATCH E AGAINST FUNCIONE, É PRECISO SETAR OS CAMPOS TITLE E SUBTITLE COMO FULLTEXT NO BANCO DE DADOS
        $blogSearch = (new Post())->find("MATCH(title, subtitle) AGAINST(:S)", "S={$search}");

        if (!$blogSearch->count()) {
            echo $this->view->render("blog", [
                "head" => $head,
                "title" => "PESQUISA POR:",
                "search" => $search
            ]);
            return;
        }

        $pager = new Pager(url("/blog/buscar/{$search}/"));
        $pager->pager($blogSearch->count(), 9, $page);

        echo $this->view->render("blog", [
            "head" => $head,
            "title" => "PESQUISA POR:",
            "search" => $search,
            "blog" => $blogSearch->limit($pager->limit())->offset($pager->offset())->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    function blogCategory(array $data): void {
        $categoryUri = filter_var($data['category'], FILTER_SANITIZE_STRIPPED);
        $category = (new Category())->findByUri($categoryUri);

        if (!$category) {
            redirect('/blog');
        }

        $blogCategory = (new Post())->find("category = :c", "c={$category->id}");
        $page = (!empty($data['page']) && filter_var($data['page'], FILTER_VALIDATE_INT) >= 1 ? $data['page'] : 1);
        $pager = new Pager(url("/blog/em/{$category->uri}/"));
        $pager->pager($blogCategory->count(), 9, $page);

        $head = $this->seo->render(
                "Artigos em {$category->uri} - " . CONF_SITE_NAME,
                $category->description,
                url("/blog/em/{$category->uri}/{$page}"),
                ($category->cover ? image() : theme("/assets/images/shared.jpg"))
        );

        echo $this->view->render('blog', [
            "head" => $head,
            "title" => "Artigos em {$category->uri} - ",
            "desc" => $category->description,
            "blog" => $blogCategory
                    ->limit($pager->limit())
                    ->offset($pager->offset())
                    ->order("post_at DESC")
                    ->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    public function forget(?array $data) {

        if (Auth::user()) {
            redirect('/app');
        }

        if (!empty($data['csrf'])) {
            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor use o formulário")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['email'])) {
                $json["message"] = $this->message->error("Informe seu email corretamente")->render();
                echo json_encode($json);
                return;
            }

            //TALVEZ ESSA FUNÇÃO NÃO SEJA TAO LEGAL, POIS O USUARIO PODE QUERER ENVIAR A RECUPERAÇÃO NOVAMENTE
            //POR N MOTIVOS QUE SÃO SATISFATORIOS
            if (request_repeat("webForget", $data['email'])) {
                $json["message"] = $this->message->error("email já informado")->before("Oops ")->render();
                echo json_encode($json);
                return;
            }

            $auth = new Auth();
            if ($auth->forget($data['email'])) {
                $json['message'] = $this->message->success("Acesse seu email para recuperar :). {$auth->getLinkForget()}")->render();
            } else {
                $json['message'] = $auth->message()->before("Oops ")->render();
            }

            echo json_encode($json);
            return;
        }

        //PAGA GET
        $head = $this->seo->render(
                "Recuperar Senha " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/recuperar"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-forget", [
            "head" => $head
        ]);
    }

    public function reset(array $data): void {

        if (Auth::user()) {
            redirect('/app');
        }

        if (!empty($data['csrf'])) {
            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor use o formulário")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data["password"]) || empty($data["password_re"])) {
                $json["message"] = $this->message->info("Informe e repita a senha para continuar")->render();
                echo json_encode($json);
                return;
            }

            list($email, $code) = explode("|", $data["code"]);
            $auth = new Auth();

            if ($auth->reset($email, $code, $data["password"], $data["password_re"])) {
                $this->message->success("Senha alterada com sucesso. Vamos controlar?")->flash();
                $json["redirect"] = url("/entrar");
            } else {
                $json["message"] = $auth->message()->before("Oops")->render();
            }

            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
                "Cria sua nova senha no " . CONF_SITE_NAME,
                CONF_SITE_DESC,
                url("/recuperar"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-reset", [
            "head" => $head,
            "code" => $data['code']
        ]);
    }

    public function register(?array $data): void {

        if (Auth::user()) {
            redirect('/app');
        }

        if (!empty($data['csrf'])) {
            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor usar o formulário")->render();
                echo json_encode($json);
                return;
            }

            if (in_array("", $data)) {
                $json['message'] = $this->message->info("Informe seus dados para criar sua conta.")->render();
                echo json_encode($json);
                return;
            }

            $auth = new Auth();
            $user = new User();
            $user->bootstrap(
                    $data["first_name"],
                    $data["last_name"],
                    $data["email"],
                    $data["password"]
            );

            if ($auth->register($user)) {
                $json['message'] = $auth->getLinkConfirm();
                echo json_encode($json);
                return;
                //$json['redirect'] = url("/confirma");
            } else {
                $json['message'] = $auth->message()->render();
            }

            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
                "Criar Conta - " . CONF_SITE_NAME,
                CONF_SITE_DESC,
                url("/cadastrar"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-register", [
            "head" => $head
        ]);
    }

    public function confirm() {
        $head = $this->seo->render(
                "Confirme seu cadastro " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/confirma"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("optin", [
            "head" => $head,
            "data" => (Object) [
                "title" => "Falta muito pouco! Confirme seu cadastro.",
                "desc" => "Enviamos um link de confirmação para seu e-mail. Acesse e siga as instruções para concluir seu cadastro e comece a controlar com o CaféControl",
                "image" => theme("/assets/images/optin-confirm.jpg")
            ]
        ]);
    }

    public function success(array $data): void {

        $email = base64_decode($data['email']);
        $user = (new User())->findByEmail($email);

        if (($user) && $user->status != 'confirmed') {
            $user->status = "confirmed";
            $user->save();
        }

        $head = $this->seo->render(
                "Bem vindo ao " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/obrigado"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("optin", [
            "head" => $head,
            "data" => (Object) [
                "title" => "Tudo pronto. Você já pode controlar :)",
                "desc" => "Bem-vindo(a) ao seu controle de contas, vamos tomar um café?",
                "image" => theme("/assets/images/optin-success.jpg"),
                "link" => url('/entrar'),
                "linkTitle" => "Fazer Login"
            ]
        ]);
    }

    /*
     * ESSE METODOS ESTA CARREGANDO TANTO PARA METODOS POST COMO GET. 
     * TINHA QUE SER UM METODO PARA O GET E UM METODOS PARA O POST. SEPARADOS!!
     * NESSA O ROBSON PECOU FEIO.
     */

    public function login(array $data) {
        if (Auth::user()) {
            redirect('/app');
        }

        if (!empty($data['csrf'])) {
            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor usar o formulário")->render();
                echo json_encode($json);
                return;
            }

            //CHECKED BLOCKED EMAIL
            if ((new BlockedEmail())->findBlocked($data['email'])) {
                $json['message'] = $this->message->error("Para sua segurança esse email bloqueado devido muitas tentativas de login. Entre em contato com nosso suporte")->render();
                echo json_encode($json);
                return;
            }

            //LIMIT REQUEST
            if (request_limit("weblogin", 3, (5))) {
                //SE AGUARDOU 5 MINTUTOS POR 3 VEZES, BLOQUEIA O USUARIO
                $countRequest = request_error_count();
                if ($countRequest >= 12) {
                    (new BlockedEmail())->block($data['email']);

                    $json['message'] = $this->message->error("Para sua segurança esse email bloqueado devido muitas tentativas de login. Entre em contato com nosso suporte")->render();
                    echo json_encode($json);
                    return;
                }

                $json['message'] = $this->message->error("Você já efetuou 3 tentativas, esse é o limite. Por favor, aguarde 5 minutos para tentar novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['email']) || empty($data['password'])) {
                $json['message'] = $this->message->warning("Informe seu email e senha para entrar")->render();
                echo json_encode($json);
                return;
            }

            $save = (isset($data['save']) ? true : false);
            $auth = new Auth();
            $login = $auth->login($data['email'], $data['password'], $save);

            if ($login) {
                unset_request();
                $json['redirect'] = url("/app");
            } else {
                $json['message'] = $auth->message()->before("Oops! ")->render();
            }

            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
                "Entrar - " . CONF_SITE_NAME,
                CONF_SITE_DESC,
                url("/entrar"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-login", [
            "head" => $head,
            "cookie" => filter_input(INPUT_COOKIE, "authEmail")
        ]);
    }

    public function terms() {
        $head = $this->seo->render(
                CONF_SITE_NAME . " - Termos para você " . CONF_SITE_DESC,
                CONF_SITE_DESC,
                url("/termos"),
                theme("/assets/images/share.jpg")
        );

        echo $this->view->render("blog-post", [
            "head" => $head,
            "nome" => 'rodolfo de jesus silva'
        ]);
    }

    public function error(array $data): void {
        $error = new \stdClass();

        switch ($data['errcode']) {
            case "problemas":
                $error->code = "Ops";
                $error->title = "Estamos enfrentando problemas";
                $error->message = "Parece que nosso serviço não está disponivel no momento. Envie um e-mail se for urgente";
                $error->linkTitle = "ENVIAR E-MAIL";
                $error->link = "mailto:" . CONF_MAIL_SUPPORT;
                break;
            case "manutencao":
                $error->code = "Ops";
                $error->title = "Nosso sistema está em manutenção. Volte novamente mais tarde";
                $error->message = "Voltamos logo! estamos trabalhando para melhorar nossso conteudo e voce poder controlar melhor suas contas";
                $error->linkTitle = null;
                $error->link = null;
                break;
            default:
                $error->code = $data['errcode'];
                $error->title = "Ops, conteudo indisponivel :/";
                $error->message = "bla balbablablablablalblaablablablablablab";
                $error->linkTitle = "Continue navegando";
                $error->link = url_back();
                break;
        }

        $head = $this->seo->render(
                "{$error->code} | {$error->title}",
                $error->message,
                url("ops/{$error->code}"),
                url("/assets/images/shared.jpg"),
                false
        );

        echo $this->view->render("error", [
            "head" => $head,
            "error" => $error
        ]);
    }

}
