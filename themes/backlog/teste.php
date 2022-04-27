<?php $v->layout('theme'); ?>


<form class="auth_form ajax_off" action="<?= url('/testeForm'); ?>" method="post" enctype="multipart/form-data">
    <div>
        <label for="nome">Nome:</label>
        <input type="text" name="name" id="nome" />
    </div>
    <div>
        <label for="email">E-mail:</label>
        <input type="email" name="id" id="email" />
    </div>
    <div>
        <label for="msg">Mensagem:</label>
        <textarea name="mensagem" id="msg"></textarea>
    </div>
    
    <button class="auth_form_btn transition gradient gradient-green gradient-hover">Entrar</button>
        
    
</form>