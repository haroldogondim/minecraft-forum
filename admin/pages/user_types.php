<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Adicionar nível</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de níveis</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <? 
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete = $conn->delete('cms_users_type', array('type_user_id' => $id));
                                    echo '<script>$(document).ready(function() { swal({   title: "Sucesso",   text: "Este nível foi deletado com sucesso!",   type: "warning",   showCancelButton: false,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Ok",   closeOnConfirm: false }, function() {   window.location.href = \'?p='.$page.'\';}); }); </script>';
                                  }
                                  ?>
                                  <table class="table table-striped" id="datatable-editable">
                                    <thead>
                                        <tr>
                                          <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Delete all selected registers"></i></a></th>
                                          <td><a class="on-default remove-row"><i class="fa fa-pencil"></i></a></td>
                                          <th>Nome do nível</th>
                                          <th>Comentários extras</th>
                                        </tr>
                                    </thead>
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
                                    <?php
                                    $sql = "SELECT * FROM cms_users_type";
                                    $res = $conn->data($sql);
                                    ?>
                                    <tbody>
                                        <? foreach($res as $levels): ?>
                                        <tr <? if($levels->status == '0'): ?>class="warning"<? endif; ?>>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $levels->type_user_id; ?>" class="on-default remove-row delete-items"><i class="fa fa-trash-o" title="Delete all selected registers"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $levels->type_user_id; ?>" class="on-default remove-row"><i class="fa fa-pencil"></i></a></td>
                                          <td><?= $levels->type_title; ?></td>
                                          <td><?= $levels->type_comment; ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                <? } elseif($a == 'insert') { ?>
                                      <?php 
                                      if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        $name = post('name');
                                        $comment = post('comment');
                                        if(empty($name) || empty($comment)) {
                                          $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                        } else {
                                          $insert_user = $conn->insert('cms_users_type', array('type_title' => $name, 'type_comment' => $comment));
                                          $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Nível criado com sucesso.</div>';
                                        }
                                      }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post">
                                              <div class="form-group" role="form">
                                                <label class="col-md-3 control-label">Nome do nível</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="name" required="" class="form-control" value="">
                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <label class="col-md-3 control-label">Comentários</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="comment" required="" class="form-control" value="">
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
                                      $level_data = $conn->first("SELECT * FROM cms_users_type WHERE type_user_id = ?", array($id));
                                      if(isset($level_data->type_user_id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $name = post('name');
                                          $comment = post('comment');
                                          if(empty($name) || empty($comment)) {
                                            $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                          } else {
                                            $insert_user = $conn->update('cms_users_type', array('type_title' => $name, 'type_comment' => $comment), array('type_user_id' => $id));
                                            $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Nível editado com sucesso.</div>';
                                            $level_data = $conn->first("SELECT * FROM cms_users_type WHERE type_user_id = ?", array($id));
                                          }
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post">
                                                <div class="form-group" role="form">
                                                  <label class="col-md-3 control-label">Nome do nível</label>
                                                  <div class="col-md-8">
                                                      <input type="text" name="name" required="" class="form-control" value="<?= $level_data->type_title; ?>">
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-3 control-label">Comentários</label>
                                                  <div class="col-md-8">
                                                      <input type="text" name="comment" required="" class="form-control" value="<?= $level_data->type_comment; ?>">
                                                  </div>
                                                </div>
                                                <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                              </form>
                                          </div>
                                        </div>
                                      </div>
                                      <? } else { ?>
                                      <div class="card-box">
                                        <div class="row">
                                            <div class="alert alert-danger"><strong>Oops!</strong> Este nível de usuário que você está tentando editar não existe.</div>
                                        </div>
                                      </div>
                                      <? } ?>
                                <?php } ?>