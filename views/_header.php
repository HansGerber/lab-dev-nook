<!DOCTYPE html>
<html>
	<head>
		<title>Lab - DevNook</title>
                <link rel="stylesheet" href="<?php echo makePath('assets/css/main.css'); ?>?201702191" />
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
                                <a class="<?php if(getPath() == "/games"){echo "active";} ?>" href="<?php echo makePath("games"); ?>">Games</a>
                                <a class="<?php if(getPath() == "/contact"){echo "active";} ?>" href="<?php echo makePath("contact"); ?>">Contact</a>
                                <a class="<?php if(getPath() == "/upload-test"){echo "active";} ?>" href="<?php echo makePath("upload-test"); ?>">Upload</a>
                                <a class="boxShadow <?php if(getPath() == "/links"){echo "active";} ?>" href="<?php echo makePath("links"); ?>">Links</a>
			</div>
			<div id="content" class="boxShadow">
                            <div id="googleadsense">
                                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                <script>
                                  (adsbygoogle = window.adsbygoogle || []).push({
                                    google_ad_client: "ca-pub-1906246425506748",
                                    enable_page_level_ads: true
                                  });
                                </script>
                            </div>
