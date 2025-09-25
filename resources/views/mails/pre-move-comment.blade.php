<!DOCTYPE html>
<html>
<head>
    <title>Kika - Pre Move Comments</title>
</head>
<body>
    <div style="display: inline">
        <div style="text-align: left;">
            {{-- <p>Hi, </p> --}}
            <p>Please find attached comments for 
                {{ $move->contact->contact_name . ' - ' . $move->move_number }}.</p>
            {{-- <p><a href="{{ url($pdf_link) }}">View PDF</a></p> --}}
            {!! isset($image_pdf_link) ? '<p><a href="' . url($image_pdf_link) . '">View Pre Move Comment Images PDF</a></p>' : '' !!}
            {{-- <p>Regards,</p> --}}
            {{-- <p>Team Kika..</p> --}}
        </div>
    </div>
</body>
</html>
