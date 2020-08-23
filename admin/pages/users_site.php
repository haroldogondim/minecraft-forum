                              <? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <link href="assets/plugins/switchery/dist/switchery.min.css" rel="stylesheet" />
                              <script src="assets/plugins/switchery/dist/switchery.min.js"></script>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de tópicos</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete_comments = $conn->delete('users', array('id' => $id));
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
                                    <form class="form-inline" style="float:right;" role="form">
                                      <div class="form-group">
                                          <input type="email" class="form-control" id="findUser" placeholder="Nick do usuário">
                                      </div>
                                      <button type="submit" name="submit-search" class="btn btn-success waves-effect waves-light m-l-10 btn-md">Buscar</button>
                                    </form>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-12">
                                      <table class="table table-striped" id="datatable-editable">
                                        <thead>
                                            <tr>
                                            <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Delete list"></i></a></th>
                                              <td><a class="on-default remove-row"><i class="fa fa-pencil" title="Edit list"></i></a></td>
                                              <th>Usuário</th>
                                              <th>Tipo de registro</th>
                                              <th>Registro</th>
                                              <th>Último Login</th>
                                              <th>Email</th>
                                              <th>Status</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $whereBusca = '';
                                        if(isset($_REQUEST['submit-search'])) {
                                          
                                        }
                                        $per_page = 10;
                                        $pages = !empty($_GET['page']) && isset($_GET['page']) ? (int) get('page') : '1';
                                        $inicio = ($pages * $per_page) - $per_page;
                                        $sql = "SELECT * FROM users ".$whereBusca." ORDER BY id DESC LIMIT $inicio, $per_page";
                                        $res = $conn->data($sql);
                                        
                                        $query_count = $conn->first("SELECT COUNT(id) AS num FROM users");
                                        $num_count = $query_count->num;
                                        ?>
                                        <tbody>
                                            <? foreach($res as $data): ?>
                                            <tr <? if(!$data->activated): ?>class="danger"<? endif; ?>>
                                              <th><a data-href="?p=<?= $page; ?>&delete=<?= $data->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                              <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $data->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                              <td><?= $data->username; ?></td>
                                              <td><?= $data->fb_id ? 'Facebook' : 'Comum'; ?></td>
                                              <td><?= date('d/m/Y H:i', $data->createdAt); ?></td>
                                              <td><?= date('d/m/Y H:i', $data->lastLoginAt); ?></td>
                                              <td><?= $data->email; ?></td>
                                              <td><?= $data->activated ? 'Ativado' : 'Inativo'; ?></td>
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
                                      $query_data = $conn->first("SELECT * FROM users WHERE id = ?", array($id));
                                      if(isset($query_data->id)) {
                                        $canChangeUsername = (($query_data->lastUpdateAtUsername + 2419200) < time());
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $dataUp = array();
                                          $errors = array();
                                          $username = post('username');
                                          $name = post('name');
                                          $activated = post('activated');
                                          $signature = post('signature');
                                          $reset_avatar = post('reset_avatar');
                                          $role = post('role');
                                          if(!empty($reset_avatar) && $reset_avatar) {
                                            $dataUp['avatar'] = 'default.png';
                                          }
                                          
                                          if(!empty($username) && $username != $query_data->username && $canChangeUsername) {
                                            $userExists = (bool) db::NumRows(db::Query(sprintf("SELECT 0 FROM users WHERE username = '%s'", $username)));
                                            if(!$userExists) {
                                              if(preg_match('/[^a-zA-Z0-9\-\_\=\?\!\@\:\,\.]/', $username)) {
                                                $errors[] = 'Seu usuário não pode conter espaços, deve conter letras de A-Z, pode conter números de 0-9, e pode conter os caracteres especiais: .,-_=?!@:';
                                              }
                                              
                                              if(strlen($username) < 4 || strlen($username) > 20) {
                                                $errors[] = 'Seu usuário deve conter entre 3 e 20 caracteres.';
                                              }
                                            } else {
                                              $errors[] = 'Já existe um usuário cadastrado com esse nome.';
                                            }
                                          }
                                          
                                          if(!count($errors)) {
                                            $dataUp['username'] = $username;
                                          }

                                          if(count($errors))
                                          {
                                            $message = '<div class="alert alert-success"><strong>Erro!</strong>';
                                            foreach($errors as $erro)
                                            {
                                              $message .= '<li>'.$erro.'</li>';
                                            }
                                            $message .= '</div>';
                                          }
                                          else
                                          {
                                            $deleteAllLevels = $conn->delete('cms_users_type_relationship', array('user_id' => $id));
                                            if($role != '0') {
                                              $conn->insert("cms_users_type_relationship", array("user_id" => $id, "type_user_id" => $role));
                                            }
                                            
                                            $dataUp = array('name' => $name, 'activated' => $activated, 'signature' => $signature);
                                            $insert_user = $conn->update('users', $dataUp, array('id' => $query_data->id));
                                            $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Usuário editado com sucesso.</div>';
                                            $query_data = $conn->first("SELECT * FROM users WHERE id = ?", array($id));
                                          }
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <form class="form-horizontal" action="" method="post" role="form">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Usuário</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" <? if(!$canChangeUsername): ?>disabled<? endif; ?> name="username" required="" class="form-control" value="<?= $query_data->username; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Nome</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="name" required="" class="form-control" value="<?= $query_data->name; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Status da conta</label>
                                                <div class="col-sm-9">
                                                  <select name="activated" class="form-control">
                                                    <option <? if($query_data->activated == '1'): ?>selected<? endif;?> value="1">Ativada</option>
                                                    <option <? if($query_data->activated == '0'): ?>selected<? endif;?> value="0">Inativada</option>
                                                  </select>
                                                  <span class="help-block"><small>Caso Inativada, o usuário não poderá postar nem comentar no fórum.</small></span>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Cargo</label>
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
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Email</label>
                                                <div class="col-sm-9">
                                                  <p class="form-control-static"><?= $query_data->email; ?></p>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Tipo de cadastro</label>
                                                <div class="col-sm-9">
                                                  <p class="form-control-static"><?= $query_data->fb_id ? 'Facebook' : 'Comum'; ?></p>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Registro</label>
                                                <div class="col-sm-9">
                                                  <p class="form-control-static"><?= date('d/m/Y H:i', $query_data->createdAt); ?></p>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-3 control-label">Último Login</label>
                                                <div class="col-sm-9">
                                                  <p class="form-control-static"><?= date('d/m/Y H:i', $query_data->lastLoginAt); ?></p>
                                                </div>
                                              </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Assinatura*</label>
	                                                <div class="col-md-9">
	                                                    <textarea type="text" name="signature" class="form-control"><?= $query_data->signature; ?></textarea>
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Avatar*</label>
	                                                <div class="col-md-9">
                                                    <img src="/assets/uploads/avatar/<?= $query_data->avatar; ?>" style="max-width:200px;" />
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-3 control-label">Resetar avatar?</label>
	                                                <div class="col-md-9">
                                                    <input type="checkbox" name="reset_avatar" data-plugin="switchery" data-color="#f05050" data-size="small"/>
                                                    <span class="help-block"><small>Caso marcado, o sistema resetará o avatar do usuário para o padrão.</small></span>
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