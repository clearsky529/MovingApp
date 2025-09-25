<!DOCTYPE html>
<html>
<head>
    <title>Kika - Move Notificatio</title>
</head>
<body>
	<div style="display: inline">
	  <div style="text-align: left;">
        <p> Please find attached the link to comments for {{ $move->contact->contact_name }} - {{ $move->move_number }}. </p> </br>
		<a href="{{url($link)}}"><h3>View PDF</h3></a>
	  </div>
	</div>
	<br/>
</body>
</html>
