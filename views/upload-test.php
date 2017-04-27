    <style>
        * {
                box-sizing:border-box;
                font:100%/1.5 arial;
        }
        #progress,
        #estimated,
        #inProgress,
        #success,
        #error {
                display:none;	
        }
        #progress_wrap {
                width:500px;
                background:#ddd;
        }
        #progress {
                background:#555;
                height:50px;
                position:relative;
        }
        #progress div {
                padding:5px;
                top:10px;
                left:10px;
                color:#55;
                background:#fff;
                border:solid 1px #555;
                position:absolute;
                font-size:150%;
        }
        #estimated,
        #inProgress,
        #success,
        #error {
                color:#fff;
                padding:10px;
                width:500px;
        }
        #estimated {
                background:#999;
        }
        #inProgress {
                background:#05a;
        }
        #success {
                background:#5a0;
        }
        #error {
                background:#a00;
        }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <form id="uploadForm" method="post" enctype="multipart/form-data">
            <input type="file" name="uploadFile" id="uploadFile" />
            <input type="submit" value="upload" id="sendUploadForm" />
            <div id="uploadFileDetails">
                <div id="uploadFileDetailsSize"></div>
            </div>
    </form>

    <div id="progress_wrap">
            <div id="progress"><div></div></div>
    </div>
    <div id="estimated">
            <span class="dataLeft"></span> Bytes uploaded.<br>
            Approximately <span class="seconds"></span> seconds left.
    </div>
    <div id="inProgress">
            Upload in progress ...
    </div>
    <div id="success">
            Upload successfull :) !
    </div>
    <div id="error">
            Upload failed :( !<br>
            Please try again a bit later.
    </div>
    <p>
        <a href="<?php echo makePath("upload-list-test"); ?>">Show list of uploads</a>
    </p>
    <script type="text/javascript">

            function objDump(obj) {
                    var out = "";
                    for(p in obj){
                            out += p + " : " + obj[p] + "; ";
                    }
                    return out;
            }

            var currentlyLoaded = 0;
            var estimateTimerRunning = false;
            var firstEstimationDone = false;
            var fadeSpeed = 100;
            $("#uploadFile").change(function() {
                if(this.files[0]){
                    var size = this.files[0].size;
                    var unit = "B";
                    if(size >= 1024 * 1024 * 1024){
                        size = size / 1024 / 1024 / 1024;
                        unit = "GB";
                    } else if(size >= 1024 * 1024){
                        size = size / 1024 / 1024;
                        unit = "MB";
                    } else if(size >= 1024){
                        size = size / 1024;
                        unit = "KB";
                    }
                    $("#uploadFileDetailsSize").html('Size : ' + (Math.round(size * 100) / 100) + unit);
                }
            });
            $("#sendUploadForm").click(function(e) {
                    var formData = new FormData($("#uploadForm")[0]);
                    e.preventDefault();
                    $.ajax({
                        xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                $("#inProgress").fadeIn(fadeSpeed);
                                $("#progress").stop().fadeIn(fadeSpeed);
                                $("#estimated").stop().fadeIn(fadeSpeed);
                                xhr.upload.addEventListener("progress", function(evt) {
                                        var completedPercentage = Math.round(evt.loaded / evt.total * 100);
                                        $("#progress").width(completedPercentage * 5);
                                        $("#progress div").html(completedPercentage + "%");
                                        $("#estimated .dataLeft").html(evt.loaded + " / " + evt.total);
                                        currentlyLoaded = evt.loaded;
                                        if(estimateTimerRunning == false){
                                                estimateTimerRunning = true;
                                                setTimeout(function() {
                                                        estimateTimerRunning = false;
                                                        var secondsRemaining = (evt.total - evt.loaded) / (currentlyLoaded - evt.loaded);
                                                        if(secondsRemaining !== Infinity){
                                                                $("#estimated .seconds").html(Math.round((evt.total - evt.loaded) / (currentlyLoaded - evt.loaded) / 2));
                                                        }
                                                        firstEstimationDone = true;
                                                }, 500);
                                        }
                                }, false);
                                return xhr;
                        },
                        method: 'POST',
                        url: '',
                        data: formData,
                        // THIS MUST BE DONE FOR FILE UPLOADING
                        cache: false,
                        contentType: false,
                        processData: false,
                        // ... Other options like success and etc
                        success: function(responseData){
                                $("#uploadForm")[0].reset();
                                $("#uploadFileDetailsSize").html("");
                                $("#progress, #estimated").stop().fadeOut(fadeSpeed);
                                $("#inProgress").stop().fadeOut(fadeSpeed, function() {
                                        if(responseData === "success"){
                                                $("#success").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                        } else {
                                                $("#error").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                        }
                                });
                        },
                        error: function() {
                                $("#uploadForm")[0].reset();
                                $("#uploadFileDetailsSize").html("");
                                $("#progress, #estimated").stop().fadeOut(fadeSpeed);
                                $("#inProgress").stop().fadeOut(fadeSpeed, function() {
                                        $("#error").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                });
                        }
                });
                return false;
        });

    </script>