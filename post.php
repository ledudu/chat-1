<?php
	$con = mysql_connect("localhost","username","password");
	date_default_timezone_set('America/New_York');
	$u=$_SERVER['PHP_AUTH_USER'];
	$m=$_POST['m'];
	$a=$_POST['a'];
	if(!isset($a)&&!isset($m)){mysql_close($con);exit;} // IF BLANK REQUEST, EXIT
	
	if($a=='1'){ // UPDATING USERS ONLINE
		echo "<b>Users Online:</b> ";
		$result=mysql_query("SELECT * FROM `chat`.`online`;");
		while($row = mysql_fetch_array($result)){
			echo "'".$row['user']."' ";
		}
		mysql_close($con);exit;
	}
	if($a=='2'){ // UPDATING TOPIC
		$result=mysql_query("SELECT `topic` FROM `chat`.`misc`;");
		while($row = mysql_fetch_array($result)){
			echo "<b>Room Topic:</b> ".$row['topic'];
		}
		mysql_close($con);exit;
	}
	
	$result=mysql_query("SELECT * FROM `chat`.`online`;"); // CHECK IF USER IS LOGGED IN
	while($row = mysql_fetch_array($result)){
		if($row['user']==$u){$in=1;}
	}
	if($in!=1&&!isset($a)){mysql_close($con);exit;} // EXIT IF NOT ONLINE
	
	$result=mysql_query("SELECT * FROM `chat`.`users` WHERE user = '$u';"); // GET USER'S RIGHTS (3=ADMIN,2=MOD,1=REGULAR)
	while($row = mysql_fetch_array($result)){
		if($row['banned']=='1'||$row['muted']=='1'){mysql_close($con);exit;} // IF USER IS BANNED, EXIT
		$rights=$row['rights'];
		if($rights<3){$m=htmlspecialchars($m);} // RESTRICT HTML TO ONLY ADMINS
		$nick=$row['nick'];
	}
	
	if($a=='disconnect'){ // DISCONNECTING
		mysql_query("DELETE FROM `chat`.`online` WHERE user = '$u' LIMIT 1;");
		$msg="<span class='user$rights'>$nick</span> has left the chat";	
	}
	else if($a=='connect'){ // CONNECTING
		mysql_query("DELETE FROM `chat`.`online` WHERE user = '$u';");
		mysql_query("INSERT INTO `chat`.`online` (`user`) VALUES ('$u');");
		$msg="<span class='user$rights'>$nick</span> has joined the chat";	
	}
	elseif(substr($m, 0, strlen("/")) === "/"){ // SERVER COMMAND
		/* CLEAR COMMAND (mod or higher) */
		if($m=="/clear"&&$rights>1){
			mysql_query("TRUNCATE TABLE `chat`.`messages`");
			mysql_close($con);exit;
		}
		/* SERVER COMMAND (admin only) */
		elseif(substr($m, 0, strlen("/server")) === "/server"&&$rights>2){
			$msg=str_replace("/server ","","<span class='server'>".mysql_escape_string(stripslashes($m))."</span>");
		}
		/* NICK COMMAND (all users) */
		elseif(substr($m, 0, strlen("/nick")) === "/nick"){
			$newnick=str_replace("/nick ","",$m);
			if(trim($newnick)==""){$newnick=$u;} // IF NEW NICKNAME IS BLANK, DEFAULT TO USERNAME
			mysql_query("UPDATE `chat`.`users` SET nick = '".mysql_escape_string(stripslashes($newnick))."' WHERE user = '$u';");
			$msg="<span class='user$rights'>$nick</span> is now <span class='user$rights'>".mysql_escape_string(stripslashes($newnick))."</span>";
		}
		/* PIC COMMAND (all users) */
		elseif(substr($m, 0, strlen("/pic")) === "/pic"){
			$msg=str_replace("/pic ","","<span class='user$rights'>$nick</span>:<img height='75%' src='".mysql_escape_string(stripslashes($m))."'>");
		}
		 /* LINK COMMAND (all users) */
                elseif(substr($m, 0, strlen("/link")) === "/link"){
                        $msg=str_replace("/link ","","<span class='user$rights'>$nick</span>:<a target='_blank' href='".mysql_escape_string(stripslashes($m))."'>".mysql_escape_string(stripslashes($m))."</a>");
                }
                /* KICK COMMAND (admin only) */
                elseif(substr($m, 0, strlen("/kick")) === "/kick" && $rights>2){
                        $msg=str_replace("/kick ","","<span class='server'><b>".mysql_escape_string(stripslashes($m))."</b> was kicked by a mod</span>");
                     	mysql_query("DELETE FROM `chat`.`online` WHERE `user` = '".str_replace("/kick ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
                        if (mysql_affected_rows()==0) {mysql_close($con);exit;}
                }
		/* MUTE COMMAND (mod or higher) */
		elseif(substr($m, 0, strlen("/mute")) === "/mute" && $rights>1){
			$msg=str_replace("/mute ","","<span class='server'><b>".mysql_escape_string(stripslashes($m))."</b> was muted by a mod</span>");
			if(str_replace("/mute ","",mysql_escape_string(stripslashes($m)))==$u){exit;}
			mysql_query("UPDATE `chat`.`users` SET `muted` = '1' WHERE `user` = '".str_replace("/mute ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
			if (mysql_affected_rows()==0) {mysql_close($con);exit;}
		}
		/* UNMUTE COMMAND (mod or higher) */
                elseif(substr($m, 0, strlen("/unmute")) === "/unmute" && $rights>1){
                        $msg=str_replace("/unmute ","","<span class='server'><b>".mysql_escape_string(stripslashes($m))."</b> was unmuted by a mod</span>");
			if(str_replace("/mute ","",mysql_escape_string(stripslashes($m)))==$u){exit;}
                        mysql_query("UPDATE `chat`.`users` SET `muted` = '0' WHERE `user` = '".str_replace("/unmute ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
                        if (mysql_affected_rows()==0) {mysql_close($con);exit;}
                }
		/* BAN COMMAND (admin only) */
		elseif(substr($m, 0, strlen("/ban")) === "/ban" && $rights>2){
			$msg=str_replace("/ban ","","<span class='server'><b>".mysql_escape_string(stripslashes($m))."</b> was banned by a mod</span>");
			mysql_query("UPDATE `chat`.`users` SET `banned` = '1' WHERE `user` = '".str_replace("/ban ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
			if (mysql_affected_rows()==0) {mysql_close($con);exit;}
		}
		/* PARDON COMMAND (admin only) */
		elseif(substr($m, 0, strlen("/pardon")) === "/pardon" && $rights>2){
			$msg=str_replace("/pardon ","","<span class='server'><b>".mysql_escape_string(stripslashes($m))."</b> was pardoned by a mod</span>");
			mysql_query("UPDATE `chat`.`users` SET `banned` = '0' WHERE `user` = '".str_replace("/pardon ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
			if (mysql_affected_rows()==0) {mysql_close($con);exit;}
		}
		/* TOPIC COMMAND (admin only) */
		elseif(substr($m, 0, strlen("/topic")) === "/topic" && $rights>2){
			$msg=str_replace("/topic ","","<span class='server'>Topic set to <b>".mysql_escape_string(stripslashes(str_replace("/topic ","",$m)))."</b></span>");
			mysql_query("UPDATE `chat`.`misc` SET `topic` = '".str_replace("/topic ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
		}
                /* BG COMMAND (admin only) */
                elseif(substr($m, 0, strlen("/bg")) === "/bg" && $rights>2){
                        //$msg=str_replace("/bg ","","<span class='server'>Background set to <b>".mysql_escape_string(stripslashes(str_replace("/bg ","",$m)))."</b></span>");
                        mysql_query("UPDATE `chat`.`misc` SET `background` = '".str_replace("/bg ","",mysql_escape_string(stripslashes($m)))."' LIMIT 1;");
			exit;
                }
		else{
			mysql_close($con);exit; // EXIT IF SERVER COMMAND IS NOT RECOGNIZED
		}
	}else{
		$msg="<span class='user$rights'>$nick</span>: <span class='msg$rights'>$m</span>"; // SET STYLE (COLORS) BASED ON $rights
	}
	
	$msg="<span class='time'>[".date("H:i:s")."]</span> ".$msg; // ADD TIMESTAMP ONTO MESSAGE
	
	mysql_query("UPDATE `chat`.`messages` SET `msg` = '".mysql_escape_string(stripslashes($msg))."';"); // TRY TO UPDATE MESSAGES
	if (mysql_affected_rows()==0) { // IF THERE WAS PREVIOUSLY NO MESSAGE IN THE DB, INSERT ONE
		mysql_query("INSERT INTO `chat`.`messages` (`msg`) VALUES ('".mysql_escape_string(stripslashes($msg))."');");
	}
	mysql_close($con);
?>
