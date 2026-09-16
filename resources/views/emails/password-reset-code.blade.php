<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your password reset code</title>
</head>
<body style="margin:0;padding:0;background:#F1EFE7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Your Ledger password reset code: <?= e($code) ?>. It expires in 10 minutes.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F1EFE7;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;">

                    <tr>
                        <td style="background:#10202F;border-radius:16px 16px 0 0;padding:26px 36px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:34px;height:34px;background:linear-gradient(135deg,#3d8c7c,#2F6F62);border-radius:9px;text-align:center;vertical-align:middle;">
                                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:17px;font-weight:700;color:#F6F4EE;line-height:34px;">L</span>
                                    </td>
                                    <td style="padding-left:12px;">
                                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:19px;font-weight:600;color:#F6F4EE;letter-spacing:-0.01em;">Ledger</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#FFFFFF;border:1px solid #EDEAE0;border-top:none;border-radius:0 0 16px 16px;padding:44px 36px 36px;text-align:center;">

                            <h1 style="margin:0 0 10px;font-family:Georgia,'Times New Roman',serif;font-size:23px;font-weight:600;color:#10202F;">
                                Reset your password
                            </h1>
                            <p style="margin:0 auto 28px;max-width:340px;font-size:14.5px;line-height:1.7;color:#5C6B72;">
                                Enter this code on the reset page to choose a new password.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="background:#E4EEE9;border:1.5px dashed #2F6F62;border-radius:12px;padding:22px 0;">
                                        <span style="font-family:'Courier New',Courier,monospace;font-size:36px;font-weight:700;letter-spacing:10px;color:#1f4d43;"><?= e($code) ?></span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0;font-size:13px;color:#8C9298;">
                                &#9201; Expires in <strong style="color:#5C6B72;">10 minutes</strong>
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;background:#F5E4DF;border-radius:10px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0;font-size:12.5px;line-height:1.6;color:#8a4130;">
                                            Didn't request a password reset? You can safely ignore this email. Your password won't change unless this code is used.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 20px 0;text-align:center;">
                            <p style="margin:0;font-size:11.5px;color:#8C9298;line-height:1.7;">
                                Ledger &middot; Banking, kept in order<br>
                                This is an automated message. Please don't reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
