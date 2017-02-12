			</div>
                        <div class="clear"></div>
                        <div id="footer" class="boxShadow">
                            <a class="right" href="<?php echo makePath("contact"); ?>">contact</a>
                            <div class="clear"></div>
                        </div>
		</div>
                <script>
                    function setContMinHeight(){
                        document.getElementById("content").style.minHeight = (window.innerHeight - 150) + "px";
                    }
                    setContMinHeight();
                    addEventListener("resize", function() {
                        setContMinHeight();
                    }, false);
		</script>
	</body>
</html>
