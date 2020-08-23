<? 
Core::includeController('settings'); 
$letters = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);
?>
<div class="ui container vertical" id="topic-create">
  <div class="ui basic segment">
    <?= Settings::register(); ?>
    <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
      <div class="required field">
        <label>Usuário</label>
        <input type="text" name="username" maxlength="16" value="<?= post('username'); ?>">
      </div>
      <div class="field">
        <label>Nome</label>
        <input type="text" name="name" maxlength="100" value="<?= post('name'); ?>">
      </div>
      <div class="required field">
        <label>Nova senha</label>
        <input type="password" name="password" maxlength="20">
      </div>
      <div class="required field">
        <label>Repita sua nova senha</label>
        <input type="password" name="password_repeat" maxlength="20">
      </div>
      <div class="required field">
        <label>E-mail</label>
        <input type="text" name="email" maxlength="64" value="<?= post('email'); ?>">
      </div>
      <div class="required field">
        <label>Repita seu e-mail</label>
        <input type="text" name="email_repeat" maxlength="64" value="<?= post('email_repeat'); ?>">
      </div>
      <div class="required field">
        <label>Confirme o código de segurança: <?= $letters; ?></label>
        <input type="text" name="code_register" maxlength="4" value="">
        <input type="hidden" name="code_hidden" maxlength="4" value="<?= $letters; ?>">
      </div>
      <button class="ui blue button" type="submit" name="submit-register"><i class="reply icon"></i> Enviar</button>
    </form>
  </div>
</div>