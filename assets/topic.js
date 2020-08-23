var Topic = {
  init: function() {
    $("#submit-comment").click(function() {
      $('form[name=form-comment]').submit();
    });
  },
  
  quote: function(id) {
    $.ajax({
      url: "/ajax",
      type: "POST",
      data: {'quote': true, "id": id},
    }).done(function(data) {
      $("#comment-field").val($("#comment-field").val() + data);
    });
  },
  
  save: function(id) {
      $.ajax({
      url: "/ajax",
      type: "POST",
      dataType: 'json',
      data: {'save': true, "id": id},
    }).done(function(data) {
      $.uiAlert({
        textHead: data.title,
        text: data.text,
        bgcolor: data.color,
        textcolor: '#fff',
        position: 'top-right',
        icon: 'checkmark box',
        time: 3
      });
    }).error(function(r) {
      console.log('e:' + r.responseText);
    });
  },
  
  editComment: function(id) {
    $('.ui.modal.edit-post').modal({
      approve  : '.positive, .approve, .ok',
      deny     : '.negative, .deny, .cancel',
      onApprove: function() {
        $.ajax({
          type: 'POST',
          data: {'edit_comment': true, 'id': id, 'comment': $('#post-edit-content').val()},
          url: '/plus/comment.json',
        }).done(function(data) {
          $('#content-'+id).html(data);
          $(this).modal('close');
        }).error(function(e) {
          console.log('e: ' + e.responseText);
        });
      },
      onDeny: function() {
        $(this).modal('close');
      },
      onShow: function() {
        $('#post-edit-content').val('');
        $.ajax({
          type: 'POST',
          data: {'load_comment': true, 'id': id},
          url: '/plus/comment.json',
          beforeSend: function() {
            $('#post-edit-content').val('Carregando...').attr('readonly', 'readonly');
          }
        }).done(function(data) {
          $('#post-edit-content').val(data).removeAttr('readonly');
        }).error(function(e) {
          console.log(e.responseText);
        });
      }
    }).modal('show');
  },
  
  removePostOrComment: function(id) {
    $('.ui.small.basic.test.modal').modal({
      closable : true,
      onDeny: function() {
      },
      onApprove: function() {
        $.ajax({
          type: 'POST',
          data: {'removeComment': true, 'id': id},
          url: '/plus/comment.json',
        }).done(function(data) {
          addNotification(data, 'Por: SuperPRO', 3);
          $(this).modal('close');
        }).error(function(e) {
          console.log('e: ' + e.responseText);
        });
      }
    }).modal('show');
  },
  
  preview: function() {
    var comment = $('#comment-field').val();
    $.ajax({
      url: "/ajax",
      type: "POST",
      data: {'preview': true, "comment": comment},
    }).done(function(data) {
      var dataComment = 
      '<div class="ui basic segment" id="comment-comment">' +
        '<div class="ui fluid card">' +
          '<div class="content">' +
            '<div class="content" id="content-comment-comment">' +
              '<div class="row">'+ data +'</div>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
      $("#preview-comment").show().html(dataComment);
    }).error(function(e) {
      console.log('error:' + e.responseText);
    });
  }
}

$(document).ready(Topic.init);