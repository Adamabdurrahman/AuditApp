<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9fafb; color: #333; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background: #16a34a; color: white; padding: 12px 20px;">
            <h2 style="margin: 0;">Audit Application</h2>
        </div>
        <div style="padding: 20px;">
            <h3>{{ $title }}</h3>
            <p style="font-size: 14px; line-height: 1.6;">
                {!! nl2br(e($messageContent)) !!}
            </p>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

            <p style="font-size: 13px; color: #666;">
                <strong>Judul Temuan:</strong> {{ $auditForm->judul_temuan }} <br>
                <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($auditForm->due_date)->format('d M Y') }} <br>
                <strong>Status:</strong> {{ $auditForm->status->status ?? 'N/A' }}
            </p>

            <p style="font-size: 13px; color: #777; margin-top: 20px;">
                Terima kasih,<br>
                <strong>Audit Application System</strong>
            </p>
        </div>
    </div>
</body>
</html>
