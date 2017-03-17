<?php

$limit = 60;
$time = time();

if ( ! isset($_POST['get_messages']) && ! isset($_POST['id'])){
	exit;
}

$last_id = abs((int)$_POST['id']);

// в настройках php.ini должно быть настроено "max_execution_time" (по умолчанию 45сек)
set_time_limit($limit + 2);

// mysql_connect('localhost', 'user', 'password');
mysql_select_db('chat');


// если первый раз
if ($last_id == 0){
	$res = mysql_query('SELECT MAX(id) AS id FROM messages');

	if (mysql_num_rows($res)) {
        $row = mysql_fetch_array($res);
 		$last_id = $row['id'];
    }
}


// следим за новыми msg
while (time() - $time < $limit) {
    
    $res = mysql_query('SELECT * FROM messages WHERE  id > "'. $last_id .'" ORDER BY id ASC LIMIT 1');
    
    if (mysql_num_rows($res)) {
        
        while ($row = mysql_fetch_array($res)) {

            echo json_encode($row);
        }

        flush();
        exit;
    }

    sleep(2);
}
mysql_close();

echo json_encode(array('timeout'=> true));