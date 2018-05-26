var flashMessageVisible = false;

//
function $_GET(val){
	var params = location.search.substring(1).split("&"), param;
	
	for(var i = 0; i < params.length; i++){
		param = params[i].split("=");
		
		if(param[0] == val){
			return param[1];
		}
	}
	
	return null;
}

//
function getMemes(options){
	var ajaxUrl = "loadMemes.php?";
	var ajax = new XMLHttpRequest();
	
	if(typeof options != "undfined"){
		
		if(typeof options.tag != "undefined")
			ajaxUrl += "tag=" + options.tag;
		
		if(typeof options.after == "undefined")
			options.after = function() {};
		
	} else {
		
		var options = {
			after: function() {},
			tag: ""
		}
	}

	ajax.open("GET", ajaxUrl);
	ajax.onreadystatechange = function() {
		
		if(ajax.readyState === XMLHttpRequest.DONE && ajax.status == 200){
		    if(JSON.parse(ajax.responseText) !== null){
			options.after(JSON.parse(ajax.responseText).data);
                    } else {
                        alert("An error occured while loading the meme data");
                    }
		}
	}
	ajax.send();
}

// function requires vanilla-lazyload
function buildMemesResult(jsonMemeData, callback){
	var memes = document.getElementById("memes");
	
	memes.innerHTML = "";
	
	for(var memeIndex = 0; memeIndex < jsonMemeData.length; memeIndex++){
                let meme = document.createElement("div"), tags = jsonMemeData[memeIndex].tags.split(/\s*,\s*/),
                    tagsHTML = "";
            
                tagsHTML = '<div class="memeTags"><span>Tags: </span>';
                for(var tagIndex = 0; tagIndex < tags.length; tagIndex++){
                    tagsHTML += '<a href="index.php?tag=' + tags[tagIndex] + '">' +
                        tags[tagIndex] +
                        '</a>&nbsp;';
                }
                tagsHTML += '</div>';
            
		meme.className = "meme";
		meme.innerHTML = '<div><img class="lazyImage" data-original="' + jsonMemeData[memeIndex].animatedUrl + '" ></div>' + 
                        '<div>#' + (memeIndex + 1) + '</div>' +
			'<div class="memeAnimatedUrl"><span>Url: </span><input onfocus="this.select();" onclick="this.select();" class="txtMemeAnimatedUrl" type="text" value="' + jsonMemeData[memeIndex].animatedUrl + '"></div>' +
                        tagsHTML;
		memes.appendChild(meme);
	}
	if(typeof callback == "function"){
		callback = callback.bind(memes);
		callback({
			memeCount: jsonMemeData.length
		});
	}
}

// function requires vanilla-lazyload
function loadMemes(tag){
	if(tag != ""){
		document.getElementById("initialText").style.display = "none";
		document.getElementById("tagSearchText").value = tag;
		document.getElementById("memeLoader").style.display = "block";
		document.getElementById("memes").style.display = "none";
		
		getMemes({
			
			tag: (typeof tag != "undefined" ? tag : ""),
			after: function(data) {
				
				buildMemesResult(data, function(info) {
					
					var LazyLoader = new LazyLoad({
						elements_selector: ".lazyImage",
						callback_load: function(elem) {
							elem.addEventListener("click", function(e) {
								this.parentNode.parentNode.querySelector(".txtMemeAnimatedUrl").select();
								document.execCommand("copy");
								showFlashMessage("URL copied");
							}, false);
						}
					});
					
					LazyLoader._boundHandleScroll();
					
					document.getElementById("memesInfo").innerHTML = info.memeCount + " results for tag &quot;" + tag + "&quot;";
					document.getElementById("memes").style.display = "block";
					document.getElementById("memeLoader").style.display = "none";
				});
			}
		});
	}
}

function showFlashMessage(msg){
	var fm = document.getElementById("flashMessage");
	fm.innerHTML = msg;
	fm.style.display = "block";
	setTimeout(function() {
		fm.innerHTML = "";
		fm.style.display = "none";
	}, 2000);
}

function initMemeSearch(){
	addEventListener(
		"load",
		function() {
			
			loadMemes(($_GET("tag") != null ? $_GET("tag") : ""));
		}, 
		false
	);
	
	document.getElementById("tagSearchText").addEventListener("keydown", function(e){
		if(e.keyCode == 13){
			loadMemes(this.value);
		}
	}, false);
	
	document.getElementById("tagSearchText").focus();
}
