<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Georgia', serif; background: #fff; }
        .certificate {
            width: 100%; height: 100%; padding: 40px;
            border: 15px solid #b45309; position: relative;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
        }
        .certificate::before {
            content: ''; position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 3px double #d97706;
        }
        .ornament { position: absolute; font-size: 30px; color: #b45309; }
        .ornament-tl { top: 20px; left: 25px; }
        .ornament-tr { top: 20px; right: 25px; }
        .ornament-bl { bottom: 20px; left: 25px; }
        .ornament-br { bottom: 20px; right: 25px; }
        .header { text-align: center; margin-bottom: 20px; padding-top: 20px; }
        .logo { font-size: 18px; font-weight: bold; color: #92400e; margin-bottom: 10px; letter-spacing: 3px; text-transform: uppercase; }
        .title { font-size: 48px; color: #92400e; font-weight: bold; font-family: 'Times New Roman', serif; letter-spacing: 5px; margin-bottom: 5px; text-shadow: 2px 2px 4px rgba(180, 83, 9, 0.2); }
        .subtitle { font-size: 18px; color: #a16207; letter-spacing: 8px; text-transform: uppercase; }
        .content { text-align: center; margin: 30px 0; }
        .presented-to { font-size: 14px; color: #a16207; margin-bottom: 10px; font-style: italic; }
        .recipient-name { font-size: 40px; color: #78350f; font-family: 'Brush Script MT', cursive; display: inline-block; padding: 0 30px 5px; margin-bottom: 15px; border-bottom: 2px solid #d97706; }
        .description { font-size: 14px; color: #78350f; max-width: 480px; margin: 0 auto; line-height: 1.7; }
        .course-name { font-size: 24px; color: #92400e; font-weight: bold; margin: 20px 0; padding: 12px 30px; display: inline-block; border: 2px solid #d97706; background: rgba(255,255,255,0.5); border-radius: 3px; }
        .footer { position: absolute; bottom: 45px; left: 60px; right: 60px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature-block { text-align: center; width: 170px; }
        .signature-line { border-top: 2px solid #b45309; padding-top: 8px; font-size: 11px; color: #78350f; font-weight: 600; }
        .center-block { text-align: center; }
        .seal { width: 85px; height: 85px; border: 4px solid #b45309; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #92400e; text-align: center; font-weight: bold; margin: 0 auto 10px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); box-shadow: 0 0 15px rgba(180, 83, 9, 0.3); }
        .date { font-size: 14px; color: #78350f; font-weight: 600; }
        .certificate-number { font-size: 9px; color: #a16207; margin-top: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="ornament ornament-tl">❧</div>
        <div class="ornament ornament-tr">❧</div>
        <div class="ornament ornament-bl">❧</div>
        <div class="ornament ornament-br">❧</div>
        <div class="header">
            <div class="logo">{{ $config['organization'] ?? 'EPAS-E Learning Management System' }}</div>
            <div class="title">Certificate</div>
            <div class="subtitle">of Excellence</div>
        </div>
        <div class="content">
            <div class="presented-to">This is to certify that</div>
            <div class="recipient-name">{{ $user->full_name }}</div>
            <div class="description">has demonstrated outstanding achievement and successfully completed all requirements in</div>
            <div class="course-name">{{ $course->course_name }}</div>
            <div class="description">with distinction and commitment to excellence.</div>
        </div>
        <div class="footer">
            <div class="signature-block">
                <div class="signature-line">{{ $config['signatory_left_title'] ?? 'Administrator' }}</div>
            </div>
            <div class="center-block">
                <div class="seal">★<br>EXCELLENCE<br>AWARD</div>
                <div class="date">{{ $issue_date }}</div>
                <div class="certificate-number">Certificate No: {{ $certificate_number }}</div>
            </div>
            <div class="signature-block">
                <div class="signature-line">{{ $config['signatory_right_title'] ?? 'Instructor' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
