<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion - TESDA NCII</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Times New Roman', 'Georgia', serif;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            padding: 8mm 14mm;
            background: white;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Outer border - navy blue */
        .certificate::before {
            content: '';
            position: absolute;
            top: 4mm;
            left: 4mm;
            right: 4mm;
            bottom: 4mm;
            border: 3px solid #003366;
            pointer-events: none;
        }

        /* Inner border - gold */
        .certificate::after {
            content: '';
            position: absolute;
            top: 7mm;
            left: 7mm;
            right: 7mm;
            bottom: 7mm;
            border: 1.5px solid #c8a951;
            pointer-events: none;
        }

        /* Corner accents */
        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: #c8a951;
            border-style: solid;
        }
        .corner-tl { top: 9mm; left: 9mm; border-width: 3px 0 0 3px; }
        .corner-tr { top: 9mm; right: 9mm; border-width: 3px 3px 0 0; }
        .corner-bl { bottom: 9mm; left: 9mm; border-width: 0 0 3px 3px; }
        .corner-br { bottom: 9mm; right: 9mm; border-width: 0 3px 3px 0; }

        /* Header section */
        .header {
            text-align: center;
            padding-top: 6mm;
        }

        .republic {
            font-size: 11px;
            letter-spacing: 2px;
            color: #003366;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .tesda-name {
            font-size: 14px;
            font-weight: 700;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 2px;
        }

        .tesda-sub {
            font-size: 9px;
            color: #555;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .cert-title {
            font-size: 28px;
            font-weight: 700;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin-bottom: 2px;
        }

        .cert-type {
            font-size: 16px;
            font-weight: 600;
            color: #c8a951;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .divider {
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #c8a951, transparent);
            margin: 8px auto;
        }

        /* Body content */
        .body {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            padding: 0 30mm;
        }

        .preamble {
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .recipient-name {
            font-size: 30px;
            font-weight: 700;
            color: #003366;
            border-bottom: 2px solid #c8a951;
            display: inline-block;
            padding: 0 30px 4px;
            margin: 4px auto;
            letter-spacing: 1px;
        }

        .qualification-label {
            font-size: 11px;
            color: #555;
            margin-top: 6px;
        }

        .qualification-name {
            font-size: 18px;
            font-weight: 700;
            color: #003366;
            background: #f0e8d4;
            display: inline-block;
            padding: 6px 30px;
            margin: 4px auto;
            border: 1px solid #c8a951;
            letter-spacing: 1px;
        }

        .nc-level {
            font-size: 14px;
            font-weight: 700;
            color: #c8a951;
            letter-spacing: 3px;
            margin-top: 2px;
        }

        .institution-text {
            font-size: 11px;
            color: #444;
            margin-top: 4px;
            line-height: 1.5;
        }

        .institution-name {
            font-weight: 700;
            color: #003366;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 12mm;
            padding-bottom: 4mm;
        }

        .signature-block {
            text-align: center;
            width: 160px;
        }

        .sign-line {
            width: 100%;
            border-top: 1.5px solid #003366;
            margin-bottom: 4px;
        }

        .sign-title {
            font-size: 9px;
            color: #003366;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sign-name {
            font-size: 10px;
            color: #333;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .center-block {
            text-align: center;
        }

        .seal {
            width: 60px;
            height: 60px;
            border: 2.5px solid #003366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 4px;
            background: #f8f4eb;
        }

        .seal-inner {
            width: 48px;
            height: 48px;
            border: 1px solid #c8a951;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .seal-text {
            font-size: 6px;
            color: #003366;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            text-align: center;
        }

        .issue-date {
            font-size: 10px;
            color: #003366;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .cert-number {
            font-size: 8px;
            color: #666;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
        }

        .training-hours {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="header">
            <div class="republic">Republic of the Philippines</div>
            <div class="tesda-name">Technical Education and Skills Development Authority</div>
            <div class="tesda-sub">Providing Direction, Policies, Programs and Standards towards Quality Technical Education and Skills Development</div>
            <div class="divider"></div>
            <div class="cert-title">Certificate of Completion</div>
            <div class="cert-type">National Certificate II</div>
        </div>

        <div class="body">
            <div class="preamble">This is to certify that</div>
            <div class="recipient-name">{{ $user->full_name }}</div>
            <div class="qualification-label">has successfully completed the requirements for the qualification of</div>
            <div class="qualification-name">{{ $course->course_name }}</div>
            <div class="nc-level">NC II</div>
            <div class="institution-text">
                as prescribed by the Technical Education and Skills Development Authority<br>
                Training Institution: <span class="institution-name">{{ $config['institution'] ?? 'IETI College of Technology - Marikina' }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <div class="sign-line"></div>
                <div class="sign-title">{{ $config['signatory_left_title'] ?? 'School Administrator' }}</div>
            </div>

            <div class="center-block">
                <div class="seal">
                    <div class="seal-inner">
                        <div class="seal-text">TESDA<br>OFFICIAL<br>SEAL</div>
                    </div>
                </div>
                <div class="issue-date">{{ $issue_date }}</div>
                <div class="cert-number">{{ $certificate_number }}</div>
            </div>

            <div class="signature-block">
                <div class="sign-line"></div>
                <div class="sign-title">{{ $config['signatory_right_title'] ?? 'Lead Instructor / Trainer' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
