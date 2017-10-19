<!DOCTYPE html>
<html>
	<head>
		<title>Meme Search</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/main.css">
	</head>
	<body>
		<div id="headerWrap">
			<div id="header">
				<h1>Meme Finder</h1>
				<h2 id="memesInfo"></h2>
				<div id="controls">
					<input
					 onfocus="this.select();"
					 onclick="this.select();"
					 type="text"
					 id="tagSearchText"
					 placeholder="Tag"
					>
					<button
					 onclick="loadMemes(document.getElementById('tagSearchText').value)">
						Search
					</button>
				</div>
			</div>
		</div>
		<div id="container">
			<div id="initialText">Type in one or multiple tags (seperated by ',') and hit 'Search' or Enter</div>
			<div id="memeLoader">
				<img src="images/memeLoader.gif">
			</div>
			<div id="memes"></div>
			<div id="flashMessage"></div>
		</div>
		<div id="footer">
			Contact/Feedback : <a href="mailto:memefulsearch.contact@gmail.com">memefulsearch.contact@gmail.com</a>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/8.0.1/lazyload.min.js"></script>
		<script src="js/main.js"></script>
		<script>
			initMemeSearch();
		</script>
	</body>
</html>
