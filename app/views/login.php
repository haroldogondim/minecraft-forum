  <div class="ui container vertical">
    <? if(!isset($_SESSION['fb_data_retrieve'])): ?>
    <div class="ui basic segment">
      <div class="ui two column middle aligned very relaxed stackable grid">
        <div class="column">
          <?= 
          User::Login();
          ?>
          <form action="" method="post" class="ui form">
            <div class="field">
              <label>Usuário</label>
              <div class="ui left icon input">
                <input type="text" name="user" placeholder="Usuário">
                <i class="user icon"></i>
              </div>
            </div>
            <div class="field">
              <label>Senha</label>
              <div class="ui left icon input">
                <input type="password" name="pass" placeholder="********">
                <i class="lock icon"></i>
              </div>
            </div>
            <div class="field">
              <div class="ui checkbox">
                <input type="checkbox" name="remember" tabindex="0" class="hidden">
                <label title="Caso marque este campo, você ficará logado automaticamente por 90 dias.">Lembrar de mim</label>
              </div>
            </div>
            <button class="ui blue submit button" name="submit-login" type="submit">Login</button>
            <button onClick="Login.facebook()" type="button" class="ui facebook button">
              <i class="facebook icon"></i>
              Login com Facebook
            </button>
          </form>
        </div>
        <div class="ui vertical divider">
          Ou
        </div>
        <div class="center aligned column">
          <div class="ui center aligned basic segment">
            <button onClick="Login.facebook()" class="ui facebook button">
              <i class="facebook icon"></i>
              Cadastre-se com Facebook
            </button>
            <div class="ui horizontal divider">
              Ou
            </div>
            <a href="/register"><div class="ui teal labeled icon button">
              Registre-se
              <i class="add icon"></i>
            </div></a>
          </div>
        </div>
      </div>
    </div>
    <? else: ?>
    <? Core::includeController('settings'); ?>
    <div class="ui basic segment">
    <h3 class="header">Finalize seu cadastro com o Facebook</h3>
    <?= message('Finalize seu cadastro', 'Você ainda não possui um cadastro conosco. Preencha os campos para finalizar seu cadastro.', 'warning', 'warning'); ?>
    <?= Settings::register_facebook(); ?>
      <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
        <div class="required field">
          <label>Usuário</label>
          <input type="text" name="username" maxlength="16" value="<?= post('username'); ?>">
        </div>
        <div class="field">
          <label>Nome</label>
          <input type="text" name="name" maxlength="100" value="<?= post('name'); ?>">
        </div>
        <button class="ui blue button" type="submit" name="register-facebook"><i class="reply icon"></i> Enviar</button>
        <button class="ui blue button" type="submit" name="cancel"><i class="cancel icon"></i> Cancelar</button>
      </form>
    </div>
    <? endif; ?>
  </div>