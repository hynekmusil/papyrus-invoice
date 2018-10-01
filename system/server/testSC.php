<?php //encoding=utf-8
	//header("Content-type: text/plain");
	require_once 'StateMachine.php';
	require_once 'state_microwave.php';
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Test StateMachine</title>
		<link href="style.css" rel="stylesheet" type="text/css" />
   </head>
   <body>
		<h1>Test StateMachine</h1>
		<h2>Inicializační proměnné</h2>
		<pre>
			<?php 
				StateMachine::$val['door_closed'] = false;
				print_r(StateMachine::$val);
			?>
		</pre>
		<h2>Sled událostí</h2>
		<pre>
			<?php
				$eventList = array('turn_on','door_close','time','time','door_open','time','door_close','time','time','time','turn_off');
				print_r($eventList);
			?>
		</pre>
		<h2>Debug</h2>
		<table>
			<tr>
				<th>stávající stav</th>
				<th>událost</th>
				<th>přechod na stav:</th>
				<th>proměnné</th>
			</tr>
			<?php
				foreach($eventList as $event){
					echo "<tr><td>{$sc->currentState}</td><td>$event</td>";
					$sc->process($event);
					echo "<td>{$sc->currentState}</td><td><pre>";
					print_r(StateMachine::$val);
					echo "</pre></td>";
				}
			?>
		</table>
   </body>
</html>