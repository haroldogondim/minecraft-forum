var Login = {
	loading_register: false,
	
	facebook: function() {
			FB.login(function (response) {
				Login.loading_register = true;
				if (response.authResponse) {
					FB.api('/me?fields=id,name,email', function (response) {
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: '/ajax',
							data: {'login': true, 'user_id': response.id, 'email': response.email},
							success: function(html) {
								Login.loading_register = false;
                if(typeof html.banned === 'undefined') {
                  if(!html.save) {
                    location.href = "/";
                  } else {
                    location.reload();
                  }
                } else {
                  $.uiAlert({
                    textHead: 'Você foi banido!',
                    text: "Motivo: " + html.reason + "<br />" + "Seu banimento acaba em: " + html.ends,
                    bgcolor: '#F2711C',
                    textcolor: '#fff',
                    position: 'top-right',
                    icon: 'remove circle',
                    time: 3
                  });
                }
							},
              error: function(e) {
                console.log(e.responseText);
              }
						});
					});
				}
			}, {scope: 'email'});
	}
}

$(document).ready(function() {
  $('.masthead').visibility({
    once: false,
    onBottomPassed: function() {
      $('.fixed.menu').transition('fade in');
    },
    onBottomPassedReverse: function() {
      $('.fixed.menu').transition('fade out');
    }
  });
    
  $('.ui.sidebar').sidebar('attach events', '.toc.item');
    
    
  $('.ui.checkbox').checkbox();
  
  $('#modalLogin').modal('attach events', '#login-button', 'show');
  var handler;
  
  $('#loggedIn').popup({
    hoverable: true,
    delay: {
      show: 1,
      hide: 300
    }
  });
});



window.fbAsyncInit = function() {
  FB.init({
    appId      : '184429495657517',
    cookie     : true,
    xfbml      : true,
    version    : 'v2.12'
  });
    
  FB.AppEvents.logPageView();   
    
};

(function(d, s, id){
   var js, fjs = d.getElementsByTagName(s)[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement(s); js.id = id;
   js.src = "https://connect.facebook.net/en_US/sdk.js";
   fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));
 
 function checkLoginState() {
  FB.getLoginStatus(function(response) {
    console.log(response);
    console.log(FB.getLoginStatus);
  });
}