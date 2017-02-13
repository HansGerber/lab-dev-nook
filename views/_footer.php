			</div>
                        <div class="clear"></div>
                        <div id="footer" class="boxShadow">
                            <a class="right" href="<?php echo makePath("contact"); ?>">contact</a>
                            <div class="clear"></div>
                        </div>
		</div>
                <script>
                    function setContMinHeight(){
                        var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                        if(w >= 800){
                            document.getElementById("content").style.minHeight = (window.innerHeight - 150) + "px";
                        } else {
                            document.getElementById("content").style.minHeight = "0";
                        }
                    }
                    setContMinHeight();
                    addEventListener("resize", function() {
                        setContMinHeight();
                    }, false);
		</script>
	</body>
</html>
