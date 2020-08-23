<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de tópicos</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $data_topic = $conn->first("SELECT user_id FROM topics WHERE id = ?", array($id));
                                    $data_ranking = $conn->data("SELECT COUNT(user_id) AS num_messages, user_id FROM topics_comments WHERE topic_id = ? GROUP BY user_id ", array($id));
                                    // subtrai o numero de msgs do usuario
                                    foreach($data_ranking as $row_ranking) {
                                      $conn->query("UPDATE users SET topics = topics - ".intval($row_ranking->num_messages)." WHERE id = " . $row_ranking->user_id);
                                    }
                                    $delete = $conn->delete('topics', array('id' => $id));
                                    $delete_comments = $conn->delete('topics_comments', array('topic_id' => $id));
                                    $conn->query("UPDATE users SET topics = topics - 1 WHERE id = " . $data_topic->user_id);
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
                                            <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Delete list"></i></a></th>
                                              <td><a class="on-default remove-row"><i class="fa fa-pencil" title="Edit list"></i></a></td>
                                              <th>Título</th>
                                              <th>Autor</th>
                                              <th>Status</th>
                                              <th>Destaque</th>
                                              <th>Comentários</th>
                                              <th>Data</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $per_page = 10;
                                        $pages = !empty($_GET['page']) && isset($_GET['page']) ? get('page') : '1';
                                        $inicio = ($pages * $per_page) - $per_page;
                                        $sql = sprintf("SELECT topics.*, users.username AS author FROM topics JOIN users ON users.id = topics.user_id ORDER BY topics.id DESC LIMIT %u, %u", $inicio, $per_page);
                                        $res = $conn->data($sql);
                                        
                                        $query_count = $conn->first("SELECT COUNT(id) AS num FROM topics");
                                        $num_count = $query_count->num;
                                        ?>
                                        <tbody>
                                            <? foreach($res as $topics): ?>
                                            <?php
                                            ?>
                                            <tr <? if(!$topics->status): ?>class="danger"<? endif; ?>>
                                              <th><a data-href="?p=<?= $page; ?>&delete=<?= $topics->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                              <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $topics->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                              <td><?= $topics->title; ?></td>
                                              <td><?= $topics->author; ?></td>
                                              <td><?= $topics->status ? 'Ativo' : 'Inativo'; ?></td>
                                              <td><?= $topics->featured ? 'Destaque' : 'Normal'; ?></td>
                                              <td><?= $topics->closed ? 'Desativado' : 'Ativado'; ?></td>
                                              <td><?= date('d/m/Y H:i', $topics->createdAt); ?></td>
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
                                      <? } elseif($a == 'edit') { ?>
                                      <?php
                                      $id = get('id');
                                      $query_data = $conn->first("SELECT topics.*, users.username AS author FROM topics JOIN users ON users.id = topics.user_id WHERE topics.id = ?", array($id));
                                      if(isset($query_data->id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $title = post('title');
                                          $category = post('category');
                                          $post = post('post');
                                          $status = post('status');
                                          $featured = post('featured');
                                          $closed = post('closed');
                                          $insert_user = $conn->update('topics', array('title' => $title, 'category' => $category, 'post' => $post, 'status' => $status, 'featured' => $featured, 'closed' => $closed), array('id' => $query_data->id));
                                          $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Tópico editado com sucesso.</div>';
                                          $query_data = $conn->first("SELECT topics.*, users.username AS author FROM topics JOIN users ON users.id = topics.user_id WHERE topics.id = ?", array($id));
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <form class="form-horizontal" action="" method="post" role="form">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Título*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="title" required="" class="form-control" value="<?= $query_data->title; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Categoria</label>
                                                <div class="col-sm-9">
                                                  <select name="category" class="form-control">
                                                    <option selected value="0">Selecione a categoria</option>
                                                    <? 
                                                    $categories_parent = $conn->data("SELECT * FROM topics_categories WHERE parent = 0");
                                                    foreach($categories_parent as $row_parent):
                                                    ?>
                                                    <option disabled value="0" style="color:#333333;">-- <?= $row_parent->name; ?> --</option>
                                                      <? 
                                                      $categories = $conn->data("SELECT * FROM topics_categories WHERE parent = " . $row_parent->id);
                                                      foreach($categories as $row_inside):
                                                      ?>
                                                      <option <? if($query_data->category_id == $row_inside->id): ?>selected<? endif; ?> value="<?= $row_inside->id; ?>"><?= $row_inside->name; ?></option>
                                                      <? endforeach; ?>
                                                    <? endforeach; ?>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Status</label>
                                                <div class="col-sm-9">
                                                  <select name="status" class="form-control">
                                                    <option <? if($query_data->status == '1'): ?>selected<? endif;?> value="1">Ativo</option>
                                                    <option <? if($query_data->status == '0'): ?>selected<? endif;?> value="0">Inativo</option>
                                                  </select>
                                                  <span class="help-block"><small>Uma vez que o tópico esteja com status "Inativo", ele sumirá do site.</small></span>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Destaque</label>
                                                <div class="col-sm-9">
                                                  <select name="featured" class="form-control">
                                                    <option <? if($query_data->featured == '1'): ?>selected<? endif;?> value="1">Tópico destaque</option>
                                                    <option <? if($query_data->featured == '0'): ?>selected<? endif;?> value="0">Tópico normal</option>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Comentários</label>
                                                <div class="col-sm-9">
                                                  <select name="closed" class="form-control">
                                                    <option <? if($query_data->closed == '0'): ?>selected<? endif;?> value="0">Ativado</option>
                                                    <option <? if($query_data->closed == '1'): ?>selected<? endif;?> value="1">Desativado</option>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Postagem*</label>
	                                                <div class="col-md-9">
	                                                    <textarea type="text" name="post" class="form-control"><?= $query_data->post; ?></textarea>
	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                              </div>
                                          </div>
                                          </form>
                                        </div>
                                      <? } else { ?>
                                      <div class="card-box">
                                        <div class="row">
                                            <div class="alert alert-danger"><strong>Oops!</strong> Esta notícia que você está tentando editar não existe.</div>
                                        </div>
                                      </div>
                                      <? } ?>
                                <?php } ?>