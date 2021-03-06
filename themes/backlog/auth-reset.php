<?php $v->layout("theme"); ?>

<article class="auth">
    <div class="auth_content container content">
        <header class="auth_header">
            <h1>Criar nova senha</h1>
            <p>Informe e repita uma nova senha para recuperar  o acesso.</p>
        </header>

        <form class="auth_form" data-reset="true" action="<?= url("/recuperar/resetar"); ?>" method="post" enctype="multipart/form-data">
            <div class="ajax_response"> <?= flash(); ?> </div>
            <?= csrf_input(); ?>
            <input type="text" name="code" value="<?= $code; ?>"/>
            <label>
                <div class="unlock-alt">
                    <span class="icon-envelope">Nova senha:</span>
                    <span><a title="Voltar e entrar" href="<?= url("/entrar"); ?>">Voltar e entrar!</a></span>
                </div>
                <input required type="password" name="password" placeholder="nova senha:"/>
            </label>
            
             <label>
                <div class="unlock-alt">
                    <span class="icon-envelope">Repita a senha:</span>
                </div>
                <input required type="password" name="password_re" placeholder="repita a nova senha:"/>
            </label>

            <button class="auth_form_btn transition gradient gradient-green gradient-hover">Alterar</button>
        </form>
    </div>
</article>