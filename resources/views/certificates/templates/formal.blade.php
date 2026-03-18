<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; background: #fff; }
        .certificate {
            width: 100%; height: 100%; padding: 35px; position: relative;
            background: #fffef7;
            border: 2px solid #1a365d;
        }
        .inner-border {
            position: absolute; top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 1px solid #2c5282;
        }
        .inner-border-2 {
            position: absolute; top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 3px double #1a365d;
        }
        .header { text-align: center; padding-top: 30px; margin-bottom: 20px; }
        .republic { font-size: 12px; color: #4a5568; letter-spacing: 2px; margin-bottom: 5px; }
        .dept { font-size: 14px; color: #2d3748; font-weight: bold; letter-spacing: 1px; margin-bottom: 3px; }
        .school { font-size: 20px; color: #1a365d; font-weight: bold; margin-bottom: 15px; }
        .title { font-size: 36px; color: #1a365d; font-weight: bold; text-transform: uppercase; letter-spacing: 5px; border-bottom: 2px solid #2c5282; border-top: 2px solid #2c5282; padding: 10px 0; margin: 0 100px; }
        .content { text-align: center; margin: 25px 0; }
        .presented-to { font-size: 14px; color: #4a5568; margin-bottom: 10px; font-style: italic; }
        .recipient-name { font-size: 32px; color: #1a365d; font-weight: bold; font-family: 'Brush Script MT', cursive; margin-bottom: 15px; }
        .description { font-size: 13px; color: #2d3748; max-width: 450px; margin: 0 auto 10px; line-height: 1.7; text-align: justify; text-align-last: center; }
        .course-name { font-size: 18px; color: #1a365d; font-weight: bold; margin: 15px 0; text-transform: uppercase; letter-spacing: 1px; }
        .given-text { font-size: 12px; color: #4a5568; margin-top: 20px; }
        .footer { position: absolute; bottom: 40px; left: 50px; right: 50px; }
        .signatures { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .signature-block { text-align: center; width: 180px; }
        .signature-name { font-size: 14px; color: #1a365d; font-weight: bold; border-top: 1px solid #1a365d; padding-top: 5px; }
        .signature-title { font-size: 10px; color: #4a5568; }
        .center-seal { text-align: center; }
        .seal-circle { width: 70px; height: 70px; border: 2px solid #1a365d; border-radius: 50%; margin: 0 auto 5px; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #1a365d; text-align: center; font-weight: bold; line-height: 1.2; }
        .meta-row { display: flex; justify-content: space-between; font-size: 10px; color: #718096; margin-top: 15px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border"></div>
        <div class="inner-border-2"></div>
        <div class="header">
            <div class="republic">Republic of the Philippines</div>
            <div class="dept">Department of Education</div>
            <div class="school">{{ $config['organization'] ?? 'EPAS-E Learning Management System' }}</div>
            <div class="title">Certificate of Completion</div>
        </div>
        <div class="content">
            <div class="presented-to">This is to certify that</div>
            <div class="recipient-name">{{ $user->full_name }}</div>
            <div class="description">
                has satisfactorily completed the prescribed course requirements and has demonstrated the necessary competencies in
            </div>
            <div class="course-name">{{ $course->course_name }}</div>
            <div class="description">
                as offered by this institution in accordance with the standards set by the Department of Education.
            </div>
            <div class="given-text">Given this {{ $issue_date }} at {{ $config['location'] ?? 'Philippines' }}.</div>
        </div>
        <div class="footer">
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-name">{{ $config['signatory_left_name'] ?? '_______________' }}</div>
                    <div class="signature-title">{{ $config['signatory_left_title'] ?? 'School Administrator' }}</div>
                </div>
                <div class="center-seal">
                    <div class="seal-circle">OFFICIAL<br>SCHOOL<br>SEAL</div>
                </div>
                <div class="signature-block">
                    <div class="signature-name">{{ $config['signatory_right_name'] ?? '_______________' }}</div>
                    <div class="signature-title">{{ $config['signatory_right_title'] ?? 'Course Instructor' }}</div>
                </div>
            </div>
            <div class="meta-row">
                <span>Certificate No: {{ $certificate_number }}</span>
                <span>Verify at: {{ config('app.url') }}/verify</span>
            </div>
        </div>
    </div>
</body>
</html>
