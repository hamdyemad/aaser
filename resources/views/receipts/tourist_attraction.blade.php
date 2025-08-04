<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إيصال الفاتورة</title>
    <style>
        body {
            direction: rtl;
            text-align: right;
            background-color: #004d40;
            color: white;
        }

        .receipt {
            background-color: #00332e;
            padding: 20px;
            border-radius: 10px;
        }

        .header {
            margin: auto;
            width: 100px;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            margin: auto;
        }




        .title {
            text-align: center;
            color: #f9c846;
            margin: 20px 0;
            font-size: 20px;
        }


        .date-section {
            float: left;
        }

        .user-info {
            float: right;
            width: 70%;
        }


        .date-info p, .user-info p {
            font-size: 14px;
            margin: 3px 0;
        }

        .table {
            margin-top: 20px;
            border: 1px solid #f9c846;
            border-radius: 5px;
            width: 100%;
            color: white;
        }

        .table-header,
        .table-row {
            background-color: #004d40;
            border-bottom: 1px solid #f9c846;
        }

        .table-header {
            background-color: #f9c846;
            color: #00332e;
            font-weight: bold;
        }

        .table td {
            padding: 8px;
            font-size: 14px;
        }

        .total {
            margin-top: 15px;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 25px;
            background-color: #002d26;
            padding: 10px;
            border-radius: 10px;
        }

        .footer h4 {
            color: #f9c846;
            margin-bottom: 5px;
        }

        .footer .left,
        .footer .right {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="receipt">

        {{-- شعار --}}
        @php
            $logo = base64_encode(file_get_contents(public_path('assets/logo.png')));
        @endphp
        <div class="header">
            <img src="data:image/png;base64,{{ $logo }}" class="logo" alt="logo">
        </div>
        <div class="title">إيصال الفاتورة</div>
        <div class="info-section">
            <div class="user-info">
                <p>الاسم: {{ $reward_request->user->name }}</p>
                <p>رقم الهاتف: {{ $reward_request->user->phone }}</p>
            </div>
            <div class="date-info">
                <p>رقم الفاتورة: {{ $reward_request->request_id }}</p>
                <p>تاريخ الفاتورة: {{ $reward_request->created_at }}</p>
            </div>
        </div>

        {{-- الجدول --}}
        <table class="table" cellspacing="0">
            <tr class="table-header">
                <td>اسم الخدمة</td>
                <td>السعر</td>
                <td>العدد</td>
                <td>المبلغ الاجمالى</td>
                <td>التاريخ</td>
                <td>النقاط المكتسبة</td>
            </tr>
            @php
                $points = 0;
                $total = 0;
            @endphp
            @foreach ($reward_request->requestTouriste as $requestTouriste)
                @php
                    $points += $requestTouriste['serviceTouristAttraction']['earn_points'];
                    $total += $requestTouriste['qty'] * $requestTouriste['serviceTouristAttraction']['price'];
                @endphp
                <tr class="table-row">
                    <td>{{ $requestTouriste['serviceTouristAttraction']['name'] }}</td>
                    <td>{{ $requestTouriste['serviceTouristAttraction']['price'] }}</td>
                    <td>{{ $requestTouriste['qty'] }}</td>
                    <td>{{ $requestTouriste['qty'] * $requestTouriste['serviceTouristAttraction']['price'] }}</td>
                    <td>{{ $requestTouriste['serviceTouristAttraction']['appointment'] }}</td>
                    <td>{{ $requestTouriste['serviceTouristAttraction']['earn_points'] }}</td>
                </tr>
            @endforeach
        </table>

        <div class="total">المبلغ الاجمالى: {{ $total }} ريال</div>
        <div class="total">مجموع النقاط: {{ $points }} نقطة</div>

        {{-- الفوتر --}}
        {{-- <div class="footer">
            <div>
                <h4>مقدم الخدمة</h4>
                <p>اسم الشركة: شسينشسينمشسيسشيشسي</p>
                <p>اسم الشركة: شسينشسينمشسيسشيشسي</p>
                <p>اسم الشركة: شسينشسينمشسيسشيشسي</p>
                <p>اسم الشركة: شسينشسينمشسيسشيشسي</p>
            </div>
        </div> --}}
    </div>
</body>
</html>
