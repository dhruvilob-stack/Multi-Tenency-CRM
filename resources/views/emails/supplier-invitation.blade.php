<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Supplier Invitation</title>
    </head>
    <body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
            <tr>
                <td align="center">
                    <table role="presentation" width="560" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:16px;padding:32px;">
                        <tr>
                            <td>
                                <h1 style="margin:0 0 12px;font-size:22px;color:#0f172a;">You're invited to join {{ $organizationName }}</h1>
                                <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#475569;">
                                    Set your password to activate your supplier account. Your email will be locked for security.
                                </p>
                                <a href="{{ $inviteUrl }}" style="display:inline-block;background:#10b981;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:600;">
                                    Activate Account
                                </a>
                                <p style="margin:20px 0 0;font-size:12px;color:#94a3b8;">
                                    If you did not expect this invitation, you can ignore this email.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
