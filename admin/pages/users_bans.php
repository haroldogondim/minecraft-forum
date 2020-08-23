<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Banir usuário</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de banimentos</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete = $conn->delete('users_bans', array('id' => $id));
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
                                          <th>Usuário</th>
                                          <th>Motivo</th>
                                          <th>Data Início</th>
                                          <th>Data Término</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql = "SELECT b.*, u.username FROM users_bans AS b JOIN users AS u ON u.id = b.user_id ORDER BY id ASC";
                                    $res = $conn->data($sql);
                                    ?>
                                    <tbody>
                                        <? 
                                        foreach($res as $row): 
                                        ?>
                                        <?php
                                        ?>
                                        <tr>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $row->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $row->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                          <td><?= $row->username; ?></td>
                                          <td><?= $row->reason; ?></td>
                                          <td><?= date('d/m/Y H:i:s', $row->started); ?></td>
                                          <td><?= date('d/m/Y H:i:s', ($row->started + $row->duration)); ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                  <? } elseif($a == 'insert') { ?>
                                      <?php
                                      if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        $username = post('username');
                                        if(empty($username)) {
                                          $message = '<div class="alert alert-danger"><strong>Oops!</strong> O campo de usuário não pode ficar em branco.</div>';
                                        } else {
                                          $getUserData = $conn->first("SELECT id FROM users WHERE username = ?", array($username));
                                          if(!isset($getUserData->id)) {
                                            $message = '<div class="alert alert-danger"><strong>Oops!</strong> Este usuário não existe.</div>';
                                          } else {
                                            $checkBan = $conn->first("SELECT id, started, duration FROM users_bans WHERE user_id = ?", array($getUserData->id));
                                            $reason = post('reason');
                                            $ban = post('ban');
                                            $_until_ban = date('d/m/Y H:i:s', (time() + $ban));
                                            if(isset($checkBan->id)) {
                                              $message = '<div class="alert alert-danger"><strong>Oops!</strong> Este usuário já está banido até '.$_until_ban.'.</div>';
                                            } else {
                                              if(empty($reason) || empty($ban)) {
                                                $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher os campos "Motivo" e "Banimento".</div>';
                                              } else {
                                                if($ban == '0') {
                                                  $conn->delete("users_bans", array("user_id" => $getUserData->id));
                                                } else {
                                                  $insert_user = $conn->insert('users_bans', array('user_id' => $getUserData->id, 'reason' => $reason, 'started' => time(), 'duration' => $ban));
                                                  $until_ban = date('d/m/Y H:i:s', (time() + $ban));
                                                  $message = '<div class="alert alert-success"><strong>Sucess!</strong> Usuário banido com sucesso até '.$until_ban.'.</div>';
                                                }
                                              }
                                            }
                                          }
                                        }
                                      }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <div class="col-sm-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post" role="form">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Usuário</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="username" required="" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Motivo</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="reason" required="" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-3 control-label">Banimento</label>
                                                  <div class="col-md-9">
                                                      <select name="ban" class="form-control">
                                                        <option value="">Selecione o que fazer</option>				
                                                        <option value="0">Desbanir</option>
                                                        <option value="7200">2 horas</option>
                                                        <option value="86400">24 horas</option>
                                                        <option value="172800">2 dias</option>
                                                        <option value="604800">1 semana</option>
                                                        <option value="1209600">2 semanas</option>
                                                        <option value="2419200">1 mês</option>
                                                        <option value="145152000">5 anos</option>
                                                      </select>
                                                  </div>
                                                </div>
                                               <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                            </form>
                                        </div>
                                        </div>
                                      </div>
                                <?php } ?>