<!DOCTYPE html>
<html>
<head>
    <title>Kika - Move Notificatio</title>
</head>
<body>
	<div style="display: inline">
	  <div style="text-align: left;">
        <!-- <p> Please find attached the link to comments for {{ $move->contact->contact_name }} - {{ $move->move_number }}. </p> </br> -->
        <p>Hi,</p>
        <p> Here is the link to the Bingo Sheet for {{ $move->contact->contact_name }} - {{ $move->move_number }}. </p> </br>
		<a href="{{url($transloadlink)}}"><h3>View PDF</h3></a>
		<p>Thank you..</p>
	  </div>
	</div>
	<br/>
</body>
</html>
