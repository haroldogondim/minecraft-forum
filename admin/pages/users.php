<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Adicionar usuário</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de usuários</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete_permissions = $conn->delete('cms_users_type_relationship', array('user_id' => $id));
                                    echo '<script>$(document).ready(function() { swal({   title: "Sucesso",   text: "Este usuário foi deletado com sucesso!",   type: "warning",   showCancelButton: false,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Ok",   closeOnConfirm: false }, function() {   window.location.href = \'?p='.$page.'\';}); }); </script>';
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
                                          <th>Usuário</th>
                                          <th>Nome</th>
                                          <th>Nível</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql = "SELECT u.* FROM users AS u JOIN cms_users_type_relationship AS r ON r.user_id = u.id";
                                    $res = $conn->data($sql);
                                    ?>
                                    <tbody>
                                        <? foreach($res as $users): ?>
                                        <?php
                                        $levels = "SELECT t.type_title FROM cms_users_type_relationship AS r LEFT JOIN cms_users_type AS t ON t.type_user_id = r.type_user_id JOIN users AS u ON u.id = r.user_id WHERE r.user_id = '".$users->id."' ORDER BY r.type_user_id ASC";
                                        $res_levels = $conn->data($levels);
                                        $levels = array();
                                        foreach($res_levels as $level) {
                                          $levels[] = $level->type_title;
                                        }
                                        ?>
                                        <tr>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $users->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $users->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                          <td><?= $users->username; ?></td>
                                          <td><?= $users->name; ?></td>
                                          <td><?= implode(', ', $levels); ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                <? } elseif($a == 'insert') { ?>
                                      <? } elseif($a == 'edit') { ?>
                                      <?php
                                      $id = get('id');
                                      $user_data = $conn->first("SELECT * FROM users WHERE id = ?", array($id));
                                      if(isset($user_data->id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $username = post('username');
                                          $password = post('password');
                                          $status = (post('status') == '1' ? '1' : '0');
                                          $password_md5 = password_hash($password, PASSWORD_BCRYPT);
                                          $name = post('name');
                                          $role = post('role');
                                          if(empty($username) || empty($name)) {
                                            $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                          } else {
                                            $deleteAllLevels = $conn->delete('cms_users_type_relationship', array('user_id' => $id));
                                            $update_data = array('username' => $username, 'name' => $name, 'status' => $status);
                                            if(!empty($password)) {
                                              $update_data['password'] = $password_md5;
                                            }
                                            
                                            if($role != '0') {
                                              $conn->insert("cms_users_type_relationship", array("user_id" => $id, "type_user_id" => $role));
                                            }
                                            $insert_user = $conn->update('users', $update_data, array('id' => $id));
                                            $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Usuário editado com sucesso.</div>';
                                            $user_data = $conn->first("SELECT * FROM users WHERE id = ?", array($id));
                                          }
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post">
                                              <div class="form-group" role="form">
	                                                <label class="col-md-2 control-label">Usuário</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="username" required="" class="form-control" value="<?= $user_data->username; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Senha</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="password" type="password" class="form-control" value="">
                                                      <span class="help-block"><small>A senha só mudará caso você preencha o campo.</small></span>

	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Cargo</label>
                                                <div class="col-sm-9">
                                                  <select name="role" class="form-control">
                                                    <option selected value="0">Usuário</option>
                                                    <?
                                                    $users_types = $conn->data("SELECT type_user_id, type_title FROM cms_users_type");
                                                    foreach($users_types as $row_type):
                                                    ?>
                                                    <option <? if(userHasLevel($id, $row_type->type_user_id)): ?>selected<? endif;?> value="<?= $row_type->type_user_id; ?>"><?= $row_type->type_title; ?></option>
                                                    <? endforeach; ?>
                                                  </select>
                                                  <span class="help-block"><small>Uma vez que o usuário esteja com status "Inativo", ele não será capaz de logar no painel.</small></span>
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
                                            <div class="alert alert-danger"><strong>Oops!</strong> Este usuário que você está tentando editar não existe.</div>
                                        </div>
                                      </div>
                                      <? } ?>
                                <?php } ?>