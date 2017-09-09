<?php
    session_start();
    
    function isLoggedIn(){
        if(isset($_SESSION["user"])){
           return true;
        }
        return false;
    }
    
    function logOut(){
        if(isLoggedIn()){
            unset($_SESSION["user"]);
        }
    }
    
    if(isLoggedIn()){
?>
<form method="post">
    <input type="submit" value="logout">
</form>
<?php
    } else {
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.10&appId=1265075126870179";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div
 class="fb-login-button"
 data-max-rows="1"
 data-size="large"
 data-button-type="continue_with"
 data-show-faces="false"
 data-auto-logout-link="false"
 data-use-continue-as="false"
 onlogin="fbLogin"></div>

<script>
function fbLogin(data){
    console.log(data);
}
</script>
<?php
    }
?>