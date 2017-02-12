<!DOCTYPE html>
<html>
	<head>
		<title>Lab - DevNook</title>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body>
		<div id="page" class="boxShadow">
			<div id="header" class="boxShadow">
                            <h1><span class="lab">lab</span>.dev-nook.de</h1>
			</div>
			<div id="navi">
                                <a class="<?php if(getPath() == "/"){echo "active";} ?>" href="<?php echo makePath(); ?>">Home</a>
                                <a class="<?php if(getPath() == "/downloads"){echo "active";} ?>" href="<?php echo makePath("downloads"); ?>">Downloads</a>
                                <a class="<?php if(getPath() == "/links"){echo "active";} ?>" href="<?php echo makePath("links"); ?>">Links</a>
			</div>
			<div id="content" class="boxShadow">
