<? if(!isset($_SESSION['admin_id'])) exit; ?>
                                    <link href="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
                                    <link href="assets/plugins/switchery/dist/switchery.min.css" rel="stylesheet" />
                                    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
                                    <link href="assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" />
                                    <link href="assets/plugins/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" />
                                    <link href="assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
                                      

                                            <?php
                                            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                              $extensoes = array('.png', '.gif', '.jpg');
                                              $image = $_FILES['profileimage'];
                                              $extension = substr(strtolower($image['name']), -4);
                                              $errors = array();
                                              if(empty($image['name']) || $image === false) {
                                                $errors[] = 'Você deve enviar algo.';
                                              } else {
                                                $size = @getimagesize($image['tmp_name']);
                                                if(!$size || !is_file($image['tmp_name'])) {
                                                  $errors[] = 'O arquivo que você enviou não é uma imagem ou está corrompido.';
                                                } else {
                                                  if($size[0] > 2000 || $size[1] > 2000) {
                                                    $errors[] = 'A imagem não pode exceder 2000x2000px.';
                                                  }

                                                  if($image['size'] > ((1024*1000) * 2)) {
                                                    $errors[] = 'Sua imagem excedeu o máximo de 2MB.';
                                                  }

                                                  if(!in_array($extension, $extensoes)) {
                                                    $errors[] = 'A extensão do arquivo que você tentou enviar não é permitida. As únicas permitidas são:' . str_replace('.', '', strtoupper(implode(', ', $extensoes))) . '.';
                                                  }
                                                }
                                              }
                                              
                                              $message = '';
                                              if(count($errors)) {
                                                $message .= '<div class="alert alert-danger"><strong>Oops!</strong>';
                                                foreach($errors as $error) {
                                                  $message .= '<li>'.$error.'</li>';
                                                }
                                                $message .= '</div>';
                                              } else {
                                                $image_temp_name = 'up-'.substr(md5(time().uniqid()), 0, 8).$extension;
                                                $image_name_path = 'uploads/'.$image_temp_name;
                                                if(move_uploaded_file($image['tmp_name'], $image_name_path)) {
                                                  $profile_image = 'uploads/' . $image_temp_name;
                                                  $update = $conn->insert('cms_uploads', array('image' => $image_temp_name, 'author' => $user->data('username'), 'date' => time()));
                                                  $message = '<div class="alert alert-success"><strong>Sucesso!</strong> Seu upload foi feito com sucesso. Link da imagem: <a href="'.$profile_image.'" target="_blank"><img src="'.$profile_image.'" style="max-width:200px;" /></a></div>';
                                                }
                                              }
                                            }
                                            ?>
                                            <? if(isset($message)) echo $message; ?>
                                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                              <div class="form-group">
                                                <label class="col-md-2 control-label">Selecione</label>
                                                <div class="col-md-7">
                                                  <input type="file" name="profileimage" class="filestyle" data-buttonname="btn-white">
                                                </div>
                                              </div>
                                              <button id="enable" type="submit" class="btn btn-success waves-effect waves-light m-t-10">Enviar</button>
                                            </form>

                                          <table class="table table-striped" id="datatable-editable">
                                            <thead>
                                                <tr>
                                                  <th>Miniatura</th>
                                                  <th>Link</th>
                                                  <th>Usuário</th>
                                                  <th>Data</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $sql = "SELECT * FROM cms_uploads ORDER BY id DESC";
                                            $res = $conn->data($sql);
                                            ?>
                                            <tbody>
                                                <? foreach($res as $uploads): ?>
                                                <tr>
                                                  <td><a target="_blank" href="uploads/<?= $uploads->image; ?>"><img src="uploads/<?= $uploads->image; ?>" style="max-width:200px;" /></a></td>
                                                  <td><a target="_blank" href="uploads/<?= $uploads->image; ?>"><?= $uploads->image; ?></a></td>
                                                  <td><?= $uploads->author; ?></td>
                                                  <td><?= date('d/m/Y H:i', $uploads->date); ?></td>
                                                </tr>
                                                <? endforeach; ?>
                                            </tbody>
                                          </table>
                                      <script src="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
                                      <script src="assets/plugins/switchery/dist/switchery.min.js"></script>
                                      <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
                                      <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
                                      <script src="assets/plugins/select2/select2.min.js" type="text/javascript"></script>
                                      <script src="assets/plugins/bootstrap-select/dist/js/bootstrap-select.min.js" type="text/javascript"></script>
                                      <script src="assets/plugins/bootstrap-filestyle/src/bootstrap-filestyle.min.js" type="text/javascript"></script>
                                      <script src="assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js" type="text/javascript"></script>
                                      <script src="assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>

                                      <script>
                                      jQuery(document).ready(function() {

                                          //advance multiselect start
                                          $('#my_multi_select3').multiSelect({
                                              selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                                              selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                                              afterInit: function (ms) {
                                                  var that = this,
                                                      $selectableSearch = that.$selectableUl.prev(),
                                                      $selectionSearch = that.$selectionUl.prev(),
                                                      selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                                                      selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                                                  that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                                                      .on('keydown', function (e) {
                                                          if (e.which === 40) {
                                                              that.$selectableUl.focus();
                                                              return false;
                                                          }
                                                      });

                                                  that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                                                      .on('keydown', function (e) {
                                                          if (e.which == 40) {
                                                              that.$selectionUl.focus();
                                                              return false;
                                                          }
                                                      });
                                              },
                                              afterSelect: function () {
                                                  this.qs1.cache();
                                                  this.qs2.cache();
                                              },
                                              afterDeselect: function () {
                                                  this.qs1.cache();
                                                  this.qs2.cache();
                                              }
                                          });

                                          // Select2
                                          $(".select2").select2();

                                          $(".select2-limiting").select2({
                                    maximumSelectionLength: 2
                                  });

                                   $('.selectpicker').selectpicker();
                                        $(":file").filestyle({input: false});
                                        });

                                        //Bootstrap-TouchSpin
                                        $(".vertical-spin").TouchSpin({
                                          verticalbuttons: true,
                                          verticalupclass: 'ion-plus-round',
                                          verticaldownclass: 'ion-minus-round'
                                      });
                                      var vspinTrue = $(".vertical-spin").TouchSpin({
                                          verticalbuttons: true
                                      });
                                      if (vspinTrue) {
                                          $('.vertical-spin').prev('.bootstrap-touchspin-prefix').remove();
                                      }

                                      $("input[name='demo1']").TouchSpin({
                                          min: 0,
                                          max: 100,
                                          step: 0.1,
                                          decimals: 2,
                                          boostat: 5,
                                          maxboostedstep: 10,
                                          postfix: '%'
                                      });
                                      $("input[name='demo2']").TouchSpin({
                                          min: -1000000000,
                                          max: 1000000000,
                                          stepinterval: 50,
                                          maxboostedstep: 10000000,
                                          prefix: '$'
                                      });
                                      $("input[name='demo3']").TouchSpin();
                                      $("input[name='demo3_21']").TouchSpin({
                                          initval: 40
                                      });
                                      $("input[name='demo3_22']").TouchSpin({
                                          initval: 40
                                      });

                                      $("input[name='demo5']").TouchSpin({
                                          prefix: "pre",
                                          postfix: "post"
                                      });
                                      $("input[name='demo0']").TouchSpin({});


                                      //Bootstrap-MaxLength
                                      $('input#defaultconfig').maxlength()

                                      $('input#thresholdconfig').maxlength({
                                          threshold: 20
                                      });

                                      $('input#moreoptions').maxlength({
                                          alwaysShow: true,
                                          warningClass: "label label-success",
                                          limitReachedClass: "label label-danger"
                                      });

                                      $('input#alloptions').maxlength({
                                          alwaysShow: true,
                                          warningClass: "label label-success",
                                          limitReachedClass: "label label-danger",
                                          separator: ' out of ',
                                          preText: 'You typed ',
                                          postText: ' chars available.',
                                          validate: true
                                      });

                                      $('textarea#textarea').maxlength({
                                          alwaysShow: true
                                      });

                                      $('input#placement') .maxlength({
                                              alwaysShow: true,
                                              placement: 'top-left'
                                          });
                                  </script>
