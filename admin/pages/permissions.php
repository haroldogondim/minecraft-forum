<? if(!isset($_SESSION['admin_id'])) exit; ?>
                            <? global $conn; ?>
                            <script>
                            $(document).ready(function(){
                              $("#selectall").change(function(){
                                $(".checkbox1").prop('checked', $(this).prop("checked"));
                              });

                              $('#list-items-button').click(function() {
                                $('#list-items').submit();
                              });
                            });
                            function select_tp(tp_id){
                              this.location = 'index.php?p=<?= $page; ?>&tp_id='+tp_id;
                            }
                            function changeData(tf){
                              if(tf.checked)
                                toggleAll(list, true);
                              else
                                toggleAll(list, false);
                            }
                            function toggleAll(formname, checked_flag){
                              len = formname.elements.length;
                              var i = 0;
                              for(i = 0; i < len; i++) {
                                  formname.elements[i].checked = checked_flag;
                              }
                            }
                            function confirm_delete() {
                              if(confirm('Are you sure you want to delete these permissions?'))
                                return true;
                              else
                                return false;
                            }
                            </script>

                            <?
                            $a = get('a');
                            $tp_id = get('tp_id');
                            if($tp_id) {
                            ?>
                            <a href="?p=<?=$page?>&tp_id=<?=$tp_id?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Dar permissões</button></a>
                            <? if(get('a')): ?><a href="?p=<?=$page?>&tp_id=<?=$tp_id?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de permissões</button></a><? endif; ?>
                            <a href="?p=<?=$page?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de níveis</button></a>
                            <!-- <a href="?p=<?=$page?>&tp_id=<?=$tp_id?>&a=insert">Insert</a> | <a href="?p=<?=$page?>&tp_id=<?=$tp_id?>">List</a> | <a href="?p=<?=$page?>">User levels</a> -->
                            <?
                            }
                            if($a == 'edit' || $a == 'insert') {
                              $id = get('id');
                              if($id) {
                                $a = "f_edit";
                              } else {
                                $a = "f_insert";
                              }
                              if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $id = get('id');
                                $type_user_id = post('type_user_id');
                                $aca_id = post('aca_id');
                                if($a == "f_edit") {
                                  if($id) {
                                    //$edit = $conn_pure->query("UPDATE cms_permissions SET type_user_id = '$type_user_id', channel_id = '$aca_id' WHERE per_id = '$id'");
                                    $update_q = $conn->update('cms_permissions', array('type_user_id' => $type_user_id, 'channel_id' => $aca_id), array('per_id' => $id));
                                    if($update_q){
                                      $message = '<div class="alert alert-success"><strong>Sucesso</strong> Os campos foram alterados com sucesso.</div>';
                                    } else {
                                      $message = '<div class="alert alert-danger"><strong>Oops!</strong> Algo de errado aconteceu. Contate o desenvolvedor.</div>';
                                    }
                                  }
                                } elseif($a == "f_insert") {
                                  $insert = $conn->insert('cms_permissions', array('type_user_id' => $type_user_id, 'channel_id' => $aca_id));
                                  if($insert) {
                                    $message = '<div class="alert alert-success"><strong>Sucesso</strong> A permissão da página foi associada ao nível com sucesso.</div>';
                                  } else {
                                    $message = '<div class="alert alert-danger"><strong>Oops!</strong> Algo de errado aconteceu. Contate o desenvolvedor.</div>';
                                  }
                                }
                              }

                              if($a == 'f_edit') {
                                $sql_f = "SELECT * FROM cms_permissions WHERE per_id='".$id."' LIMIT 1";
                                $row_f = $conn->first($sql_f);
                              }
                            ?>
                             <form method="post" class="form-horizontal" role="form" action="">
                                <div class="row">
                                  <div class="form-group">
                                    <div class="col-sm-12">
                                      <div class="card-box">
                                        <div class="row">
                                          <div class="col-md-6">

                                            <input type="hidden" name="a" value="<?= $a; ?>" />
                                            <input type="hidden" name="id" value="<?= $id; ?>" />
                                            <input type="hidden" value="<?= $tp_id; ?>" name="tp_id">

                                            <? if(isset($message)) echo $message; ?>
                                            <?
                                            $sql_usr = "SELECT * FROM cms_users_type";
                                            $res_usr = $conn->data($sql_usr);
                                            $sql_can = "SELECT * FROM cms_channels ORDER BY id ASC";
                                            $res_can = $conn->data($sql_can);
                                            ?>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Empresa</label>
                                                <div class="col-sm-9">
                                                  <select name="type_user_id" class="form-control">
                                                    <? foreach($res_usr as $row_usr){?>
                                                    <option value="<?= $row_usr->type_user_id; ?>" <? if($row_usr->type_user_id == $tp_id) echo 'selected="selected"';?>><?= $row_usr->type_title; ?></option>
                                                    <? }?>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Canal</label>
                                                <div class="col-sm-9">
                                                  <select name="aca_id" class="form-control">
                                                    <? foreach($res_can as $row_can) { ?>
                                                      <option value="<?= $row_can->id; ?>" <? if(isset($row_f->channel_id)): ?> <? if($row_f->channel_id == $row_can->id) echo 'selected="selected"';?><? endif; ?>><?= $row_can->title ?></option>
                                                    <? } ?>
                                                  </select>
                                                </div>
                                              </div>
                                            <button type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                </form>

                            <?
                            }
                            if($tp_id && $a == ""){
                              if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                foreach($_POST['del_item'] as $item) {
                                  $query = $conn->delete('cms_permissions', array('per_id' => $item));
                                }
                                if($query) {
                                  $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Os campos foram deletados com sucesso.</div>';
                                }
                                $a = "";
                              }
                            ?>
                                <? if(isset($message)) echo $message; ?>
                                <form name="lista" id="list-items" method="post" action="" onsubmit="return confirm_delete()">
                                <input type="Hidden" value="delete" name="a">
                                <input type="hidden" value="<?= $tp_id; ?>" name="tp_id">
                                <table class="table table-striped" id="datatable-editable">
	                                    <thead>
	                                        <tr>
	                                            <th><input id="selectall" type="checkbox" title="Select all"></th>
	                                            <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Deletar todos os registros selecionados"></i></a></th>
	                                            <th>Nível</th>
	                                            <th>Link curto</th>
	                                        </tr>
	                                    </thead>
                                      <?
                                      $sql = "SELECT * FROM cms_permissions p, cms_channels c, cms_users_type u
                                        WHERE p.type_user_id = '$tp_id' AND p.channel_id = c.id AND p.type_user_id = u.type_user_id
                                        ORDER BY p.per_id";
                                      $res = $conn->data($sql);
                                      ?>
	                                    <tbody>
                                          <? foreach($res as $usertype):
                                            $parent_menu = false;
                                            if($usertype->link == '0') {
                                              $parent_menu = true;
                                            }
                                          ?>
	                                        <tr <? if($parent_menu): ?>class="success"<? endif; ?>>
	                                            <td><input class="checkbox1" type="checkbox" name="del_item[]" value="<?= $usertype->per_id; ?>" title="Marque para deletar"></td>
	                                            <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $usertype->per_id; ?>&tp_id=<?=$tp_id?>" class="on-default remove-row"><i class="fa fa-pencil" title="Editar"></i></a></td>
	                                            <td><?= ($parent_menu ? '<b>'.$usertype->title.'</b>' : $usertype->title); ?></td>
	                                            <td><?= ($parent_menu ? '<b>#</b>' : $usertype->link); ?></td>
	                                        </tr>
                                          <? endforeach; ?>
	                                    </tbody>
	                                </table>
                                </form>
                            <?
                            } elseif($a == '') {
                              $sql = "SELECT * FROM cms_users_type ORDER BY type_title";
                              $res = $conn->data($sql);
                            ?>
                              Selecione um nível para alterar suas permissões.
                              <form>
                                  <select name="tp_id" onchange="select_tp(this.value)" class="form-control">
                                    <option value=""> -- </option>
                            <? foreach($res as $level): ?>
                              <option value="<?= $level->type_user_id; ?>"><?= $level->type_title; ?></option>
                            <? endforeach; ?>
                                    </select>
                                </form>
                            <? } ?>
