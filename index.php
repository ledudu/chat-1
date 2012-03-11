<?php
$user=$_SERVER['PHP_AUTH_USER'];
$ver='2.0.2';
$con = mysql_connect("localhost","root","toor");
$result=mysql_query("SELECT * FROM `chat`.`users` WHERE user = '$user';"); // GET USER'S RIGHTS (3=ADMIN,2=MOD,1=REGULAR)
	while($row = mysql_fetch_array($result)){
		$rights=$row['rights'];
		if($row['banned']=='1'){die("You are banned from the chat");}
	}
$result=mysql_query("SELECT * FROM `chat`.`misc`;"); // GET BACKGROUND IMAGE
        while($row = mysql_fetch_array($result)){
                $background=$row['background'];
        }
?>
<html>
<head>
<title>The Chat <?php echo $ver; ?> by nate</title>
<style type='text/css'>
body{
	color:black;
	cursor:default;
	font-family:Arial;
	font-size:15px;
	background: url("<?php echo $background; ?>");
}
a{
	text-decoration:none;
	font-weight:normal;
}
#container{
	opacity:0.99;
	margin: 0 auto;
	background-color:#999;
	padding:17.5px;
	width:625px;
	height:500px;
	border-radius:17px;
}
.title{
	text-align:center;
	font-size:25px;
	font-weight:bold;
	letter-spacing:-2.5px;
	text-shadow:0 1px  #fff;
}
#msg{
    border: 0 none;
    overflow: auto;
    padding-bottom: 5px;
    padding-top: 5px;
    width: 90%;
}
#content{
	height:375px;
	overflow:auto;
}
#form{
	background:#CCC;
	padding:5px;
	margin-top:5px;
}
#container img{
	width:50%;
}
.time{
	font-size:11px;	
}
.user1{
	color:blue;
	text-shadow:0 1px 0 #000;

	font-weight:bold;
	font-size:larger;
}
.user2{
	color:red;
	text-shadow:0 1px 0 #000;
	
	font-weight:bold;
	font-size:larger;
}
.user3{
	color:hotpink;
	text-shadow:0 1px 0 #000;
	
	font-weight:bold;
	font-size:larger;
}
.msg3{
	color:orange;	
	text-shadow:0 1px 0 #000;
}
.server{
	color:#000;
	text-shadow:0 1px 0 #000;
	font-size:14px;
	font-family:"Lucida Console", Monaco, monospace;
}
#online, #topic{
	background:#CCC;
	padding:17.5px;
	width:625px;
	margin:2.5px auto;
	opacity:0.99;
	border-radius:17px;
}
#side{
	float:right;
	width:250px;
	opacity:0.99;
	display:none;
	background:#CCC;
	padding:5px;
}
</style>
<script src='/jquery.js'></script>
<script type='text/javascript'>

var old;

function send(){ // FUNCTION TO SEND A MESSAGE
	var message=$("#msg").val();
	if(message.trim()!=""){
	$("#msg").val("");
	$.post("post.php",{m:message});
	if(message=="/commandlist"){
		commandList();
	}
	if(message=="/rules"){
        	showRules();         
        }
	}
}

function update(){ // FUNCTION TO UPDATE THE CHAT
	$.get("chat.php?t="+new Date().getTime(), function(result){
	if (result!=old) {
		old=result;
		$("#content").append(result);
		$("#content").prop({scrollTop:$("#content").prop("scrollHeight")});
		updateUsers();
		updateTopic();
		//notify();
	}
	else if(result=="") {
		$("#content").html("");
	}
	});
}

function updateUsers(){ // FUNCTION TO UPDATE THE USER LIST
	$.post("post.php", { a: '1' }, function(data) {
		$("#online").html(data);
	});
}

function updateTopic(){ // FUNCTION TO UPDATE THE ROOM TOPIC
	$.post("post.php", { a: '2' }, function(data) {
		$("#topic").html(data);
	});
}

function disconnect(){ // FUNCTION TO DISCONNECT FROM THE CHAT
	$.post("post.php",{a:'disconnect'});
}

function commandList(){
$("#side").show();
$("#side").html("<a href='#' onclick='$(\"#side\").fadeOut(\"normal\");' style='font-size:15px;color:red;float:right'>X</a><b>Command List:</b><br><u>Admins:</u><br>/ban [user]<br>/pardon [user]<br>/server [server message]<br>/topic [new topic]<br>/kick [user]<br>/bg [background image]<br><u>Mods:</u><br>/clear<br>/mute [user]<br>/unmute [user]<br><u>Everyone:</u><br>/nick [new nickname]<br>/pic [picture url]<br>/link [url]<br>/commandlist<br>/rules<br>");
}

function showRules(){
$("#side").show();
$("#side").html("<a href='#' onclick='$(\"#side\").fadeOut(\"normal\");' style='font-size:15px;color:red;float:right'>X</a><b>Rules:</b><br>1. No idling for extended periods of time<br>2. No incessant spamming<br>3. No illegal content<br>4. No unfunny content<br>");
}

var focused = true;
var New=false;
window.onblur = function() { focused = false; }
window.onfocus = function() { focused = true; }
window.onbeforeunload = function() { disconnect(); return null; }

/*function notify(){
var old=document.title;
timer=window.setInterval(function() {
	if(!focused){
	document.title=document.title == old ? "New Message!" : old;
	}else{
	document.title=old;
	clearInterval(timer);
	}
  }, 1000);
}
*/
function connect(){ // FUNCTION TO CONNECT TO THE CHAT
	$.post("post.php",{a:'connect'});
}

$(document).ready(function () {
    setInterval(function () { // UPDATE THE CHAT EVERY 500 ms
		update();
    }, 500);
	/*setInterval(function () { // UPDATE USERS & TOPIC EVERY 60 SECONDS
		updateUsers();
		updateTopic();
    }, 60000);*/
});

</script>
</head>
<body onload='$("#msg").focus();connect();'>
<div id='side'></div>
<div id='container'>
<p class='title'>The Chat <?php echo $ver; ?> by nate</p>
<div id='content'>
</div>
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
