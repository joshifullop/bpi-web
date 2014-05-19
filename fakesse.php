<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data
//header('connection': 'keep-alive');


$t1 = 100;
$t2 = 50;
$t3 = 75;
$t4 = 25;

while (1){

	printf("event: timetest\ndata: The unix timestamp is: %s<br>\n\n", time());
	printf("event: livetemp\ndata:{\"utime\": \"%s\", \"t1\": \"%.1f\", \"t2\": \"%.1f\", \"t3\": \"%.1f\", \"t4\": \"%.1f\"}\n\n", time(), $t1, $t2, $t3, $t4); 
	$t1+=.1;
	$t2+=1;
	$t3-=1;
	$t4+=0.01;

	flush();
	ob_flush();
	sleep(5);
}



?>
