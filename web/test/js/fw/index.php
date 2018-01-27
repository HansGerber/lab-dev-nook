<style>
body {
	padding:0;
	margin:0;
}
#fwContainer {
	position:relative;
}
#pointer {
	position:absolute;
	left:195px;
	top:0;
	width:10px;
	height:40px;
	background:#310;
	z-index:1001;
}
#fw {
	z-index:1000;
}
#fwResult {
	display:none;
	position:absolute;
	z-index:1001;
	bottom: 20px;
	left:120px;
	right:120px;
	text-align:center;
	border:solid 1px #fff;
	box-shadow:0 0 10px rgba(0, 0, 0, 0.5);
	background:#310;
	padding:5px 7px;
	color:#fff;
}
</style>
<h1>Fortune Wheel!</h1>
<table cellpadding="20">
	<tr>
		<td>
			Just click the Screen!
		</td>
		<td>
			<div id="fwContainer">
				<div id="pointer"></div>
				<img id="fw" src="wheel.png">
				<div id="fwResult"></div>
			</div>
		</td>
	</tr>
</table>
<script>
var diffDeg = 45, pos = [], turnPos = 0, resultVisible = false, wheelSpinning = false,
	paid = 0, costPerSpin = 5, priceMoney = 0, totalSpins = 0;

pos[0] = {name:"fish", value:1};
pos[-45] = {name:"bottle", value:2};
pos[-90] = {name:"coins", value:10};
pos[-135] = {name:"camel", value:3};
pos[-180] = {name:"coins", value:10};
pos[-225] = {name:"toxic", value:0};
pos[-270] = {name:"man", value:5};
pos[-315] = {name:"pond", value:15};

function getReward(deg){
	var deg = Math.abs(parseInt(deg));
	deg = -1 * (Math.floor(deg / 45) * 45 - Math.floor(deg / 360) * 360);
	
	return pos[deg];
}
function showResult(msgResult){
	if(resultVisible == false){
		var fwResult = document.getElementById("fwResult");
		resultVisible = true;
		fwResult.style.display = "block";
		fwResult.innerHTML = msgResult;
		setTimeout(function() {
			fwResult.style.display = "none";
			resultVisible = false
			wheelSpinning = false;
		}, 2000);
	}
}

function spinWheel(){
	if(wheelSpinning == false){
		wheelSpinning = true;
		
		paid += costPerSpin;
		totalSpins ++;
		
		var randEnd = Math.round(Math.random() * 720),
			stepWidth = 1, currentSpinPos = 0,
			fw = document.getElementById("fw");
			
			
		while(randEnd < 90){
			randEnd = Math.round(Math.random() * 720);
		}
		
		var delay = Math.round(Math.random() * 200) + 200;
		stepWidth = Math.round(Math.random() * 20) / 10;
		while(stepWidth < 1){
			stepWidth = Math.round(Math.random() * 50) / 10;
		}
		console.log("\nSpin\n");
		var spinTmr = setInterval(
			function() {
				if(stepWidth <= 0){
					var objReward = getReward(turnPos);
					clearInterval(spinTmr);
					priceMoney += objReward.value;
					showResult(objReward.name + "<br>Price Money : " + priceMoney + "$ <br>Paid money : " + paid + "$<br>Spins : " + totalSpins);
					return true;
				}
				if(delay > 0){
					delay--;
				} else {
					stepWidth -= 0.01;
				}
				currentSpinPos += stepWidth;
				turnPos -= stepWidth;
				console.log(delay, stepWidth, currentSpinPos);
				fw.style.transform = "rotate(" + turnPos + "deg)";
			}, 1
		);
	}
}

addEventListener("click", function() {
	spinWheel();
}, false);
</script>