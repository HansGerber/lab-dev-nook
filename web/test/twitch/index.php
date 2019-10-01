<?php $streamListLength = 200; ?>
<html>
	<head>
		<style>
			@keyframes loading {
				0% {
					opacity: 1;
				}
				100% {
					opacity: 0.2;
				}
			}

			body {
				font-family: arial;
				margin: 0;
				color: #555;
			}
			h3 {
				padding: 10px;
				color: #fff;
				background: #6441a4;
			}
			li {
				padding: 4px 8px;
			}
			.twitchColor {
				color: #6441a4;
			}
			li.loading {
				list-style: none;
				margin-left: -20px;
				text-shadow: ;
				-webkit-animation: 0.5s infinite alternate loading;
						animation: 0.5s infinite alternate loading;
			}
		</style>
	</head>
	<body>
		<h3>Top <?php echo $streamListLength; ?> streams currently live</h3>
		<ol id="streamList"></ol>
		<script>
			var streamList = document.getElementById('streamList');
			var streams = [];
			var streamListLength = <?php echo $streamListLength; ?>;

			var twitchRequest = function(endpointUrl, callback, headers) {
				var xhr = new XMLHttpRequest();
				var clientId = 'aoa8gk8qfq30nryuy07h5zsm8sooai';
				var apiBaseUrl = 'https://api.twitch.tv/helix/';

				xhr.open('GET', apiBaseUrl + endpointUrl);
				xhr.setRequestHeader('Client-ID', clientId);
				if(headers instanceof Array) {
					for(var i = 0; i < headers.length; i++) {
						xhr.setRequestHeader(headers[i].name, headers[i].value);
					}
				}

				xhr.onreadystatechange = (function() {
					if(this.xhr.readyState === 4) {
						this.callback(this.xhr.responseText, this.xhr.status);
					}
				}).bind({ xhr: xhr, callback: callback });
				
				xhr.send();
				return xhr;
			}
			
			var loadStreamListData = function(length, callback, nextCursor) {
				if(streams.length < streamListLength) {
					twitchRequest(
						nextCursor ? 'streams?after=' + nextCursor : 'streams',
						function(response, status) {
							if(status < 400) {
								var json = JSON.parse(response);
								Array.prototype.push.apply(streams, json.data);
								loadStreamListData(length, callback, json.pagination.cursor);
							} else {
								streams = [];
								alert("Error " + status + " while loading the list");
							}
						}
					);
				} else {
					callback instanceof Function && callback();
				}
			}

			var renderStreamList = function(list) {
				streamList.innerHTML = '';
				for(var i = 0; i < list.length; i++) {
					streamList.innerHTML += '<li> ' +
						'<b class="twitchColor">' + list[i].user_name + '</b> ' +
						list[i].title + ' ' +
						'<b class="twitchColor">(' + list[i].viewer_count + ')</b>' +
					'</li>';
				}
			}
			
			var updateStreamList = function() {
				streamList.innerHTML = '<li class="loading twitchColor">Loading streams ...</li>';
				loadStreamListData(streamListLength, function() {
					renderStreamList(streams);
				});
			}

			updateStreamList();
		</script>
	</body>
</html>
