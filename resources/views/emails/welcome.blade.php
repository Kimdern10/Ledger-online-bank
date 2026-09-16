<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Ledger</title>
</head>
<body style="margin:0;padding:0;background:#F1EFE7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
    <!-- Preview text shown in inbox lists, hidden in the email body itself -->
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Your Ledger account is open. Here's everything you need to get started.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F1EFE7;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

                    <!-- Brand bar -->
                    <tr>
                        <td style="background:#10202F;border-radius:16px 16px 0 0;padding:26px 40px;">
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

                    <!-- Content card -->
                    <tr>
                        <td style="background:#FFFFFF;border:1px solid #EDEAE0;border-top:none;border-radius:0 0 16px 16px;padding:44px 40px 36px;">

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom:22px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:56px;height:56px;background:#E4EEE9;border-radius:50%;text-align:center;vertical-align:middle;">
                                                    <span style="font-size:26px;line-height:56px;color:#2F6F62;">&#10003;</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin:0 0 12px;text-align:center;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#10202F;">
                                Welcome, {{ $user->first_name }}.
                            </h1>

                            <p style="margin:0 auto 30px;max-width:400px;text-align:center;font-size:15px;line-height:1.7;color:#5C6B72;">
                                Your Ledger account is open. Sign in any time to send money, check your balance, and manage your cards. Every dollar, accounted for.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F4EE;border-radius:12px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:18px 22px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#8C9298;">Signed up with</p>
                                        <p style="margin:0;font-size:15px;font-weight:600;color:#10202F;">{{ $user->email }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('login') }}" style="display:inline-block;background:#2F6F62;color:#F6F4EE;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:10px;">
                                            Open Ledger
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;border-top:1px solid #EDEAE0;">
                                <tr>
                                    <td style="padding-top:22px;text-align:center;">
                                        <p style="font-size:12.5px;color:#8C9298;margin:0;line-height:1.6;">
                                            Didn't create this account? Contact our support team right away through the app.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
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
