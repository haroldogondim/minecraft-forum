$.uiAlert = function(options) {
  var setUI = $.extend({
    textHead: 'Your user registration was successful.',
    text: 'You may now log-in with the username you have chosen',
    textcolor: '#19c3aa',
    bgcolors: '#fff',
    position: 'top-right',
    icon: '',
    time: 5,
    permanent: false
  }, options);

    var ui_alert = 'ui-alert-content';
      ui_alert += '-' + setUI.position;
      setUI.bgcolors ='style="background-color: '+setUI.bgcolor+';   box-shadow: 0 0 0 1px rgba(255,255,255,.5) inset,0 0 0 0 transparent;"';
      if(setUI.bgcolors === '') setUI.bgcolors ='style="background-color: ; box-shadow: 0 0 0 1px rgba(255,255,255,.5) inset,0 0 0 0 transparent;"';
    if(!$('body > .' + ui_alert).length) {
      $('body').append('<div class="ui-alert-content ' + ui_alert + '" style="width: inherit;"></div>');
    }
    var message = $('<div id="messages" class="ui icon message" ' + setUI.bgcolors + '><i class="'+setUI.icon+' icon" style="color: '+setUI.textcolor+';"></i><i class="close icon" style="color: '+setUI.textcolor+';" id="messageclose"></i><div style="color: '+setUI.textcolor+'; margin-right: 10px;">   <div class="header">'+setUI.textHead+'</div>  <p> '+setUI.text+'</p></div>  </div>');
    $('.' + ui_alert).prepend(message);
    message.animate({
      opacity: '1',
    }, 300);
    if(setUI.permanent === false){
      var timer = 0;
      $(message).mouseenter(function(){
        clearTimeout(timer);
      }).mouseleave(function(){
        uiAlertHide();
      });
      uiAlertHide();
    }
    function uiAlertHide(){
      timer = setTimeout(function() {
        message.animate({
          opacity: '0',
        }, 300, function() {
          message.remove();
        });
      }, (setUI.time * 1000) );
    }

    $('#messageclose')
    .on('click', function() {
      $(this)
        .closest('#messages')
        .transition('fade')
      ;
    })
  ;

};

function selecao(obj, def_texto_padrao) {
    if(obj.constructor == String){obj = document.getElementById(obj);}
    var def_texto = (def_texto_padrao) ? function(text){obj.value += text;} : function(){return false;};
    var selecao = {text: "", defTexto: def_texto};
    if(document.selection){
        var faixa = document.selection.createRange();
        if(faixa.text){
            selecao.text = faixa.text;
            selecao.defTexto = function(text){
                faixa.text = text.replace(/\r?\n/g, "\r\n");
            }
    }
    } else if(typeof(obj.selectionStart) != "undefined"){
        selecao.text = obj.value.substring(obj.selectionStart, obj.selectionEnd);
        selecao.defTexto = function(text){
            obj.value = obj.value.substring(0, obj.selectionStart) + text + obj.value.substring(obj.selectionEnd);
            return false;
        }
    } else if(window.getSelection){
        selecao.text = window.getSelection().toString();
    }
    return selecao;
}
function setBBCode(obj, antes, depois){
    var selecionado = selecao(obj, false);
    selecionado.defTexto(antes + selecionado.text + depois);
    $('#'+obj).focus();
}
function setAltBBCode(obj, tipo, objthis){
    valor = objthis.value;
    var selecionado = selecao(obj, true);
    selecionado.defTexto('['+tipo+'='+valor+']' + selecionado.text + '[/'+tipo+']');
    objthis.value='';
    $('#'+obj).focus();
}