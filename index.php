<?php
if (isset($_POST['send_message'])){

	$msg = isset($_POST['msg']) ? trim($_POST['msg']): '';
	$name = isset($_POST['name']) ? trim($_POST['name']): '';

	// ?? проверка
	if ( ! $msg){
		exit;
	}

	function escape($str) {
	    return str_replace('"', '\"', $str);
	}

	mysql_connect('localhost', 'user', 'password');
	mysql_select_db('chat');
	mysql_query('
		INSERT INTO messages 
			SET 
				name = "'. escape($name) .'", 
				text = "'. escape($msg) .'", 
				`date` = "'. date('h:i', time()) .'"
	');
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Chat</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	
	<h1 align="center">Chat</h1>
	
	<div class="chat">
		<div id="display" class="display"></div>
		<div class="bord">
			<input id="msg" class="input-msg" type="text" name="msg" value="" placeholder="Введите текст...">
		</div>
	</div>



<script>
;(function($,w,d){

	var name = prompt('Введите Ваше имя') || 'User',
		lastId = 0,
		timeout = 120 * 1000; // тут 2мин. а на сервере выставлено $limit = 60сек


	var display = $('#display');
	
	var input = $('#msg').on('keydown', function(e){
		if (e.keyCode != 13) return;

		var msg = $.trim(this.value.replace(/\s+/g, ' '));
		
		// очищаем поле
		this.value = '';
		
		if ( ! msg ) return;
		
		$.post('',{
			send_message:'',
			name:name,
			msg:msg
		});

	}).trigger('focus');



	// эта херня следит за новыми сообщениями
	function trace(){
		self.comet = $.ajax({
            type: "post",
            url:  "server.php",
            data: {'get_messages':'', 'id': lastId},
            dataType: "json",
            timeout: timeout,
            success: function(data){

            	// если на сервере вышло время
            	if (data.timeout){
            		return setTimeout(trace, 1000);
            	}

            	// ответ
            	var msg = $('<div class="msg">'+
					'<div class="name">'+ (data.name == name ? 'Я' : data.name) +': <div class="time">'+ data.date +'</div></div>'+
					'<div class="message">'+ data.text +'</div>'+
				'</div>').appendTo(display);

				// прокручиваем чат
				display.animate({scrollTop:display[0].scrollHeight}, 500);

            	// запоминаем последний ID
            	lastId = data.id;

				// повторно запускаем запрос
                setTimeout(trace, 1000);
            },
            error: function(a){
                // повторно запускаем запрос
                setTimeout(trace, 1000);
           }
        });
	}
	// начинаем следить
    trace();

})(jQuery, window, document);
</script>
</body>
</html>