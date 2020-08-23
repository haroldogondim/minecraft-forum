  <? global $Main; ?>
  <div class="ui container vertical" id="topics">
    <div class="ui padded basic segment" style="border-bottom: 1px solid #d4d4d5">
      <h3 class="ui header"><i class="icon heart"></i> Home</h3>
    </div>

    <div class="ui basic segment">
      <h4 class="ui header">TÓPICOS RECENTES</h4>
      <div class="ui basic segment">
        <!-- start topic -->
        <?
        $i_t = 1;
        $recent_topics = $Main->recentTopics();
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
                      <? if(count($row_topic['lastComment'])): ?><img class="ui avatar image" src="/assets/uploads/avatar/<?= (!count($row_topic['lastComment']) ? 'default.png' : $row_topic['lastComment']['avatar']); ?>"><? endif; ?>
                      <div class="content">
                        <? if(count($row_topic['lastComment'])): ?>
                        <a href="/profile/<?= $row_topic['lastComment']['author']; ?>" class="header"><?= $row_topic['lastComment']['author']; ?></a>
                        <div class="description">há <?= dTime($row_topic['lastComment']['createdAt']); ?></div>
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
      </div>
      
      
      <? 
      $featured_topics = $Main->recentTopics(true); 
      if(count($featured_topics)):
      ?>
      <div class="ui section divider"></div>
      <h4 class="ui header">DESTAQUES</h4>
      <div class="ui basic segment">
        <?
        $i_t = 1;
        
        foreach($featured_topics AS $row_topic): 
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
        <? if(count($featured_topics) > $i_t): ?><div class="ui divider"></div><? endif; ?>
        <? $i_t++; endforeach; ?>
      </div>
      <? endif; ?>
      
      <!--<div class="ui stackable grid">
        <div class="eleven wide column">-->
          <? foreach($Main->categories() as $cat_parent): ?>
          <div class="ui basic segment"><h3 class="ui horizontal divider header"><i class="<?= $cat_parent['icon']; ?> icon"></i><?= $cat_parent['name']; ?></h3></div>
          <? foreach($Main->categories($cat_parent['id']) as $cat_inside): ?>
          <div class="ui raised segments">
            <div class="ui padded segment">
              <div class="ui stackable grid">
                <div class="ten wide column">
                  <div class="ui fluid list">
                      <div class="item">                  
                        <div class="ui avatar image"><div class="ui circular large icon button"><i class="<?= $cat_inside['icon']; ?> icon"></i></div></div>
                        <div class="content">
                          <a href="/topic/category/<?= $cat_inside['id']; ?>"><h3 class="header"><?= $cat_inside['name']; ?></h3></a>
                          <div class="description"><?= $cat_inside['description']; ?></div>
                        </div>
                      </div>
                  </div>
                </div>
                <div class="six wide column">
                  <div class="ui grid">
                    <div class="six wide column right aligned">
                      <div class="ui tiny statistic">
                        <div class="value">
                          <?= $cat_inside['num_topics']; ?>
                        </div>
                        <div class="label">
                          tópicos
                        </div>
                      </div>
                    </div>
                      <div class="ten wide column">
                        <div class="ui fluid list">
                        <? if(count($cat_inside['lastTopic'])): ?>
                          <div class="item">
                            <img class="ui avatar image" src="/assets/uploads/avatar/<?= $cat_inside['lastTopic']['avatar']; ?>">
                            <div class="content">
                              <a href="/topic/<?= $cat_inside['lastTopic']['slug']; ?>/<?= $cat_inside['lastTopic']['id']; ?>" class="header"><?= cuts_text($cat_inside['lastTopic']['title'], 15); ?></a>
                              <div class="description">Por <?= $cat_inside['lastTopic']['author']; ?><br />
                              há <?= dTime($cat_inside['lastTopic']['createdAt']); ?></div>
                            </div>
                          </div>
                          <? else: ?>
                          <div class="item">
                            <div class="content">
                              <span class="header"></span><br />
                              <div class="description">Categoria ainda sem discussões.</div>
                            </div>
                          </div>
                          <? endif; ?>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <? endforeach; ?>
          <? endforeach; ?>
        <!--</div>
        <div class="five wide column">
        
        </div>
      </div>-->
    </div>
    
  </div>