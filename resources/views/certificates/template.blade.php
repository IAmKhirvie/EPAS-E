<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', serif;
            background: #fff;
        }
        .certificate {
            width: 100%;
            height: 100%;
            padding: 40px;
            border: 15px solid #1e40af;
            position: relative;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #3b82f6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .title {
            font-size: 42px;
            color: #1e40af;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 30px;
        }
        .content {
            text-align: center;
            margin: 40px 0;
        }
        .presented-to {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 10px;
        }
        .recipient-name {
            font-size: 36px;
            color: #1e3a5f;
            font-style: italic;
            border-bottom: 2px solid #3b82f6;
            display: inline-block;
            padding: 0 40px 10px;
            margin-bottom: 20px;
        }
        .description {
            font-size: 16px;
            color: #475569;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .course-name {
            font-size: 24px;
            color: #1e40af;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            position: absolute;
            bottom: 60px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-block {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #1e40af;
            padding-top: 5px;
            font-size: 12px;
            color: #64748b;
        }
        .date-block {
            text-align: center;
        }
        .date {
            font-size: 14px;
            color: #475569;
        }
        .certificate-number {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 5px;
        }
        .seal {
            width: 80px;
            height: 80px;
            border: 3px solid #1e40af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #1e40af;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="logo">EPAS-E Learning Management System</div>
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">Electronic Products Assembly and Servicing</div>
        </div>

        <div class="content">
            <div class="presented-to">This is to certify that</div>
            <div class="recipient-name">{{ $user->full_name }}</div>
            <div class="description">
                has successfully completed all the requirements and demonstrated competency in
            </div>
            <div class="course-name">{{ $course->course_name }}</div>
            <div class="description">
                as prescribed by the EPAS-E Learning Management System curriculum.
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <div class="signature-line">Administrator</div>
            </div>

            <div class="date-block">
                <div class="seal">
                    OFFICIAL<br>SEAL
                </div>
                <div class="date">{{ $issue_date }}</div>
                <div class="certificate-number">Certificate No: {{ $certificate_number }}</div>
            </div>

            <div class="signature-block">
                <div class="signature-line">Instructor</div>
            </div>
        </div>
    </div>
</body>
</html>
