<!DOCTYPE html>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>

    <meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
    <meta content='width=device-width, initial-scale=1.0' name='viewport'>
    <title>Kika</title>
    
</head>
<body style='background: #d5d5d5; font-family:Helvetica Neue, Helvetica, Arial;'>
<table align='center' bgcolor='#d5d5d5' border='0' cellpadding='0' cellspacing='0' id='backgroundTable' style='background: #d5d5d5;' width='100%'>
    <tr>
        <td align='center'>
            <center>
                <table border='0' cellpadding='35' cellspacing='0' style='margin-left: auto;margin-right: auto;width:600px;text-align:center;' width='520'>
                    <tr>
                        <td style="color: #000;text-decoration: none;font-size: 50px;"><b>kika</b> activation</td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
    <tr>
        <td align='center'>
            <center>
                <table border='0' cellpadding='30' cellspacing='0' style='margin-left: auto;margin-right: auto;width:520px;text-align:center;' width='520'>
                    <tr>
                        <td align='left' style='background: #ffffff; border: 1px solid #dce1e5;border-radius: 6px;' valign='top' width=''>
                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <td align='center' valign='top'>
                                        <h2 style="font-size: 20px;margin-top: 0;">You’re almost there !</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center' valign='top'>
                                        <p style='margin: 0 0 20px 0;font-size: 18px;'>
                                            Thank you for joining Kika. Your account number is {{ $kika_id }}. Please click the button below to activate <br> your account.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <a href="{{ url('company-admin/send-mail/'.Crypt::encrypt($id)) }}" style="color: #ffffff !important; text-decoration: none;font-size: 18px;"><h3 style="border-radius: 6px;background-color: #00a1fe;margin: 0;padding: 15px;">Activate your account</h3></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
    <tr>
        <td align="center">
            <center>
                <table width='520'>
                    <tr>
                        <td align='center'>
                            <p style="margin: 5px 0 0 0;font-size: 12px;">Button not working ? Click <a href="mailto:info@kikamoving.com" style="color:#00a1fe;">here</a> to contact Support.</p>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
</body>
</html>
