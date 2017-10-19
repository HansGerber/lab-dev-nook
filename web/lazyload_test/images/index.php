<html>
	<head>
		<title>Lazy Test</title>
		<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
		<script src="jquery.lazy.js"></script>
		<style>
			#test {
				height:400px;
				width:500px;
				overflow:scroll;
				border:solid 1px #306;
			}
		</style>
	</head>
	<body>
		<div id="test">
			<div>
				<figure class="grid-item_">
					<img data-original="dummy5.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure>
				<figure class="grid-item_">
					<img data-original="dummy6.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure>
				<figure class="grid-item_">
					<img data-original="dummy7.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure>
				<figure class="grid-item_">
					<img data-original="dummy8.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure>
				<figure class="grid-item_">
					<img data-original="dummy9.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure><figure class="grid-item_">
					<img data-original="dummy10.jpg" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure><figure class="grid-item_">
					<img data-original="dummy11.png" width="200" height="200" alt="Rainbow near Keswick" class="lazy" />
					<figcaption class="blue">Anna W.</figcaption>
				</figure>
			</div>
		</div>
		<script>
		$("img.lazy").lazyload({
			container: $("#test")
		});
		</script>
	</body>
</html>
