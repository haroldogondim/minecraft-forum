  <script type="text/javascript" src="/assets/topic.js"></script>
  <div class="ui modal edit-post">
    <i class="close icon"></i>
    <div class="header">Edite sua mensagem</div>
    <div class="content">
      <div class="ui form">
        <div class="field">
          <label>Sua postagem</label>
          <textarea id="post-edit-content"></textarea>
        </div>
      </div>
    </div>
    <div class="actions">
      <div class="ui button cancel">Cancelar</div>
      <div class="ui green button ok">Editar</div>
    </div>
  </div>
  <div class="ui container vertical">
  <? if(Topic::exists()): ?>
  <? $data_topic = Topic::data(); ?>
    <div class="ui basic segment">
      <div class="ui stackable grid">
        <div class="eight wide column">
          <div class="ui list">
            <div class="item">
              <h3 class="header"><?= $data_topic['title']; ?></h3>
              Postado há <?= $data_topic['created_at']; ?> em <a href="/topic/category/<?= $data_topic['category_id']; ?>"><b><?= $data_topic['category_name']; ?></b></a>
            </div>
          </div>
        </div>
        <div class="eight wide column right aligned">
          <div class="ui pagination menu">
          <?= $data_topic['pagination']; ?>
          </div>
        </div>
      </div>
      <div class="ui grid"> 
        <div class="tablet computer only row">
          <div class="ui fluid card">
            <div class="content">
              <div class="content">
                <div class="right floated meta"><!--<a class="ui black label"><i class="warning icon"></i> &nbsp;Denunciar</a>--> <a class="ui violet label" onClick="Topic.save(<?= $data_topic['id']; ?>)"><i class="save icon"></i> &nbsp;Salvar</a></div>
                <img class="ui avatar image" src="/assets/uploads/avatar/<?= $data_topic['avatar']; ?>"> <b><a class="user-data" data-html="<div class='header'><?= $data_topic['author']; ?></div><div class='content'>Membro desde <b><?= date('d/m/Y H:i', $data_topic['user_createdAt']); ?></b><br /><?= $data_topic['total_comments']; ?> mensagens</div>" href="/profile/<?= $data_topic['author']; ?>"><?= $data_topic['author']; ?></a></b> - <i><?= $data_topic['created_at']; ?> atrás</i>
              </div>
              <div class="ui divider"></div>
              <div class="content">
                <div class="ui grid"> 
                  <div class="four wide column">
                    <div class="ui vertical">
                      <div class="item">
                        <h4 class="ui header"><a href="/profile/<?= $data_topic['author']; ?>"><?= $data_topic['author']; ?></a></h4>
                        <p><img class="ui medium rounded image" src="/assets/uploads/avatar/<?= $data_topic['avatar']; ?>">
                      </div>
                      <div class="item"><br>
                         <button class="ui right floated inverted red button">GERENTE</button><br><br>
                          <p><b>Posts:</b> <?= $data_topic['total_comments']; ?></p>
                          <p><b>Registro em:</b> <?= date('d/m/Y', $data_topic['user_createdAt']); ?></p>
                          <p><b>Clan:</b> RST</p>
                          <p><b>Medalhas:</b></p>
                          <p><img src="https://i.imgur.com/fw7Y3W3.png" data-title="Medalha" data-content="Por ganhar o evento x" class="ui image"></p>
                      </div>
                    </div>  
                  </div>
                  <div class="twelve wide column">
                    <div class="row">
                      <?= $data_topic['post']; ?>
                      <div class="ui divider"></div>
                      <?= $data_topic['signature']; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mobile only row">
          <div class="ui fluid card">
            <div class="content">
              <div class="content">
                <div class="right floated meta"><!--<a class="ui black label"><i class="warning icon"></i> &nbsp;Denunciar</a>--> <a class="ui violet label" onClick="Topic.save(<?= $data_topic['id']; ?>)"><i class="save icon"></i> &nbsp;Salvar</a></div>
                <img class="ui avatar image" src="/assets/uploads/avatar/<?= $data_topic['avatar']; ?>">
                <a class="user-data" 
                          data-html="
                          <div class='header'><?= $data_topic['author']; ?></div>
                          <div class='content'>Membro desde <b><?= date('d/m/Y H:i', $data_topic['user_createdAt']); ?></b><br /><?= $data_topic['total_comments']; ?> mensagens</div>
                          <b>Registro em:</b> <?= date('d/m/Y', $data_topic['user_createdAt']); ?><br />
                          <b>Clan:</b> RST<br />
                          <b>Medalhas:</b> 0<br />
                          <img src='https://i.imgur.com/fw7Y3W3.png' data-title='Medalha' data-content='Por ganhar o evento x' class='ui image'></p>"
                  href="/profile/<?= $data_topic['author']; ?>"><?= $data_topic['author']; ?></a> - <i><?= $data_topic['created_at']; ?> atrás</i>
              </div>
              <div class="ui divider"></div>
              <div class="content">
                <div class="row">
                  <?= $data_topic['post']; ?>
                  <div class="ui divider"></div>
                  <?= $data_topic['signature']; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <? foreach(Topic::comments() as $row_comment): ?>
    <div class="ui basic segment" id="comment-<?= $row_comment['id']; ?>">
      <div class="ui fluid card">
        <div class="content">
          <div class="content">
            <div class="right floated meta"><? if(User::loggedIn()): ?><!--<a class="ui black label"><i class="warning icon"></i> &nbsp;Denunciar</a>--> <a href="#comment-field" onClick="Topic.quote(<?= $row_comment['id']; ?>)" class="ui green label"><i class="comment icon"></i> &nbsp;Citar</a><? endif; ?></div>
            <img class="ui avatar image" src="/assets/uploads/avatar/<?= $row_comment['avatar']; ?>"> <b><a class="user-data" data-html="<div class='header'><?= $row_comment['author']; ?></div><div class='content'>Membro desde <b><?= date('d/m/Y H:i', $row_comment['user_createdAt']); ?></b><br /><?= $row_comment['total_comments']; ?> mensagens</div>" href="/profile/<?= $row_comment['author']; ?>"><?= $row_comment['author']; ?></a></b> - <i><?= $row_comment['created_at']; ?> atrás</i>
          </div>
          <div class="ui divider"></div>
          <div class="content" id="content-comment-<?= $row_comment['id']; ?>">
            <div class="row">
              <?= $row_comment['comment']; ?>
              <div class="ui divider"></div>
              <?= $row_comment['signature']; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <? endforeach; ?>
    
    <div id="preview-comment" style="display:none;"></div>
    <div class="row">
      <div class="ui basic segment">
        <div class="ui pagination menu right floated">
          <?= $data_topic['pagination']; ?>
        </div>
      </div>
    </div>
    <? if(!User::loggedIn()): ?>
    <?= message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para comentar em tópicos.', 'remove circle', 'error', true); ?>
    <? elseif(!User::active()): ?>
    <?= message('Oops!', 'Você precisa <a href="/settings/activate/">ativar sua conta</a> para comentar em tópicos.', 'remove circle', 'error', true); ?>
    <? else: ?>
      <? if(!boolval($data_topic['closed'])): ?>
      <? Core::includeController('topic'); ?>
      <div class="ui basic segment">
        <?= Topic::comment(); ?>
        <h3 class="header">Faça um comentário neste tópico</h3>
        <div class="row">
          <div class="ui icon buttons">
            <button onClick="setBBCode('comment-field', '[b]', '[/b]');" class="ui button"><i class="bold icon"></i></button>
            <button onClick="setBBCode('comment-field', '[u]', '[/u]');" class="ui button"><i class="underline icon"></i></button>
            <button onClick="setBBCode('comment-field', '[i]', '[/i]');" class="ui button"><i class="italic icon"></i></button>
            <button onClick="setBBCode('comment-field', '[img]', '[/img]');" class="ui button"><i class="image icon"></i></button>
            <button onClick="setBBCode('comment-field', '[youtube]', '[/youtube]');" class="ui button"><i class="youtube icon"></i></button>
          </div>
          <form action="" name="form-comment" method="post" class="ui reply form">
            <div class="field">
              <textarea id="comment-field" name="comment-field"></textarea>
            </div>
            <div class="ui blue labeled submit icon button" id="submit-comment">
              <i class="reply icon"></i> Enviar
            </div>
            <div onClick="Topic.preview()" class="ui blue labeled submit icon button">
              <i class="find icon"></i> Visualizar
            </div>
          </form>
        </div>
      </div>
      <? else: ?>
      <?= message('Oops!', 'Os comentários deste tópico foram desativados por um moderador.', 'remove circle', 'error'); ?>
      <? endif; ?>
    <? endif; ?>
  <? else: ?>
  <?= message('Oops!', 'Este tópico não existe.'); ?>
  <? endif; ?>
  </div>
