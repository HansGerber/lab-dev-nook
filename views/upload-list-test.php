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
                        . '   <a href="'.makePath("uploads/".$upload["fileid"]).'_" tagret="_blank">'
                        . '      <img width="100" alt="'.makePath("uploads/".$upload["fileid"]).'" id="'.$upload["fileid"].'" data-src="'.makePath("uploads/".$upload["fileid"]).'_" />'
                        . '   </a>'
                        . '</td>'
                        . '<td>'.$upload["fileid"].'</td><td>'.$upload["adddate"].'</td>'
                    . '</tr>';
            }
            ?></table>
            <script>
                function byId(id){
                    return document.getElementById(id);
                }
                var rows = document.getElementsByTagName("tr");
                var img = null;
                for(var i = 1; i < rows.length; i++){
                    img = rows[i].getElementsByTagName("td")[1].getElementsByTagName("a")[0].getElementsByTagName("img")[0];
                    if(typeof img.getAttribute("data-src") != "undefined" && img.getAttribute("data-src") != null){
                        img.addEventListener("error", function() {
                            this.parentNode.removeChild(this);
                        }, false);
                        img.src = img.getAttribute("data-src");
                    }
                }
            </script><?php
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