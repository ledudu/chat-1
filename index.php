<?php
$user   = $_SERVER['PHP_AUTH_USER'];
$ver    = '2.0.5';
$con    = mysql_connect("localhost", "username", "password");
$result = mysql_query("SELECT * FROM `chat`.`users` WHERE user = '$user';"); // GET USER'S RIGHTS (3=ADMIN,2=MOD,1=REGULAR)
while ($row = mysql_fetch_array($result)) {
    $rights = $row['rights'];
    if ($row['banned'] == '1') {
        die("You are banned from the chat");
    } // IF USER IS BANNED, EXIT
}
?> 
<html>
    
    <head>
        <title>The Chat <?php echo $ver; ?> by nate</title>
        <style type='text/css'>
            body {
                color:#000;
                cursor:default;
                font-family:Arial;
                font-size:15px;
                word-wrap:break-word;
            }
            a {
                text-decoration:none;
                font-weight:400;
            }
            #container {
                opacity:0.99;
                background-color:#999;
                width:625px;
                height:500px;
                border-radius:17px;
                margin:0 auto;
                padding:17.5px;
            }
            #title {
                text-align:center;
                font-size:25px;
                font-weight:700;
                letter-spacing:-2.5px;
                text-shadow:0 1px #fff;
            }
            #msg {
                border:0 none;
                overflow:auto;
                padding-bottom:5px;
                padding-top:5px;
                width:90%;
            }
            #content {
                height:375px;
                overflow:auto;
            }
            #form {
                background:#CCC;
                margin-top:5px;
                padding:5px;
            }
            #container img {
                width:50%;
            }
            #online, #topic {
                background:#CCC;
                width:625px;
                opacity:0.99;
                border-radius:17px;
                margin:2.5px auto;
                padding:17.5px;
            }
            #side {
                float:right;
                width:250px;
                opacity:0.99;
                display:none;
                background:#CCC;
                padding:5px;
            }
            .u1 {
                color:blue;
                text-shadow:0 1px 0 #000;
                font-weight:700;
                font-size:larger;
            }
            .u2 {
                color:red;
                text-shadow:0 1px 0 #000;
                font-weight:700;
                font-size:larger;
            }
            .u3 {
                color:#FF69B4;
                text-shadow:0 1px 0 #000;
                font-weight:700;
                font-size:larger;
            }
        </style>
        <script src='/jquery.js'></script>
        <script type='text/javascript'>
            var old, oldTime;

            function send() { // FUNCTION TO SEND A MESSAGE
                var message = $("#msg").val();
                if (message.trim() != "") {
                    $("#msg").val("");
                    $.post("post.php", {
                        m: message
                    });
                    if (message == "/cmdlist") {
                        commandList();
                    }
                    if (message == "/rules") {
                        showRules();
                    }
                }
            }

            function update() { // FUNCTION TO UPDATE THE CHAT
                $.get("chat.php", function (result) {
                    if (result != old) {
                        old = result;
                        if (result != "") {
                            var o = $.parseJSON(result);
                            result = "[" + o.t + "] <span class='u" + o.r + "'>" + o.n + "</span> " + o.m;
                            $("#content").append(result + "<br>");
                            $("#content").prop({
                                scrollTop: $("#content").prop("scrollHeight")
                            });
							if(o.u==null) {
								o.u="None";
							}
                            $("#online").html("<b>Users Online</b>: " + o.u);
                            $("#topic").html("<b>Room Topic</b>: " + o.p);
                            $("body").css("background", "url('" + o.b + "')");
                        }
                    } else if (result == "") {
                        $("#content").html(""); // CLEAR #content
                    }
                });
            }

            function disconnect() { // FUNCTION TO DISCONNECT FROM THE CHAT
                $.post("post.php", {
                    a: 'disconnect'
                });
            }

            function commandList() { // FUNCTION TO SHOW COMMANDLIST
                $("#side").show();
                $("#side").html("<a href='#' onclick='$(\"#side\").fadeOut(\"normal\");' style='font-size:15px;color:red;float:right'>X</a><b>Command List:</b><br><u>Admins:</u><br>/ban [user]<br>/pardon [user]<br>/server [server message]<br>/topic [new topic]<br>/kick [user]<br>/bg [background image]<br><u>Mods:</u><br>/clear<br>/mute [user]<br>/unmute [user]<br><u>Everyone:</u><br>/nick [new nickname]<br>/pic [picture url]<br>/link [url]<br>/cmdlist<br>/rules<br>");
            }

            function showRules() { // FUNCTION TO SHOW RULES
                $("#side").show();
                $("#side").html("<a href='#' onclick='$(\"#side\").fadeOut(\"normal\");' style='font-size:15px;color:red;float:right'>X</a><b>Rules:</b><br>1. No idling for extended periods of time<br>2. No incessant spamming<br>3. No illegal content<br>4. No unfunny content<br>");
            }

            window.onbeforeunload = function () {
                disconnect();
                return null;
            }

            function connect() { // FUNCTION TO CONNECT TO THE CHAT
                $.post("post.php", {
                    a: 'connect'
                });
            }

            $(document).ready(function () {
                setInterval(function () { // UPDATE THE CHAT EVERY 500 ms
                    update();
                }, 500);
            });
        </script>
    </head>
    
    <body onload='$("#msg").focus();connect();'>
        <div id='side'></div>
        <div id='container'>
            <p id='title'>The Chat <?php echo $ver; ?> by nate</p>
            <div id='content'></div>
            <div id='form' align='center'>
                <form action='javascript:send();'>
                    <input id='msg' type='text'>
                </form>
            </div>
        </div>
        <div id='online' align='center'></div>
        <div id='topic' align='center'></div>
    </body>

</html>