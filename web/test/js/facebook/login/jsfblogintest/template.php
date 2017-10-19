<script>

function loginUser(data, callback){
	if(
		typeof data != "undefined" &&
		typeof data.name != "undefined" &&
		typeof data.id != "undefined"
	){
		var ajax = new XMLHttpRequest();
		var strQueryData = "id=" + data.id + "&name=" + data.name;
		
		if(typeof data.email != "undfined"){
			strQueryData += "&email=" + data.email;
		}
		
		ajax.open("POST", "fbRegistration.php");
		ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4){
				if(ajax.status == 200){
					if(typeof callback == "function"){
						callback(ajax.responseText, ajax.status);
					}
				}
			}
		}
		
		ajax.send(strQueryData);
	}
}

function fbLogin(){
	FB.login(function(response) {
		if (response.authResponse) {
		 console.log('Welcome!  Fetching your information.... ');
			FB.api('/me', {fields: 'email, name'}, function(response) {
				console.log('data :', response);
				
				loginUser(
					{
						id: response.id,
						name: response.name,
						email: response.email
					},
					function(loginResponse) {
						console.log(loginResponse);
						var jsonLoginResponse = JSON.parse(loginResponse);
						if(jsonLoginResponse.success == "true"){
							location.reload(true);
						}
					}
				);
			});
		} else {
		 console.log('User cancelled login or did not fully authorize.');
		}
	});
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