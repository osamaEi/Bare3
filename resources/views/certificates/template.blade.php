<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; }
        .cert {
            width: 100%; height: 540px; padding: 30px;
            background: #fff; position: relative;
            border: 14px solid #38BDF8;
            outline: 4px solid #EC4899; outline-offset: -22px;
        }
        .inner { text-align: center; padding: 30px 50px; }
        .brand { font-size: 30px; font-weight: bold; color: #0E7490; margin-bottom: 6px; }
        .brand .p { color: #EC4899; }
        .ttl { font-size: 22px; color: #6B7280; letter-spacing: 2px; margin-bottom: 26px; }
        .label { font-size: 14px; color: #9CA3AF; margin-bottom: 10px; }
        .name { font-size: 38px; font-weight: bold; color: #1C1C2E; margin-bottom: 8px;
                border-bottom: 3px solid #84CC16; display: inline-block; padding: 0 24px 8px; }
        .desc { font-size: 16px; color: #4B5563; margin: 24px 0 6px; }
        .path { font-size: 24px; font-weight: bold; color: #0E7490; margin-bottom: 30px; }
        .footer { position: absolute; bottom: 40px; left: 60px; right: 60px; }
        .row { width: 100%; }
        .cell { display: inline-block; vertical-align: bottom; }
        .left { float: left; text-align: center; }
        .right { float: right; text-align: right; }
        .sig-line { border-top: 2px solid #1C1C2E; width: 180px; padding-top: 6px; font-size: 13px; color: #6B7280; }
        .meta { font-size: 12px; color: #9CA3AF; }
        .meta strong { color: #4B5563; }
        .qr { width: 110px; height: 110px; }
    </style>
</head>
<body>
    <div class="cert">
        <div class="inner">
            <div class="brand">بارع<span class="p">.</span></div>
            <div class="ttl">شهادة إنجاز</div>

            <div class="label">تشهد منصة بارع بأن الطالب</div>
            <div class="name">{{ $studentName }}</div>

            <div class="desc">قد أتمّ بنجاح جميع دروس مسار</div>
            <div class="path">{{ $pathTitle }}</div>
        </div>

        <div class="footer">
            <div class="row" style="overflow: hidden;">
                <div class="cell right">
                    <img class="qr" src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR" />
                </div>
                <div class="cell left">
                    <div class="sig-line">إدارة منصة بارع</div>
                </div>
            </div>
            <div class="meta" style="clear: both; text-align: center; margin-top: 18px;">
                رقم الشهادة: <strong>{{ $certNumber }}</strong>
                &nbsp;·&nbsp; تاريخ الإصدار: <strong>{{ $issuedAt->format('Y-m-d') }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
