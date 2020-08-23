<? if(!isset($_SESSION['admin_id'])) exit; ?>
                              <? if(get('a') != 'insert'): ?><a href="?p=<?= $page; ?>&a=insert"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Adicionar artigo</button></a><? endif; ?>
                              <? if(get('a')): ?><a href="?p=<?= $page; ?>"><button id="enable" type="button" class="btn btn-success waves-effect waves-light m-t-10">Lista de artigos</button></a><? endif; ?>
                              <?php
                              $a = get('a');
                              if($a == '') {
                              ?>
                                  <?
                                  if(get('delete')) {
                                    $id = get('delete');
                                    $delete = $conn->delete('articles', array('id' => $id));
                                    echo '<script>$(document).ready(function() { swal({   title: "Sucesso",   text: "Esta notícia foi deletada com sucesso!",   type: "warning",   showCancelButton: false,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Ok",   closeOnConfirm: false }, function() {   window.location.href = \'?p='.$page.'\';}); }); </script>';
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
                                          <th>Título</th>
                                          <th>Autor</th>
                                          <th>Data</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql = "SELECT * FROM articles ORDER BY id DESC";
                                    $res = $conn->data($sql);
                                    ?>
                                    <tbody>
                                        <? foreach($res as $articles): ?>
                                        <?php
                                        ?>
                                        <tr <? if(!$articles->status): ?>class="warning"<? endif; ?>>
                                          <th><a data-href="?p=<?= $page; ?>&delete=<?= $articles->id; ?>" class="on-default remove-row delete-items" style="cursor:pointer;"><i class="fa fa-trash-o" title="Delete"></i></a></th>
                                          <td><a href="?p=<?= $page; ?>&a=edit&id=<?= $articles->id; ?>" class="on-default remove-row"><i class="fa fa-pencil" title="Edit"></i></a></td>
                                          <td><?= $articles->title; ?></td>
                                          <td><?= $articles->author; ?></td>
                                          <td><?= date('d/m/Y H:i', $articles->date); ?></td>
                                        </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                  </table>
                                <? } elseif($a == 'insert') { ?>
                                      <?php
                                      if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        $title = post('title');
                                        $resume = post('resume');
                                        $content = post('area');
                                        $image = post('image');
                                        $imageFeatured = post('imageFeatured');
                                        if(empty($title) || empty($resume) || empty($content) || empty($image)) {
                                          $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                        } else {
                                          $insert_user = $conn->insert('articles', array('title' => $title, 'image' => $image, 'imageFeatured' => $imageFeatured, 'resume' => $resume, 'content' => $content, 'author' => $user->data('username'), 'date' => time(), 'status' => false));
                                          $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Notícia criada com sucesso.</div>';
                                        }
                                      }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                          <form class="form-horizontal" action="" method="post" role="form">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Título*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="title" required="" class="form-control" value="">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Resumo*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="resume" required="" type="password" class="form-control" value="">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Imagem (192x106px)*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="image" required="" type="password" class="form-control" value="">
                                                      <span class="help-block"><small>Insira apenas o link.</small>
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Imagem fixa (338x194px)</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="imageFeatured" required="" type="password" class="form-control" value="">
                                                      <span class="help-block"><small>Insira apenas o link. Imagem grande para as notícias fixas. Campo não obrigatório.</small>
	                                                </div>
	                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-sm-12">
                                                <div class="card-box">
                                                  <textarea id="elm1" name="area"><?= $channel_data->page; ?></textarea>
                                                </div>
                                              </div>
                                            </div>
                                            <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                          </div>
                                          </form>
                                        </div>
                                      </div>
                                      <? } elseif($a == 'edit') { ?>
                                      <?php
                                      $id = get('id');
                                      $articles_data = $conn->first("SELECT * FROM articles WHERE id = ?", array($id));
                                      if(isset($articles_data->id)) {
                                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                          $title = post('title');
                                          $resume = post('resume');
                                          $content = post('area');
                                          $image = post('image');
                                          $imageFeatured = post('imageFeatured');
                                          $status = (post('status') == '1' ? '1' : '0');
                                          $fixed = (post('fixed') == '1' ? '1' : '0');
                                          if(empty($title) || empty($resume) || empty($content) || empty($image)) {
                                            $message = '<div class="alert alert-danger"><strong>Oops!</strong> Você deve preencher todos os campos.</div>';
                                          } else {
                                            $insert_user = $conn->update('articles', array('title' => $title, 'image' => $image, 'imageFeatured' => $imageFeatured, 'status' => $status, 'fixed' => $fixed, 'resume' => $resume, 'content' => $content, 'author' => $user->data('username'), 'date' => time()), array('id' => $articles_data->id));
                                            $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Notícia editada com sucesso.</div>';
                                            $articles_data = $conn->first("SELECT * FROM articles WHERE id = ?", array($id));
                                          }
                                        }
                                      ?>
                                      <div class="card-box">
                                        <div class="row">
                                        <form class="form-horizontal" action="" method="post" role="form">
                                          <div class="col-md-6">
                                            <? if(isset($message)) echo $message; ?>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Título*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="title" required="" class="form-control" value="<?= $articles_data->title; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Resumo*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="resume" required="" type="password" class="form-control" value="<?= $articles_data->resume; ?>">
	                                                </div>
	                                            </div>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-9">
                                                  <select name="status" class="form-control">
                                                    <option <? if($articles_data->status == '1'): ?>selected<? endif;?> value="1">Ativa</option>
                                                    <option <? if($articles_data->status == '0'): ?>selected<? endif;?> value="0">Inativa</option>
                                                  </select>
                                                  <span class="help-block"><small>Uma vez que o usuário esteja com status "Inativo", ele não será capaz de logar no painel.</small>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Tipo de notícia</label>
                                                <div class="col-sm-9">
                                                  <select name="fixed" class="form-control">
                                                    <option <? if($articles_data->fixed == '1'): ?>selected<? endif;?> value="1">Fixa</option>
                                                    <option <? if($articles_data->fixed == '0'): ?>selected<? endif;?> value="0">Normal</option>
                                                  </select>
                                                  <span class="help-block"><small>Caso a notícia seja fixa, preencha o campo "Imagem fixa".</small>
                                                </div>
                                              </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Imagem (192x106px)*</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="image" required="" type="password" class="form-control" value="<?= $articles_data->image; ?>">
                                                      <span class="help-block"><small>Insira apenas o link.</small>
	                                                </div>
	                                            </div>
                                              <div class="form-group">
	                                                <label class="col-md-2 control-label">Imagem fixa (338x194px)</label>
	                                                <div class="col-md-9">
	                                                    <input type="text" name="imageFeatured" required="" type="password" class="form-control" value="<?= $articles_data->imageFeatured; ?>">
                                                      <span class="help-block"><small>Insira apenas o link. Imagem grande para as notícias fixas. Campo não obrigatório.</small>
	                                                </div>
	                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-sm-12">
                                                <div class="card-box">
                                                  <textarea id="elm1" name="area"><?= $articles_data->content; ?></textarea>
                                                </div>
                                              </div>
                                            </div>
                                            <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
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