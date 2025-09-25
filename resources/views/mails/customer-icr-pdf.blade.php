<!DOCTYPE html>
<html>
<head>
    <title>Kika - Uplift ICR</title>
</head>
<body>
	<div style="display: inline">
	  <div style="text-align: left;">
        <p>Hi, </p>
        <p>Please find attached the inventory and condition report for your move.</p>
        {!! isset($image_pdf_link) ? '<p><a href="' . url($image_pdf_link) . '">View Post Move Comment Images PDF</a></p>' : '' !!}
        {!! isset($condition_image_pdf_link) ? '<p><a href="' . url($condition_image_pdf_link) . '">'.$move_type.' ICR Images</a></p>' : '' !!}
        <p>IMPORTANT : This email is NOT monitored. For ALL questions regarding your move please contact the moving company directly through their email address.</p>
        <p>Thank you.</p>
        <p>Regards..</p>
      </div>
    </div>
</body>
</html>