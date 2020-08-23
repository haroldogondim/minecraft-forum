<div class="ui container vertical" id="topic-create">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para acessar esta página.', 'remove circle', 'error'); else: ?>
  <div class="ui basic segment">
    <?= Settings::password(); ?>
    <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
      <div class="field">
        <label>Senha atual</label>
        <input type="password" name="password" maxlength="20">
      </div>
      <div class="field">
        <label>Nova senha</label>
        <input type="password" name="new_password" maxlength="20">
      </div>
      <div class="field">
        <label>Repita sua nova senha</label>
        <input type="password" name="repeat_password" maxlength="20">
      </div>
      <button class="ui blue button" type="submit" name="submit-password"><i class="reply icon"></i> Enviar</button>
    </form>
  </div>
  <? endif; ?>
</div>