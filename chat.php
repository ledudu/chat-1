<?php
$con = mysql_connect("localhost","username","password");
$user=$_SERVER['PHP_AUTH_USER'];
$result=mysql_query("SELECT * FROM `chat`.`users`;");
while($row = mysql_fetch_array($result)){
	if($row['user']==stripslashes($user) && $row['banned']=='1'){
		echo "<center><span class='server'>You have been banned from the chat.</span></center>";exit; // DISPLAY MESSAGE AND EXIT IF USER IS BANNED
	}
}
$result=mysql_query("SELECT `msg` FROM `chat`.`messages` LIMIT 1;"); // GET MOST RECENT MESSAGE
while($row = mysql_fetch_array($result)){
echo $row['msg']."<br>";
}
mysql_close($con);
?>
