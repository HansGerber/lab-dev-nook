<script>

function fbGetUserData(callback) {
	FB.api('/me', {fields: 'name, email'}, function(response) {
		if(typeof callback == "function"){
			callback(response);
		}
	});
}

function checkLoginState() {
	FB.getLoginStatus(function(response) {
		statusChangeCallback(response);
	});
}

function statusChangeCallback(response) {
	console.log(response);
	if (response.status === 'connected') {
		fbGetUserData(function(r){
			console.log(r);
		});
	} else {
		console.log("Login-Status : " + response.status);
	}
}
	
// Load the SDK asynchronously
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
	
 window.fbAsyncInit = function() {
  FB.init({
    appId      : '1265075126870179',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.8' // use graph api version 2.8
  });
  
	checkLoginState();
 }

</script>
<?php

$_table = $_c->query("select * from $_db.fbLoginTest_users");
echo '<div style="padding:10px;margin-bottom:10px;border:solid 1px #ddd;background:#eee;"><h3>User List :</h3>';
while($_r = $_table->fetch_assoc()){
	echo $_r["email"];
	echo "<br>";
}
$_table->free_result();
echo '</div>';

if(hasFlashMessage()){
	echo '<p>'.getFlashMessage().'</p>';
}

echo renderLoginModule();

?>