  <div class="ui container vertical">
    <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para acessar esta página.', 'remove circle', 'error'); else: ?>
      <? if(!get('code')): ?>
      <?= Settings::sendMailActivation(); ?>
      <div class="ui basic segment">
        <h3 class="header">Ative sua conta</h3>
        Caso não tenha chegado email algum, clique no botão e enviaremos um novo email pra você.
        <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
          <button type="submit" class="ui black button" name="send-mail">Enviar e-mail.</button>
        </form>
      </div>
      <? else: ?>
      <?= Settings::checkCode(); ?>
      <? endif; ?>
    <? endif; ?>
  </div>