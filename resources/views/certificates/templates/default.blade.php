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
            border: 15px solid #0c3a2d; position: relative;
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        }
        .certificate::before {
            content: ''; position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 2px solid #6d9773;
        }
        .corner { position: absolute; width: 50px; height: 50px; border: 3px solid #0c3a2d; }
        .corner-tl { top: 18px; left: 18px; border-right: none; border-bottom: none; }
        .corner-tr { top: 18px; right: 18px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 18px; left: 18px; border-right: none; border-top: none; }
        .corner-br { bottom: 18px; right: 18px; border-left: none; border-top: none; }
        .header { text-align: center; margin-bottom: 25px; padding-top: 15px; }
        .logo { font-size: 20px; font-weight: bold; color: #0c3a2d; margin-bottom: 8px; letter-spacing: 2px; }
        .title { font-size: 42px; color: #0c3a2d; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 5px; }
        .subtitle { font-size: 16px; color: #64748b; letter-spacing: 2px; }
        .content { text-align: center; margin: 25px 0; }
        .presented-to { font-size: 14px; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 2px; }
        .recipient-name { font-size: 36px; color: #0c3a2d; font-style: italic; border-bottom: 2px solid #ffb902; display: inline-block; padding: 0 40px 10px; margin-bottom: 15px; }
        .description { font-size: 14px; color: #475569; max-width: 500px; margin: 0 auto; line-height: 1.6; }
        .course-name { font-size: 22px; color: #0c3a2d; font-weight: bold; margin: 15px 0; padding: 8px 25px; display: inline-block; border: 2px solid #6d9773; border-radius: 5px; background: #e8f5e9; }
        .footer { position: absolute; bottom: 50px; left: 60px; right: 60px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature-block { text-align: center; width: 160px; }
        .signature-line { border-top: 2px solid #0c3a2d; padding-top: 8px; font-size: 11px; color: #475569; font-weight: 600; }
        .center-block { text-align: center; }
        .seal { width: 80px; height: 80px; border: 3px solid #0c3a2d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #0c3a2d; text-align: center; font-weight: bold; margin: 0 auto 8px; background: #e8f5e9; }
        .date { font-size: 13px; color: #475569; }
        .certificate-number { font-size: 9px; color: #94a3b8; margin-top: 4px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        <div class="header">
            <div class="logo">{{ $config['organization'] ?? 'EPAS-E LEARNING MANAGEMENT SYSTEM' }}</div>
            <div class="title">Certificate</div>
            <div class="subtitle">OF COMPLETION</div>
        </div>
        <div class="content">
            <div class="presented-to">This is to certify that</div>
            <div class="recipient-name">{{ $user->full_name }}</div>
            <div class="description">has successfully completed all requirements and demonstrated competency in</div>
            <div class="course-name">{{ $course->course_name }}</div>
            <div class="description">as prescribed by the {{ $config['organization'] ?? 'EPAS-E Learning Management System' }} curriculum.</div>
        </div>
        <div class="footer">
            <div class="signature-block">
                <div class="signature-line">{{ $config['signatory_left_title'] ?? 'Administrator' }}</div>
            </div>
            <div class="center-block">
                <div class="seal">OFFICIAL<br>SEAL</div>
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
