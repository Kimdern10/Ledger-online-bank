<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#F1EFE7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
    @php
        $isCredit = $amountSign === '+';
        $accent = $isCredit ? '#2F6F62' : '#C1503C';
        $accentTint = $isCredit ? '#E4EEE9' : '#F5E4DF';
        $accentTintText = $isCredit ? '#1f4d43' : '#8a4130';
        $arrow = $isCredit ? '&#8595;' : '&#8593;'; // money in = arrow down into account, out = up
    @endphp

    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        {{ $title }}: {{ $amountSign }}${{ number_format($amount, 2) }} · Ref {{ $reference }}
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

                    <!-- Accent strip signaling money in/out at a glance -->
                    <tr>
                        <td style="background:{{ $accent }};height:4px;line-height:4px;font-size:0;">&nbsp;</td>
                    </tr>

                    <!-- Content card -->
                    <tr>
                        <td style="background:#FFFFFF;border:1px solid #EDEAE0;border-top:none;border-radius:0 0 16px 16px;padding:40px 40px 8px;">

                            <p style="margin:0 0 6px;text-align:center;font-size:13.5px;color:#8C9298;">
                                Hi {{ $user->first_name }}, here's a copy of your receipt.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:6px 0 4px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:48px;height:48px;background:{{ $accentTint }};border-radius:50%;text-align:center;vertical-align:middle;">
                                                    <span style="font-size:20px;line-height:48px;color:{{ $accent }};">{!! $arrow !!}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin:14px 0 6px;text-align:center;font-family:Georgia,'Times New Roman',serif;font-size:21px;font-weight:600;color:#10202F;">
                                {{ $title }}
                            </h1>

                            <p style="margin:0 0 6px;text-align:center;font-size:34px;font-weight:700;color:{{ $accent }};">
                                {{ $amountSign }}${{ number_format($amount, 2) }}
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
                                <tr>
                                    <td style="background:{{ $accentTint }};color:{{ $accentTintText }};font-size:11.5px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;border-radius:20px;padding:5px 14px;">
                                        &#10003; Completed
                                    </td>
                                </tr>
                            </table>

                            <!-- Full transaction detail table: every field the controller passed
                                 in $rows (who/where, fees, date, etc.), plus reference — nothing
                                 about this transaction is left out of the email. -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #EDEAE0;margin-bottom:24px;">
                                @foreach ($rows as $row)
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid #EDEAE0;font-size:13.5px;color:#8C9298;">{{ $row['label'] }}</td>
                                        <td style="padding:12px 0;border-bottom:1px solid #EDEAE0;font-size:13.5px;color:#10202F;font-weight:600;text-align:right;">{{ $row['value'] }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:12px 0;font-size:13.5px;color:#8C9298;">Reference</td>
                                    <td style="padding:12px 0;font-size:13.5px;color:#10202F;font-weight:600;text-align:right;font-family:'Courier New',Courier,monospace;">{{ $reference }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F4EE;border-radius:12px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:18px 22px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#8C9298;">Balance after this transaction</p>
                                        <p style="margin:0;font-size:19px;font-weight:700;color:#10202F;">${{ number_format($newBalance, 2) }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #EDEAE0;">
                                <tr>
                                    <td style="padding-top:20px;padding-bottom:32px;text-align:center;">
                                        <p style="font-size:12.5px;color:#8C9298;margin:0;line-height:1.7;">
                                            Don't recognize this? Contact our support team right away through the app.<br>
                                            You're getting this because transaction email alerts are on in Settings.
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
