<?php
    $image = @$_FILES["imageUpload"];
    $testImage = @$_GET["testImage"];
    $output = "";
    //if($image){
        $testImageFolder = "images/";
	$testImageName = "test1.jpg";
        if($testImage){
            $testImageName = $testImage;
        }
        $imagePath = $testImageFolder.$testImageName;
        $tesseractOutputFolder = "output/";
        $tesseractOutputFile = "test1";
        $tesseractOutputPath = $tesseractOutputFolder.$tesseractOutputFile;
        $tesseractCLIOutput = array();
        $tesseractCLIOutputStatus = 0;
	
        $tesseractCommand = "tesseract $imagePath $tesseractOutputPath";
        
        @exec($tesseractCommand, $tesseractCLIOutput, $tesseractCLIOutputStatus);
        
        ob_start();
        var_dump($tesseractCLIOutput, $tesseractCLIOutputStatus);
        $output = "<ul>
            <li>Bild : <a href=\"$imagePath\">show</a></li>
            <li>Ergebnis : <a href=\"$tesseractOutputPath\">show</a></li>
        </ul>";
        $output .= "INFO : " . ob_get_clean();
    //}
?>
<html>
    <head>
        <title>OCR Scanner</title>
    </head>
    <body>
        <div>
            <?php
                echo $output;
            ?>
        </div>
        <footer>
            Powered by <a href="https://github.com/tesseract-ocr/tesseract" target="_blank">Tesseract</a>
        </footer>
    </body>
</html>