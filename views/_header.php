<!DOCTYPE html>
<html>
	<head>
		<title>Lab - DevNook</title>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body>
            <noscript>
            Javascript needs to be enabled when using this site.<br>
            </noscript>
            <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-80065119-2', 'auto');
            ga('send', 'pageview');

            </script>
		<div id="page" class="boxShadow">
			<div id="header" class="boxShadow">
                            <h1><a href="<?php echo makePath(); ?>"><span class="lab">lab</span>.dev-nook.de</a></h1>
			</div>
			<div id="navi">
                                <a class="<?php if(getPath() == "/"){echo "active";} ?>" href="<?php echo makePath(); ?>">Home</a>
                                <a class="<?php if(getPath() == "/downloads"){echo "active";} ?>" href="<?php echo makePath("downloads"); ?>">Downloads</a>
                                <a class="boxShadow <?php if(getPath() == "/links"){echo "active";} ?>" href="<?php echo makePath("links"); ?>">Links</a>
			</div>
			<div id="content" class="boxShadow">
