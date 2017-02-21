<?php require_once "_header.php"; ?>
<style>
    #messageForm > div {
        padding:10px 0;
    }
    #messageForm input[type=text],
    #messageForm select,
    #messageForm textarea {
        width: 100%;
        border:solid 1px #ccc;
        background:#fff;
    }
</style>
<div class="contBox">
	<h1>Contact</h1>
        <form method="post" id="messageForm">
            <div>
                <label>Full Name</label><br />
                <input type="text" name="contact[name]" />
            </div>
            <div>
                <label>E-Mail</label><br />
                <input type="text" name="contact[email]" />
            </div>
            <div>
                <label>Topic</label><br />
                <select name="contact[topic]">
                    <option value="0">- select -</option>
                    <option value="1">Admin Request</option>
                    <option value="2">other</option>
                </select> 
            </div>
            <div>
                <label>Message</label><br />
                <textarea name="contact[message]"></textarea>
            </div>
            <?php printCaptcha(); ?>
            <input type="submit" value="send" />
        </form>
</div>
<?php require_once "_footer.php"; ?>