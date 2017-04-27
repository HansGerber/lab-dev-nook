<style>
    body {
        padding:0;
        margin:0;
        font:100%/1.5 arial;
        color:#555;
    }
    table {
        border-collapse: collapse;
    }
    table tr th{
        background:#555;
        border: solid 1px #333;
        color:#fff;
        text-align: left;
        padding:10px;
    }
    table tr td{
        border: solid 1px #ddd;
        padding:10px;
    }
</style>
<h3>Uploads</h3>
<a href="<?php echo makePath("upload-test"); ?>">Upload a file</a>
<?php
    if($uploads["success"] == true){
        $uploadCount = count(@$uploads["data"]);
        if($uploadCount > 0){
            $bgcolor = "eee";
            ?>
            <h4>Number of uploads : <?php echo $uploadCount; ?></h4>
            <table>
                <tr><th>Name</th><th>Preview</th><th>fileid</th><th>Uploaddate</th></tr>
            <?php
            foreach($uploads["data"] as $upload){
                if($bgcolor == "eee"){$bgcolor = "fff";}else{$bgcolor = "eee";}
                echo '<tr style="background:#'.$bgcolor.';">'
                        . '<td>'.$upload["filename"].'</td>'
                        . '<td>'
                        . '   <a href="'.makePath("uploads/".$upload["fileid"]).'" tagret="_blank">'
                        . '      <img width="100" alt="'.makePath("uploads/".$upload["fileid"]).'" src="'.makePath("uploads/".$upload["fileid"]).'" />'
                        . '   </a>'
                        . '</td>'
                        . '<td>'.$upload["fileid"].'</td><td>'.$upload["adddate"].'</td>'
                    . '</tr>';
            }
            ?></table><?php
        } else {
            ?>
<p>Nothing has been uploaded so far.</p>
            <?php
        }
    } else {
        ?>
<p>Uploads couldn't be listed due to an unknown reason ...</p>
        <?php
    }
?>