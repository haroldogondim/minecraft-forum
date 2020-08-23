<div class="ui container vertical" id="topic-create">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para acessar esta página.', 'remove circle', 'error'); else: ?>
  <div class="ui basic segment">
    <div class="ui warning message">
      <div class="header">Atenção!</div>
      <ul class="list">
        <li>Caso você altere o email, sua conta ficará restrita até você confirmar seu novo e-mail.</li>
      </ul>
    </div>
    <?= Settings::my(); ?>
    <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
      <div class="field">
        <label>Nome</label>
        <input type="text" name="name" maxlength="100" value="<?= User::data('name'); ?>">
      </div>
      <div class="field">
        <label>E-mail</label>
        <input type="text" name="email" maxlength="64" value="<?= User::data('email'); ?>">
      </div>
      <button class="ui blue button" type="submit" name="submit-my"><i class="reply icon"></i> Enviar</button>
    </form>
  </div>
  <? endif; ?>
</div>