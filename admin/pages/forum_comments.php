<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de categorias</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $data_topic = $conn->first("SELECT user_id, topic_id FROM topics_comments WHERE id = ?", array($id));
                                    $delete = $conn->delete('topics_comments', array('id' => $id));
                                    $conn->query("UPDATE users SET topics = topics - 1 WHERE id = " . $data_topic->user_id);
                                    $conn->query("UPDATE topics SET num_comments = num_comments - 1 WHERE id = " . $data_topic->topic_id);
                                    echo '<script>$(document).ready(function() { swal({   title: "Sucesso",   text: "Tópico deletado com sucesso!",   type: "warning",   showCancelButton: false,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Ok",   closeOnConfirm: false }, function() {   window.location.href = \'?p='.$page.'\';}); }); </script>';
                                  }
                                  ?>
                                  <script>
                                  $(document).ready(function() {
                                    $('.delete-items').click(function() {
                                      var href = $(this).data('href');
                                      swal({
                                          title: "Tem certeza?",
                                          text: "Você não poderá desfazer isso.",
                                          type: "warning",
                                          showCancelButton: true,
                                          confirmButtonColor: "#DD6B55",
                                          confirmButtonText: "Sim, deletar!",
                                          closeOnConfirm: false
                                      }, function() {
                                          window.location.href = href;
                                      });
                                    });
                                  });
                                  </script>
                                  <div class="row">
                                    <div class="col-md-12">
                                      <table class="table table-striped" id="datatable-editable">
                                        <thead>
                                            <tr>
                                            <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Excluir"></i></a></th>
                                              <td align="center">Comentário</td>
                                              <td>Autor</td>
                                              <td>Data</td>
                                              <td>Tópico</td>
                                            </tr>
                                        </thead>
                                        <?php
                                        $per_page = 10;
                                        $pages = !empty($_GET['page']) && isset($_GET['page']) ? get('page') : '1';
                                        $inicio = ($pages * $per_page) - $per_page;
                                        $sql = sprintf("SELECT topics_comments.*, users.username, topics.title, topics.slug FROM topics_comments JOIN users ON users.id = topics_comments.user_id JOIN topics ON topics.id = topics_comments.topic_id LIMIT %u, %u", $inicio, $per_page);
                                        $res = $conn->data($sql);
                                        $query_count = $conn->first("SELECT COUNT(id) AS num FROM topics_comments");
                                        $num_count = $query_count->num;
                                        ?>
                                        <tbody>
                                          <? 
                                          foreach($res as $topics): 
                                          ?>
                                          <tr>
                                            <th><a data-href="?p=<?= $page; ?>&delete=<?= $topics->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Excluir"></i></a></th>
                                            <td align="center"><?= $topics->comment; ?></td>
                                            <td><?= $topics->username; ?></td>
                                            <td><?= date('d/m/Y H:i', $topics->createdAt); ?></td>
                                            <td><a href="/topic/<?= $topics->slug; ?>/<?= $topics->topic_id; ?>"><?= $topics->title; ?></a></td>
                                          </tr>
                                          <? endforeach; ?>
                                        </tbody>
                                      </table>
                                      <div class="row">
                                        <div class="text-left">
                                          <ul class="pagination pagination-split m-t-30 m-b-0">
                                            <?= pagination('?p='.get('p').'&page=%u', $num_count, $inicio, $per_page, 3, true); ?>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <?php } ?>