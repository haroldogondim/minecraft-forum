<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Adicionar categoria</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de categorias</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete = $conn->delete('topics_categories', array('id' => $id));
                                    $delete_topics = $conn->delete('topics', array('category_id' => $id));
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
                                  <table class="table table-striped" id="datatable-editable">
                                    <thead>
                                        <tr>
                                        <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Delete list"></i></a></th>
                                          <td><a class="on-default remove-row"><i class="fa fa-pencil" title="Edit list"></i></a></td>
                                          <th>Nome</th>
                                          <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql = "SELECT * FROM topics_categories ORDER BY id ASC";
                                    $res = $conn->data($sql);
                                    ?>
                                    <tbody>
                                        <? 
                                        foreach($res as $topics): 
                                        $parent = 'Categoria principal';
                                        if($topics->parent != 0) {
                                          $getCatName = $conn->first("SELECT * FROM topics_categories WHERE id = ?", array($topics->parent));
                                          $parent = 'Subcategoria de ' . $getCatName->name;
                                        }
                                        ?>
                                        <?php
                                        ?>
                                        <tr>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $topics->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $topics->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                          <td><?= $topics->name; ?></td>
                                          <td><?= $parent; ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                  <? } elseif($a == 'insert') { ?>
                                      <?php
                                      if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        $name = post('name');
                                        $icon = post('icon');
                                        $category = post('category');
                                        $description = post('description');
                                        if(empty($name) || empty($icon)) {
                                          $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                        } else {
                                          $insert_user = $conn->insert('topics_categories', array('name' => $name, 'description' => $description, 'icon' => $icon, 'parent' => $category));
                                          $message = '<div class="alert alert-success"><strong>Sucess!</strong> Categoria criada com sucesso.</div>';
                                        }
                                      }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <div class="col-sm-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post" role="form">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Nome</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="name" required="" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Descrição</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="description" required="" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Ícone</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="icon" required="" class="form-control" value="">
                                                        <span class="help-block"><small>Você pode ver a lista de ícones <a href="https://semantic-ui.com/elements/icon.html" target="_blank">aqui</a>.</small></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-3 control-label">Categoria</label>
                                                  <div class="col-md-9">
                                                      <select name="category" class="form-control">
                                                        <option selected value="0" style="color:#333333;">Categoria principal</option>
                                                        <? 
                                                        $categories = $conn->data("SELECT * FROM topics_categories WHERE parent = 0");
                                                        foreach($categories as $row_inside):
                                                        ?>
                                                        <option value="<?= $row_inside->id; ?>">Subcategoria de <?= $row_inside->name; ?></option>
                                                        <? endforeach; ?>
                                                      </select>
                                                  </div>
                                                </div>
                                               <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                            </form>
                                        </div>
                                        </div>
                                      </div>
                                      <? } elseif($a == 'edit') { ?>
                                      <?php
                                      $id = get('id');
                                      $query_data = $conn->first("SELECT * FROM topics_categories WHERE id = ?", array($id));
                                      if(isset($query_data->id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $name = post('name');
                                          $category = post('category');
                                          $icon = post('icon');
                                          $description = post('description');
                                          $insert_user = $conn->update('topics_categories', array('name' => $name, 'description' => $description, 'icon' => $icon, 'parent' => $category), array('id' => $query_data->id));
                                          $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Categoria editada com sucesso.</div>';
                                          $query_data = $conn->first("SELECT * FROM topics_categories WHERE id = ?", array($id));
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <form class="form-horizontal" action="" method="post" role="form">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Nome*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="name" required="" class="form-control" value="<?= $query_data->name; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Descrição*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="description" required="" class="form-control" value="<?= $query_data->description; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                  <label class="col-md-3 control-label">Ícone</label>
                                                  <div class="col-md-9">
                                                      <input type="text" required="" name="icon" required="" class="form-control" value="<?= $query_data->icon; ?>">
                                                      <span class="help-block"><small>Você pode ver a lista de ícones <a href="https://semantic-ui.com/elements/icon.html" target="_blank">aqui</a>.</small></span>
                                                  </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Categoria</label>
                                                <div class="col-sm-9">
                                                  <select name="category" class="form-control">
                                                    <option <? if(!$query_data->parent): ?>selected<? endif; ?> value="0" style="color:#333333;">Categoria principal</option>
                                                      <? 
                                                      $categories = $conn->data("SELECT * FROM topics_categories WHERE parent = 0");
                                                      foreach($categories as $row_inside):
                                                      ?>
                                                      <option <? if($query_data->parent == $row_inside->id): ?>selected<? endif; ?> value="<?= $row_inside->id; ?>">Subcategoria de <?= $row_inside->name; ?></option>
                                                      <? endforeach; ?>
                                                  </select>
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