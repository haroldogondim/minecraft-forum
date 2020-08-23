<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? $system_pages = array('1', '5', '6', '7', '8', '12'); ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Adicionar páginas</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de páginas</button></a><? endif; ?>
                              <br /><br />
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $checkFile = $conn->first("SELECT * FROM cms_channels WHERE id = '$id'");
                                      if($id == '4') {
                                        echo '<script>$(document).ready(function() { swal("Você não pode deletar essa página."); }); </script>';
                                      } else {

                                        $delete = $conn->delete('cms_channels', array('id' => $id));
                                        echo '<script>$(document).ready(function() { swal({   title: "Sucesso",   text: "A página foi deletada com sucesso!",   type: "warning",   showCancelButton: false,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Ok",   closeOnConfirm: false }, function() {   window.location.href = \'?p='.$page.'\';}); }); </script>';
                                      }
                                  }
                                  ?>
                                  <div class="alert alert-success"><strong>Verde significa que é um menu principal.</strong></div>
                                  <div class="alert alert-info"><strong>Azul significa que é uma página do sistema e não pode ser alterada.</strong></div>
                                  <table class="table table-striped" id="datatable-editable">
                                    <thead>
                                        <tr>
                                          <th><a id="list-items-button" class="on-default remove-row"><i class="fa fa-trash-o" title="Delete all selected registers"></i></a></th>
                                          <td><a class="on-default remove-row"><i class="fa fa-pencil"></i></a></td>
                                          <th>Nome</th>
                                          <th>Link</th>
                                          <th>Submenu</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql = "SELECT * FROM cms_channels WHERE id <> 4";
                                    $res = $conn->data($sql);
                                    ?>
                                    <script>
                                    $(document).ready(function() {
                                      $('.delete-items').click(function() {
                                        var href = $(this).data('href');
                                        if(href != '#') {
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
                                        }
                                      });
                                    });
                                    </script>
                                    <tbody>
                                        <?
                                        foreach($res as $channel):
                                          $parent_menu = false;
                                          if($channel->parent == '0') {
                                            $parent_menu = '<strong>Menu principal</strong>';
                                          } elseif(count($data_channels = $conn->first("SELECT * FROM cms_channels WHERE id = ?", array($channel->parent)))) {
                                            $parent_menu = $data_channels->title;
                                          }
                                        ?>

                                        <tr<? if($channel->parent == '0'): ?> class="success"<? endif; ?>>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $channel->id; ?>" title="Remover" style="cursor:pointer;" class="on-default remove-row delete-items"><i class="fa fa-trash-o"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $channel->id; ?>" title="Editar" class="on-default remove-row"><i class="fa fa-pencil"></i></a></td>
                                          <td><?= $channel->title; ?></td>
                                          <td><?= ($channel->parent == '0' ? '<strong>#<strong>' : $channel->link); ?></td>
                                          <td><?= $parent_menu; ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                <? } elseif($a == 'insert') { ?>
                                      <?php
                                      if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        $title = post('title');
                                        $link = post('link');
                                        $parent = post('parent');
                                        if($parent == '0') {
                                          $link = '#';
                                        }
                                        if(empty($title) || empty($page)) {
                                          $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                        } else {
                                          $insert_user = $conn->insert('cms_channels', array('title' => $title, 'link' => $link, 'parent' => $parent));
                                          $message = '<div class="alert alert-success"><strong>Sucess!</strong> Pagina criada com sucesso.</div>';
                                        }
                                      }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <div class="col-sm-6">
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post" role="form">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Título</label>
                                                    <div class="col-md-10">
                                                        <input type="text" name="title" required="" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-2 control-label">Página</label>
                                                  <div class="col-md-10">
                                                    <select name="parent" class="form-control">
                                                      <option value="0" selected="selected" style="color:green;">Menu principal</option>
                                                      <?php
                                                      $sql_can = "SELECT * FROM cms_channels WHERE parent = 0 AND id != 4 ORDER BY title";
                                                      $res_can = $conn->data($sql_can);
                                                      foreach($res_can as $row_can) {
                                                      ?>
                                                        <option value="<?= $row_can->id; ?>">Submenu de "<?= $row_can->title ?>"</option>
                                                      <? } ?>
                                                    </select>
                                                    <span class="help-block"><small>Uma vez que você selecionar <b>Menu principal</b>, você deve ignorar o conteúdo da página, pois será inútil.</small></span>
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Link</label>
                                                    <div class="col-md-10">
                                                        <input type="text" name="link" required="" class="form-control" value="">
                                                        <span class="help-block"><small>Digite o nome do arquivo sem a extensão.</small></span>
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
                                      $channel_data = $conn->first("SELECT * FROM cms_channels WHERE id = ?", array($id));
                                      if(isset($channel_data->id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $title = post('title');
                                          $link = post('link');
                                          $parent = post('parent');
                                          if($parent == '0') {
                                            $link = '#';
                                          }
                                          if(empty($title)) {
                                            $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                          } else {
                                            $insert_user = $conn->update('cms_channels', array('title' => $title, 'link' => $link, 'parent' => $parent), array('id' => $id));
                                            $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Página editada com sucesso.</div>';
                                            $channel_data = $conn->first("SELECT * FROM cms_channels WHERE id = ?", array($id));
                                          }
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">

                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post">
                                              <div class="col-md-6">
                                                <div class="form-group">
                                                  <label class="col-md-2 control-label">Título</label>
                                                  <div class="col-md-10">
                                                    <input type="text" name="title" required="" class="form-control" value="<?= $channel_data->title; ?>">
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-2 control-label">Página</label>
                                                  <div class="col-md-10">
                                                      <select name="parent" class="form-control">
                                                      <?= $channel_data->parent; ?>
                                                      <option value="0" <? if($channel_data->parent == '0'): ?>selected="selected"<? endif; ?> style="color:green;">Menu principal</option>
                                                      <?php
                                                      $sql_can = "SELECT * FROM cms_channels WHERE parent = 0 AND id != 4 ORDER BY title";
                                                      $res_can = $conn->data($sql_can);
                                                      foreach($res_can as $row_can) {
                                                      ?>
                                                        <option value="<?= $row_can->id; ?>" <? if($channel_data->parent == $row_can->id): ?>selected="selected"<? endif; ?>>Child for "<?= $row_can->title ?>"</option>
                                                      <? } ?>
                                                    </select>
                                                    <span class="help-block"><small>Uma vez que você selecionar <b>Menu principal</b>, você deve ignorar o conteúdo da página, pois será inútil.</small></span>
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-md-2 control-label">Link</label>
                                                  <div class="col-md-10">
                                                    <input type="text" name="link" required="" class="form-control" value="<?= $channel_data->link; ?>">
                                                    <span class="help-block"><small>Digite o nome do arquivo sem a extensão.</small></span>
                                                  </div>
                                                </div>
                                                <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                              </div>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                      <? } else { ?>
                                      <div class="card-box">
                                        <div class="row">
                                            <div class="alert alert-danger"><strong>Oops!</strong> A página que você está tentando editar não existe.</div>
                                        </div>
                                      </div>
                                      <? } ?>
                                <?php } ?>

                                <script src="assets/plugins/tinymce/tinymce.min.js"></script>

                                <script type="text/javascript">
                                  $(document).ready(function () {
                                  if($("#elm1").length > 0){
                                      tinymce.init({
                                          selector: "textarea#elm1",
                                          theme: "modern",
                                          height:300,
                                          plugins: [
                                              "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                                              "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                                              "save table contextmenu directionality emoticons template paste textcolor"
                                          ],
                                          toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
                                          style_formats: [
                                              {title: 'Bold text', inline: 'b'},
                                              {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                                              {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                                              {title: 'Example 1', inline: 'span', classes: 'example1'},
                                              {title: 'Example 2', inline: 'span', classes: 'example2'},
                                              {title: 'Table styles'},
                                              {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                                          ]
                                      });
                                  }
                              });
                                </script>
