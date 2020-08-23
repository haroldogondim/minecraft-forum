<div class="ui container vertical" id="topic-create">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para postar um tópico.', 'remove circle', 'error'); ?>
  <? elseif(!User::active()): echo message('Oops!', 'Você precisa <a href="/settings/activate/">ativar sua conta</a> para postar um tópico.', 'remove circle', 'error', true); else: ?>
  <script>
  $(document).ready(function() {
    $('#form-comment').form({
      inline: true,
      on: 'blur',
      fields: {
        title: {
          identifier: 'title',
          rules: [
            {
              type: 'empty',
              prompt: 'Título não pode ficar em branco.'
            },
            {
              type: 'minLength[6]',
              prompt: 'Título deve conter ao menos 6 caracteres.'
            },
            {
              type: 'maxLength[70]',
              prompt: 'Título deve conter no máximo 70 caracteres.'
            }
          ]
        },
        category: {
          identifier: 'category',
          rules: [
            {
              type: 'not[0]',
              prompt: 'Selecione uma categoria válida.'
            }
          ]
        },
        post: {
          identifier: 'post',
          rules: [
            {
              type: 'empty',
              prompt: 'O campo de postagem não pode ficar em branco.'
            },
            {
              type: 'minLength[10]',
              prompt: 'O campo de postagem deve ter ao menos 10 caracteres.'
            },
          ]
        }
      }
    });
  });
  </script>
  <div class="ui basic segment">
    <?= Topic::create(); ?>
    <form action="" name="form-comment" id="form-comment" method="post" class="ui reply form">
      <div class="required field">
        <label>Título</label>
        <input type="text" name="title" maxlength="70" placeholder="Título do tópico">
      </div> 
      <div class="required field">
        <label>Categoria</label>
        <select name="category" class="ui fluid">
          <option selected value="0">Selecione a categoria</option>
          <? 
          $categories_parent = Topic::categories();
          foreach($categories_parent as $row_parent):
          ?>
          <option disabled value="0" style="color:#333333;">-- <?= $row_parent['name']; ?> --</option>
            <? 
            $categories = Topic::categories($row_parent['id']);
            foreach($categories as $row_inside):
            ?>
            <option value="<?= $row_inside['id']; ?>"><?= $row_inside['name']; ?></option>
            <? endforeach; ?>
          <? endforeach; ?>
        </select>
      </div>
      <div class="required field">
        <div class="ui icon buttons">
          <button type="button" onClick="setBBCode('comment-field', '[b]', '[/b]');" class="ui button"><i class="bold icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[u]', '[/u]');" class="ui button"><i class="underline icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[i]', '[/i]');" class="ui button"><i class="italic icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[img]', '[/img]');" class="ui button"><i class="image icon"></i></button>
          <button type="button" onClick="setBBCode('comment-field', '[youtube]', '[/youtube]');" class="ui button"><i class="youtube icon"></i></button>
        </div>
        <label>Conteúdo do tópico</label>
        <textarea id="comment-field" placeholder="Escreva sobre sua postagem." name="post"></textarea>
      </div>
      <div class="ui blue labeled submit icon button">
        <i class="reply icon"></i> Enviar
      </div>
    </form>
  </div>
  <? endif; ?>
</div>