<?php
/*
 * Copyright 2009 Trevor Oldak
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.

 * boxfilereader.php 
 * By Trevor Oldak
 * trevdakatgmaildotcom
 * 
 * Takes an image that was used for training in tesseract, and
 * the resulting box file, and shows the location of each box
 * on the image.
 * 
 * Requires: 
 * PHP
 * Firefox (for the javascript to work right. All JS is untested in other browsers)
 * ImageMagick (for converting tif to jpg)
 * GD for image creation/manipulation
 */

//The location for the original image to be saved to, converted to jpeg
$img_name = "image.jpg";
//The temp location of the tif image. Gets deleted when script executes
$tif_name = "image.tif";
//The location of the box image that is output
$box_png = "boxes.png";

if(isset($_POST['readboxfile'])){
	if(!empty($_FILES['image']['name'])){
		//Convert the image to jpg, then delete the tif
		move_uploaded_file($_FILES['image']['tmp_name'],$tif_name);
		exec("convert $tif_name $img_name");
		unlink($tif_name);
	}
	
	$img_size = getimagesize($img_name);
	$img_width = $img_size[0];
	$img_height = $img_size[1];

	$img = imagecreatetruecolor($img_width,$img_height);
	$black = imagecolorallocate($img, 0, 0, 0);
	imagecolortransparent($img, $black);
	//Red, green, and blue so that boxes are more easily distinguished from one another
	$colors = array(imagecolorallocate($img, 255, 0, 0), imagecolorallocate($img, 0, 255, 0), imagecolorallocate($img, 0, 0, 255));
	
	$boxes = file_get_contents($_FILES['box']['tmp_name']);
	$boxes = explode("\n", $boxes);
	$minwidth = (int)$_POST['minwidth'];
	$maxwidth = (int)$_POST['maxwidth'];
	$minheight = (int)$_POST['minheight'];
	$maxheight = (int)$_POST['maxheight'];
	$cleanedboxes = "";
	foreach($boxes as $index => $line){
		$row = explode(" ", $line);
		if((count($row) == 5) // Valid row
		  && ($row[0] != "~") // Tilde is the fudge character, if it's not sure what's in the box
		  && ($minwidth <= ($row[3]-$row[1]))
		  && ($maxwidth >= ($row[3]-$row[1]))
		  && ($minheight <= ($row[4]-$row[2]))
		  && ($maxheight >= ($row[4]-$row[2]))){
			$cleanedboxes .= "$line\n";
			imagerectangle($img, $row[1], ($img_height-$row[2]), $row[3], ($img_height-$row[4]), $colors[$index%3]);
		}
	}
	
	//Save the image as a png
	imagepng($img, $box_png);
	imagedestroy($img)
?>
<html>
	<head>
	<style>
	body{
		padding:0px;
		margin:0px;
	}
	
	#frame{
			width:<?php echo $img_width ?>px;
			height:<?php echo $img_height ?>px;
			background-image: url(<?php echo $img_name ?>);
			cursor: crosshair;
	}
	</style>
	<script>
		
		//saves the coordinates  of the beginning of the drag of a 
		//bounding box.
		//Lazy hack, only works from the corner of the screen, not
		//where the image was actually clicked.
		//Not tested in IE
		var lastx, lasty;
		function getCoords(e){
			e.preventDefault();
			lastx = e.pageX;
			lasty = <?php echo $img_height ?>-e.pageY;
		}

		//Prints the coordinates of the dragged box over the image.
		//Lazy hack, only works from the corner of the screen, not
		//where the image was actually clicked.
		//Not tested in IE
		function displayCoords(e){
			e.preventDefault();
			var a, b, c, d;
			//Fix the order of the coordinates so it's lower-left corner first
			a = Math.min(lastx, e.pageX);
			b = Math.min(lasty, (<?php echo $img_height ?>-e.pageY));
			c = Math.max(lastx, e.pageX);
			d = Math.max(lasty, (<?php echo $img_height ?>-e.pageY));
			//Don't alert on a single pixel click, because it's accidental more than anything else
			if((a != c) || (b != d)) alert(a + " " + b + " " + c + " " + d);;
		}
	</script>
	</head>
	<body>
		<div id="frame">
			<img src="<?php echo $box_png ?>" onMouseDown="javascript:getCoords(event);" onMouseUp="javascript:displayCoords(event);"/>
		</div>
		<br/>
		<textarea rows="10" cols="25"><?php echo $cleanedboxes ?></textarea>
		
	</body>
</html>
<?php 
}else{
?>
<html>
	<body>
		<form method="POST" enctype="multipart/form-data" action="#">
			Image file: <input type="file" name="image"/><br/>
			Box file: <input type="file" name="box"/><br/>
			Min width: <input type="text" name="minwidth" value="0"/><br/>
			Max width: <input type="text" name="maxwidth" value="1000"/><br/>
			Min height: <input type="text" name="minheight" value="0"/><br/>
			Max height: <input type="text" name="maxheight" value="1000"/><br/>
			<input type="hidden" name="readboxfile" value="true"/>
			<input type="submit" value="Go!"/>
		</form>
	</body>
</html>
<?php
}
?>