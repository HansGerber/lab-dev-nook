<?php require_once "_header.php"; ?>

<div class="contBox">
        <?php 
            if($result["success"] == true){
        ?>
	<h1>Contact sent</h1>
        <?php
            } else {
        ?>
        <h1>Error while sending contact.</h1>
        <?php
            }
        ?>
</div>
<?php require_once "_footer.php"; ?>