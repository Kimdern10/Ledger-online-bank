{{-- Statement email — App\Mail\StatementMail. A short covering note, the way
     banks send statements: the statement itself is the attached PDF
     (resources/views/pdf/statement.blade.php). Same brand bar and colours as
     emails/transaction.blade.php. $statement is an App\Support\MonthlyStatement;
     $requested is true when the customer asked for it on History. --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('statement.email_heading') }}</title>
</head>
<body style="margin:0;padding:0;background:#F1EFE7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#10202F;">
    <?php $periodLabel = $statement->periodLabel(); ?>

    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        {{ __('statement.email_attached', ['period' => $periodLabel]) }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F1EFE7;padding:24px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

                    <!-- Brand bar -->
                    <tr>
                        <td style="background:#10202F;border-radius:16px 16px 0 0;padding:20px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:32px;height:32px;background:#C9A24B;border-radius:9px;text-align:center;vertical-align:middle;">
                                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:700;color:#10202F;line-height:32px;">L</span>
                                    </td>
                                    <td style="padding-left:10px;">
                                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:18px;font-weight:600;color:#F6F4EE;">Ledger</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Note -->
                    <tr>
                        <td style="background:#FFFFFF;border:1px solid #EDEAE0;border-top:none;border-radius:0 0 16px 16px;padding:34px 28px 36px;">
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#10202F;">{{ __('statement.email_hello', ['name' => $statement->user->name]) }}</p>

                            <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.25;font-weight:600;color:#10202F;">{{ __('statement.email_heading') }}</h1>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#10202F;">{{ __('statement.email_attached', ['period' => $periodLabel]) }}</p>

                            <!-- The attachment, called out -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="background:#F6F4EE;border-radius:12px;margin:0 0 22px;width:100%;">
                                <tr>
                                    <td style="padding:14px 16px;width:36px;vertical-align:middle;">
                                        <div style="width:34px;height:40px;background:#C1503C;border-radius:5px;color:#FFFFFF;font-size:10px;font-weight:700;text-align:center;line-height:40px;">PDF</div>
                                    </td>
                                    <td style="padding:14px 16px 14px 0;vertical-align:middle;">
                                        <p style="margin:0;font-size:13.5px;font-weight:600;color:#10202F;word-break:break-all;">{{ $statement->filename() }}</p>
                                        <p style="margin:2px 0 0;font-size:12px;color:#8C9298;">{{ $statement->from->translatedFormat('j M Y') }} &ndash; {{ $statement->to->translatedFormat('j M Y') }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5B6670;">
                                {{ $requested ? __('statement.email_requested_note') : __('statement.email_monthly_note') }}
                            </p>

                            <p style="margin:0;font-size:15px;line-height:1.7;color:#10202F;">
                                {{ __('statement.email_regards') }}<br>
                                {{ __('statement.email_team') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 16px 0;text-align:center;">
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
