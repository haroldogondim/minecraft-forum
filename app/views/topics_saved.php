  <div class="ui container vertical" style="background:#FFF;">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para postar um tópico.', 'remove circle', 'error', true); else: ?>
    <div class="ui basic segment">
      <h3 class="ui header">Seus tópicos salvos</h3>
      <div class="ui basic segment">
        <!-- start topic -->
        <?
        $i_t = 1;
        $recent_topics = Topic::saved();
        if(count($recent_topics)):
        foreach($recent_topics AS $row_topic): 
        ?>
        <div class="row">
          <div class="ui stackable grid">
            <div class="nine wide column">
              <div class="ui fluid list">
                <div class="item">
                  <div class="content">
                    <span class="header"><a href="/topic/<?= $row_topic['slug']; ?>/<?= $row_topic['id']; ?>"><?= $row_topic['title']; ?></a> - <a href="/topic/saved/delete/<?= $row_topic['id']; ?>">Remover dos salvos <i class="icon remove"></i></a></span>
                    <div class="description">Por <a href="/profile/<?= $row_topic['author']; ?>"><b><?= $row_topic['author']; ?></b></a> há <?= dTime($row_topic['createdAt']); ?> atrás em <b><a href="/topic/category/<?= $row_topic['category_id']; ?>"><?= $row_topic['category']; ?></a></b>. Salvo há <?= dTime($row_topic['savedAt']); ?>.</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="seven wide column">
              <div class="ui grid">
                <div class="six wide column right aligned">
                  <div class="ui fluid list right aligned">
                    <div class="item">
                      <div class="content">
                        <div class="description"><?= $row_topic['comments']; ?> respostas</div>
                        <div class="description"><?= $row_topic['views']; ?> visitas</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="ten wide column left aligned">
                  <div class="ui fluid list">
                    <div class="item">
                      <img class="ui avatar image" src="/assets/uploads/avatar/<?= (!count($row_topic['lastComment']) ? 'default.png' : $row_topic['lastComment']['avatar']); ?>">
                      <div class="content">
                        <? if(count($row_topic['lastComment'])): ?>
                        <a href="/profile/<?= $row_topic['lastComment']['author']; ?>" class="header"><?= $row_topic['lastComment']['author']; ?></a>
                        <div class="description"><?= dTime($row_topic['lastComment']['createdAt']); ?> atrás</div>
                        <? else: ?>
                        <span class="header">Ninguém comentou.</span>
                        <div class="description">Seja o primeiro!</div>
                        <? endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <? if(count($recent_topics) > $i_t): ?><div class="ui divider"></div><? endif; ?>
        <? $i_t++; endforeach; ?>
        <? else: ?>
        Você ainda não salvou nenhum tópico.
        <? endif; ?>
      </div>
    </div>
    <? endif; ?>
  </div>