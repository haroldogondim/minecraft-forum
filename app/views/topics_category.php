  <? 
  global $Main; 
  $category = $Main->catExists(get('cat_id'));
  if(count($category)): 
  ?>
  <div class="ui container vertical" style="background:#FFF;">
    <div class="ui basic segment">
      <h3 class="ui header">Tópicos sobre <?= $category['name']; ?></h3>
      <div class="ui basic segment">
        <!-- start topic -->
        <?
        $i_t = 1;
        $recent_topics = $Main->topicsList(get('cat_id'));
        if(count($recent_topics)):
        foreach($recent_topics AS $row_topic): 
        ?>
        <div class="row">
          <div class="ui stackable grid">
            <div class="nine wide column">
              <div class="ui fluid list">
                <div class="item">
                  <div class="content">
                    <a href="/topic/<?= $row_topic['slug']; ?>/<?= $row_topic['id']; ?>" class="header"><?= $row_topic['title']; ?></a>
                    <div class="description">Por <a href="/profile/<?= $row_topic['author']; ?>"><b><?= $row_topic['author']; ?></b></a> há <?= dTime($row_topic['createdAt']); ?> atrás em <b><a href="/topic/category/<?= $row_topic['category_id']; ?>"><?= $row_topic['category']; ?></a></b>.</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="seven wide column">
              <div class="ui grid">
                <div class="seven wide column right aligned">
                  <div class="ui fluid list right aligned">
                    <div class="item">
                      <div class="content">
                        <div class="description"><?= $row_topic['comments']; ?> respostas</div>
                        <div class="description"><?= $row_topic['views']; ?> visitas</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="nine wide column left aligned">
                  <div class="ui fluid list">
                    <div class="item">
                      <img class="ui avatar image" src="/assets/uploads/avatar/<?= (!count($row_topic['lastComment']) ? 'default.png' : $row_topic['lastComment']['avatar']); ?>">
                      <div class="content">
                        <? if(count($row_topic['lastComment'])): ?>
                        <a href="/profile/<?= $row_topic['lastComment']['author']; ?>" class="header"><?= $row_topic['lastComment']['author']; ?></a>
                        <div class="description">há <?= dTime($row_topic['lastComment']['createdAt']); ?> atrás</div>
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
        Ainda não há tópicos postados nesta categoria.
        <? endif; ?>
      </div>
    </div>
  </div>
  <? else: ?>
  <div class="ui container vertical">
  <?= message('Oops', 'A categoria que você está tentando acessar não existe.'); ?>
  </div>
  <? endif; ?>