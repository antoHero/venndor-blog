<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} is your Venndor admin sign-in code</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #ededed;">
                    <tr>
                        <td style="background-color:#006b5e;padding:24px 32px;">
                            <span style="color:#ffffff;font-size:20px;font-weight:700;">Venndor</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;color:#423a3a;font-size:16px;line-height:24px;">
                                Hi {{ $adminUser->name }},
                            </p>
                            <p style="margin:0 0 24px;color:#423a3a;font-size:16px;line-height:24px;">
                                Use this code to sign in to the Venndor admin dashboard. It expires in {{ (int) ceil($expiresInSeconds / 60) }} minutes.
                            </p>
                            <p style="margin:0 0 24px;text-align:center;">
                                <span style="display:inline-block;padding:16px 32px;border-radius:12px;background-color:#e0fffb;color:#006b5e;font-size:32px;font-weight:700;letter-spacing:8px;">
                                    {{ $code }}
                                </span>
                            </p>
                            <p style="margin:0;color:#7e7e7e;font-size:14px;line-height:20px;">
                                If you didn&rsquo;t request this code, you can safely ignore this email &mdash; no one can access your account without it.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background-color:#fcfcfd;border-top:1px solid #ededed;">
                            <p style="margin:0;color:#7e7e7e;font-size:12px;line-height:18px;">
                                &copy; {{ now()->year }} Venndor. This is an automated security message.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
