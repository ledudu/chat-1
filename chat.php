<?php
$con    = mysql_connect("localhost", "username", "password");
$user   = $_SERVER['PHP_AUTH_USER'];
$now    = $_REQUEST['n'];
$old    = $_REQUEST['o'];
$result = mysql_query("SELECT * FROM `chat`.`users`;");
while ($row = mysql_fetch_array($result)) {
    if ($row['user'] == stripslashes($user) && $row['banned'] == '1') {
        echo "<center><span class='server'>You have been banned from the chat.</span></center>";
        exit; // DISPLAY MESSAGE AND EXIT IF USER IS BANNED
    }
}
$result = mysql_query("SELECT `topic` FROM `chat`.`misc`;");
while ($row = mysql_fetch_array($result)) {
    $topic = $row['topic'];
}
$result = mysql_query("SELECT `background` FROM `chat`.`misc`;");
while ($row = mysql_fetch_array($result)) {
    $bg = $row['background'];
}
$result = mysql_query("SELECT * FROM `chat`.`online`;");
while ($row = mysql_fetch_array($result)) {
    $users .= "\"" . $row['user'] . "\" ";
}
$result  = mysql_query("SELECT * FROM `chat`.`messages` LIMIT 1;"); // GET MOST RECENT MESSAGE
$message = array();
while ($row = mysql_fetch_array($result)) {
    $time         = time();
    $message['t'] = $row['time']; // current time
    $message['r'] = $row['rights']; // user's rights
    $message['n'] = $row['nick']; // user's nickname
    $message['u'] = $users; // user list
    $message['p'] = $topic; // topic 
    $message['m'] = $row['msg']; // message
    $message['b'] = $bg; // background
    $JSON_message = json_encode($message);
    echo $JSON_message;
    exit;
    mysql_close($con);
}
?>
