<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 30px; }
        .header { text-align: center; border-bottom: 4px solid #800000; padding-bottom: 10px; margin-bottom: 30px; }
        .univ-name { font-size: 20px; font-weight: bold; color: #800000; margin: 0; text-transform: uppercase; }
        .office-name { font-size: 14px; font-weight: bold; margin: 5px 0; }
        .address { font-size: 10px; color: #666; }
        
        .report-title { text-align: center; margin-top: 40px; font-size: 18px; font-weight: bold; text-decoration: underline; }
        .date { text-align: center; font-size: 11px; margin-bottom: 40px; color: #777; }
        
        .stat-card { background: #fdf2f2; padding: 20px; border-left: 10px solid #800000; margin-bottom: 15px; }
        .stat-label { font-size: 11px; font-weight: bold; color: #800000; text-transform: uppercase; }
        .stat-value { font-size: 24px; font-weight: bold; display: block; margin-top: 5px; }
        
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</div>
        <div class="office-name">OFFICE OF THE VICE PRESIDENT FOR STUDENT AFFAIRS AND SERVICES</div>
        <div class="address">Anonas St., Sta. Mesa, Manila, Philippines 1016</div>
    </div>

    <div class="report-title">{{ $title }}</div>
    <div class="date">Report generated on {{ $date }}</div>

    <div class="stat-card">
        <span class="stat-label">Total Document Count</span>
        <span class="stat-value">{{ $totalFiles }}</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Monthly Submission Rate</span>
        <span class="stat-value">{{ $filesThisMonth }} New Uploads</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Departments Monitored</span>
        <span class="stat-value">{{ $activeOffices }} Active Units</span>
    </div>

    <div class="footer">
        Student Affairs Services and Information System (SASIS) - Internal Administrative Report
    </div>
</body>
</html>