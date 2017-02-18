<?php require_once "_header.php"; ?>

<div class="contBox">
        <h1>Contact</h1>
        <?php 
            if($result["success"] == true){
        ?>
        <strong>Message sent</strong>
        <?php
            } else {
        ?>
        
            <strong>Failed to send message</strong><br><br>
            Have you filled in all form fields?<br>
            If yes this might fail due to technical difficulties and we kindly ask you to try it again in a few minutes.<br><br>
            Sorry for the inconvinience
        
        <?php
            }
        ?>
</div>
<?php require_once "_footer.php"; ?>