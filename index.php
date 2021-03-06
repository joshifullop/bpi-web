<?php

$cdata = array();
$data = array("Time","HLT","Mash","Kettle","Other");
array_push($cdata, $data);
if (($handle = fopen("/home/pi/barleypi/DisplaySession", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$data[0] = $utime = date("H:i", $data[0]);
	$data[1] = $temp1 = floatval($data[1]);
        $data[2] = $temp2 = floatval($data[2]);
	$data[3] = $temp3 = floatval($data[3]);
        $data[4] = $temp4 = floatval($data[4]);

	array_push($cdata, $data);
    }

    fclose($handle);


}


?>

<!DOCTYPE html>
<html>
<head><title>Barley Pi</title>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

	var data;
	var options;
	var chart;

	var tempHLT;
	var tempMash;


      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        window.data = google.visualization.arrayToDataTable(<?php echo json_encode($cdata); ?>);

        window.options = {
          title: 'Test Batch #1',
	  vAxis: {
		title: 'degrees C'
	  }
        };

        chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(window.data, window.options);
      }

	
	// Set up functions and formatting

	//SimpleDateFormat sdfHM = new SimpleDateFormat("H:m");



	function myFunction()
	{
		document.getElementById("sensor4").innerHTML="100.1 C";
		//alert("test");
		document.getElementById("phytase_sb").value = 10;
		updateEnzymes();

		var ulast = new Date();
		
		alert(ulast.getHours() + ":" + (ulast.getMinutes()-40));



	}


	function updateEnzymes()
	{
		//temp2 = Float.parseFloat(document.getElementById("sensor2").innerHTML)
		document.getElementById("phytase_sb").value = tempMash.toString();
		document.getElementById("ttHLT").value = "abc"; //window.tempMash.toString();

	}

	// Define SSE feeds
	var source = new EventSource("fakesse.php");

	source.addEventListener('livetemp', function(event){
	        //console.log("Live Temperature Message Received by listener function:" + event.data);
	        //document.getElementById("ticker").innerHTML += event.data;
		var obj = JSON.parse(event.data);
		document.getElementById("sensor1").innerHTML = obj.t1;
                document.getElementById("sensor2").innerHTML = obj.t2;
                document.getElementById("sensor3").innerHTML = obj.t3;
                document.getElementById("sensor4").innerHTML = obj.t4;
		tempHLT = Number(obj.t1);
		window.tempMash = Number(obj.t2);
		tempKettle = Number(obj.t3);
		tempOther = Number(obj.t4);

		var ulast = new Date(obj.utime * 1000);
		updateEnzymes();
		//document.getElementById("lastupdate").innerHTML = ulast.getHours() + ':' + ulast.getMinutes() + ':' + ulast.getSeconds();
		document.getElementById("lastupdate").innerHTML = ulast.toLocaleTimeString();
//console.log(" obj type = " + typeof(obj));

		var a = [];
		//a[0] = ulast.getHours() + ":" + String.format("%02d", ulast.getMinutes());
		a[0] = ulast.getHours() + ":" + ulast.getMinutes();
		//a[0] = String.format("%tH:%tM", ulast);
		a[1] = parseFloat(obj.t1);
                a[2] = parseFloat(obj.t2);
                a[3] = parseFloat(obj.t3);
                a[4] = parseFloat(obj.t4);

//console.log("a=" + a);
//console.log("a[1] type =" + typeof(a[1]));
		window.data.addRow(a);
		chart.draw(window.data, window.options);
		delete a;
		delete obj;
		delete ulast;
	}, false);




    </script>
</head>

<body style="font-family:Trebuchet MS;" >

<div id="container" style="width:100%">

<div id="titlebar" style="height:57px;background-color:#00D700">
<h1 style="margin-bottom:0;">Barley Pi - Brew Session Temperature Logger</h1></div>

<div id="menu" style="background-color:#FFD700;height:100%;width:100px;float:left;">
<b>Menu</b><br>
Main<br>
Config<br>
Other<br>
<button onclick="myFunction()">Test</button><br>
<br>
<br>
Last Update:<br>
<div id="lastupdate" style="background-color:#AAAAAA;">
n/a
</div>
</div>


<div id="content" style="background-color:#EEEEEE;float:left;">

<div id="sensors" style=height:250px;width=100%;background-color:#666666">
<div id="enzymes" style="width:200px;;height:100%;background-color:#9999FF;float:left;">
<fieldset>
<legend>Enzyme Activity</legend>

<table style="font-size: 60%">  
<colgroup id="enzymeColgroup" span="1"></colgroup>
<tr><td>Phytase</td><td><progress id='phytase_sb' label='label1' value="22" max="100"></progress></td><td></td></tr>
<tr><td>b-Glucanase</td><td><progress value="22" max="100"></progress></td><td></td></tr>
<tr><td>Pepsidase</td><td><progress value="22" max="50" width=60px></progress></td><td></td></tr>
<tr><td>Protease</td><td><progress value="22" max="100"></progress></td><td></td></tr>
<tr><td>b-amylase</td><td><progress value="22" max="100"></progress></td><td></td></tr>
<tr><td>a-amylase</td><td><progress value="22" max="100"></progress></td><td></td></tr>

</table>
</fieldset>
</div>

<div id="sensor1frame" style="width:200px;height:100%;background-color:#FF0000;float:left;">
<fieldset>
<legend>HLT</legend>
<h1 id="sensor1" align='center'><?php printf("%.1f", $temp1); ?> C</h1>
<table style="font-size: 60%">
<tr><td><b>Target Temp</b></td><td><center><input type="text" name="ttHLT" style='width:3em'></center></td><tr>
<tr><td>Rate of Change</td><td name="rateHLT"><center>- deg/sec</center></td></tr>
<tr><td>Time to Target</td><td name="etaHLT"><center>- min</center></td></tr>
<tr><td><input type="checkbox" name="alertChkHLT">Alarm @</td><td><input type="text" name="alertValHLT" style='width:2em'><select name="alertTypeHLT"><option value="deg">Deg</option><option value="min">Min</option></select></td></tr>
</table>
</fieldset>
</div>

<div id="sensor2frame" style="width:200px;height:100%;background-color:#FF6666;float:left;">
<fieldset>
<legend>Mash</legend>
<h1 id="sensor2" align='center'><?php printf("%.1f", $temp2); ?> C</h1><br>
</fieldset>
</div>

<div id="sensor3frame" style="width:200px;height:100%;background-color:#FF9999;float:left;">
<fieldset>
<legend>Kettle</legend>
<h1 id="sensor3" align='center'><?php printf("%.1f", $temp3); ?> C</h1><br>
</fieldset>
</div>

<div id="sensor4frame" style="width:200px;height:100%;background-color:#FFAAAA;float:left;">
<fieldset>
<legend>Other</legend>
<h1 id="sensor4" align='center'><?php printf("%.1f", $temp4); ?> C</h1><br>
</fieldset>
</div>
</div>

<div id="chart_div" style=height:250px;width=100%;background-color:#999999;">
Graph
</div>


</div>

<div id="footer" style="background-color:#FFA500;float:bottom;clear:both;text-align:center;">
Copyright &copyJoshi Fullop, 2014. All Rights Reserved. No portion of this work may be reproduced without express written consent.</div>

</div>


</body>
</html>
