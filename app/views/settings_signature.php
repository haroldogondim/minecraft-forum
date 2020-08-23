<div class="ui container vertical" id="topic-create">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para acessar esta página.', 'remove circle', 'error'); else: ?>
  <div class="ui basic segment">
    <?= Settings::signature(); ?>
    <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
      <div class="field">
        <div class="ui icon buttons">
          <button type="button" onClick="setBBCode('comment-field', '[b]', '[/b]');" class="ui button"><i class="bold icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[u]', '[/u]');" class="ui button"><i class="underline icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[i]', '[/i]');" class="ui button"><i class="italic icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[img]', '[/img]');" class="ui button"><i class="image icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[youtube]', '[/youtube]');" class="ui button"><i class="youtube icon"></i></button>
        </div>
        <label>Sua assinatura</label>
        <textarea id="comment-field" name="signature"><?= User::data('signature'); ?></textarea>
      </div>
      <button class="ui blue button" type="submit" name="submit-signature"><i class="reply icon"></i> Enviar</button>
    </form>
  </div>
  <? endif; ?>
</div>