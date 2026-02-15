<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 20px; }
        .header { text-align: center; border-bottom: 4px solid #800000; padding-bottom: 10px; margin-bottom: 30px; }
        .univ-name { font-size: 18px; font-weight: bold; color: #800000; margin: 0; text-transform: uppercase; }
        .office-name { font-size: 12px; font-weight: bold; margin: 5px 0; }
        .address { font-size: 9px; color: #666; }
        
        .report-title { text-align: center; margin-top: 30px; font-size: 16px; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .date { text-align: center; font-size: 10px; margin-bottom: 30px; color: #777; }
        
        /* Stats Grid */
        .stats-container { margin-bottom: 30px; width: 100%; }
        .stat-card { 
            background: #fdf2f2; 
            padding: 15px; 
            border-left: 5px solid #800000; 
            margin-bottom: 10px; 
            width: 30%; 
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }
        .stat-label { font-size: 9px; font-weight: bold; color: #800000; text-transform: uppercase; }
        .stat-value { font-size: 18px; font-weight: bold; display: block; margin-top: 5px; color: #333; }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
        th { background-color: #800000; color: white; padding: 8px; text-align: left; text-transform: uppercase; }
        td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    {{-- Header Section --}}
    <div class="header">
        <div class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</div>
        <div class="office-name">OFFICE OF THE VICE PRESIDENT FOR STUDENT AFFAIRS AND SERVICES</div>
        <div class="address">Anonas St., Sta. Mesa, Manila, Philippines 1016</div>
    </div>

    <div class="report-title">{{ $title }}</div>
    <div class="date">Report generated on {{ $generatedAt }}</div>

    {{-- Summary Cards --}}
    <div class="stats-container">
        <div class="stat-card">
            <span class="stat-label">Total Documents</span>
            <span class="stat-value">{{ $totalCount }}</span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Active Units</span>
            <span class="stat-value">{{ $activeUnits }} Units</span>
        </div>
        
        <div class="stat-card" style="margin-right: 0;">
            <span class="stat-label">Status</span>
            <span class="stat-value">Active</span>
        </div>
    </div>

    {{-- Detailed Activity Table --}}
    <h3 style="font-size: 12px; color: #800000; border-bottom: 1px solid #eee; padding-bottom: 5px;">RECENT SYSTEM ACTIVITY</h3>
    <table>
        <thead>
            <tr>
                <th width="30%">Document Title</th>
                <th width="25%">Office/s</th>
                <th width="25%">Category</th>
                <th width="20%">Date Uploaded</th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $a)
                <tr>
                    <td><strong>{{ $a->title }}</strong></td>
                    <td>
                        {{-- Handles both Array and Legacy String data --}}
                        {{ is_array($a->office) ? implode(', ', $a->office) : $a->office }}
                    </td>
                    <td>
                        {{ is_array($a->category) ? implode(', ', $a->category) : $a->category }}
                    </td>
                    <td>{{ $a->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999; font-style: italic;">No activity records found in the database.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Student Affairs Services and Information System (SASIS) - Internal Administrative Report
    </div>
</body>
</html>