<div class="ui container vertical" id="topic-create">
  <? if(!User::loggedIn()): echo message('Oops!', 'Você precisa fazer <a href="/login">login</a> ou <a href="/login">cadastre-se</a> para acessar esta página.', 'remove circle', 'error'); else: ?>
  <script>
  $(document).ready(function() {
    $('input[type="file"]').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#label-file").html("<i class='icon file'></i> &nbsp;&nbsp;" + fileName);
    });
  });
  </script>
  <div class="ui basic segment">
    <?= Settings::changePicture(); ?>
    <form action="" enctype="multipart/form-data" name="form-avatar" id="form-avatar" method="post" class="ui reply form">
      <div class="field">
        <label>Seu avatar atual</label>
        <img src="/assets/uploads/avatar/<?= User::data('avatar'); ?>" style="width:200px; max-width:100%;" />
      </div> 
      <div class="field">
        <div class="eight wide field">
          <label for="file" class="ui icon button" id="label-file"><i class="file icon"></i>&nbsp;&nbsp;Selecione uma imagem</label>
          <input type="file" name="imagem" id="file" style="display:none">
        </div>
      </div>
      <button class="ui blue button" type="submit" name="submit-avatar"><i class="reply icon"></i> Enviar</button>
    </form>
  </div>
  <? endif; ?>
</div>