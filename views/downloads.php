<?php require_once "_header.php"; ?>
<div class="contBox">
	<h1>Downloads</h1>
        <h3>(speed - Test File)</h3>
        <div>
            <audio controls>
                <source src="<?php echo makePath("assets/audio/New6_9.MP3"); ?>">
            </audio>
        </div>
        <h3>(speed - Test File 2)</h3>
        <div>
            <audio controls>
                <source src="<?php echo makePath("assets/audio/New99.MP3"); ?>">
            </audio>
        </div>
</div>
<script>
    var as = document.getElementsByTagName("audio");
    for(var i = 0; i < as.length; i++){
        as[i].setAttribute("data-count", i);
        as[i].addEventListener("play", function() {
            var _as = document.getElementsByTagName("audio");
            for(var _i = 0; _i < _as.length; _i++){
                if(_i != this.getAttribute("data-count")){
                    _as[_i].pause();
                }
            }
        }, false);
    }
</script>
<?php require_once "_footer.php"; ?>