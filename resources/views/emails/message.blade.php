<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>رسائل عسير الإلكترونية</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; }
    body { margin: 0; padding: 0; width: 100% !important; }
    table { border-collapse: collapse !important; }
    
    /* CUSTOM STYLES */
    body {
      font-family: 'Cairo', 'Amiri', Arial, sans-serif;
      background: linear-gradient(135deg, #0d4f3c 0%, #1a5f4a 50%, #0d4f3c 100%);
      direction: rtl;
      min-height: 100vh;
    }

    .main-container {
      background: linear-gradient(135deg, #0d4f3c 0%, #1a5f4a 50%, #0d4f3c 100%);
      padding: 20px 0;
      min-height: 100vh;
    }

    .email-wrapper {
      max-width: 700px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(13, 79, 60, 0.3);
    }

    .header {
      background: linear-gradient(135deg, #0d4f3c 0%, #1a5f4a 100%);
      padding: 40px 30px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .header::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      animation: float 20s linear infinite;
    }

    @keyframes float {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .logo {
      font-size: 2.8rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 10px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      position: relative;
      z-index: 2;
      font-family: 'Amiri', serif;
    }

    .logo::before {
      content: "🏔️";
      margin-left: 15px;
      font-size: 2.5rem;
    }

    .subtitle {
      color: #e8f5e8;
      font-size: 1.2rem;
      font-weight: 300;
      position: relative;
      z-index: 2;
      font-family: 'Cairo', sans-serif;
    }

    .content-area {
      padding: 40px 30px;
    }

    .block {
      background: #f8fffe;
      border: 2px solid #e8f5e8;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 25px;
      position: relative;
      transition: all 0.3s ease;
    }

    .block:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(13, 79, 60, 0.15);
    }

    .block h3 {
      color: #0d4f3c;
      font-size: 1.3rem;
      font-weight: 600;
      margin: 0 0 15px 0;
      font-family: 'Cairo', sans-serif;
      position: relative;
    }

    .greeting-block {
      background: linear-gradient(135deg, #0d4f3c, #1a5f4a);
      color: white;
      text-align: center;
      border: none;
    }

    .greeting-block h3 {
      color: white;
      font-size: 1.5rem;
      margin: 0;
    }

    .greeting-block::before {
      content: "👋";
      position: absolute;
      top: -10px;
      right: 20px;
      background: #ffffff;
      padding: 8px 12px;
      border-radius: 50%;
      font-size: 1.3rem;
    }

    .message-block::before {
      content: "💬";
      position: absolute;
      top: -10px;
      right: 20px;
      background: #ffffff;
      padding: 8px 12px;
      border-radius: 50%;
      font-size: 1.2rem;
    }

    .link-block::before {
      content: "🔗";
      position: absolute;
      top: -10px;
      right: 20px;
      background: #ffffff;
      padding: 8px 12px;
      border-radius: 50%;
      font-size: 1.2rem;
    }

    .image-block::before {
      content: "🖼️";
      position: absolute;
      top: -10px;
      right: 20px;
      background: #ffffff;
      padding: 8px 12px;
      border-radius: 50%;
      font-size: 1.2rem;
    }

    .file-block::before {
      content: "📎";
      position: absolute;
      top: -10px;
      right: 20px;
      background: #ffffff;
      padding: 8px 12px;
      border-radius: 50%;
      font-size: 1.2rem;
    }

    .block img {
      max-width: 100%;
      height: auto;
      max-height: 400px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(13, 79, 60, 0.2);
      margin-top: 10px;
      transition: all 0.3s ease;
    }

    .block img:hover {
      transform: scale(1.02);
      box-shadow: 0 15px 40px rgba(13, 79, 60, 0.3);
    }

    .link-button {
      display: inline-block;
      background: linear-gradient(135deg, #0d4f3c, #1a5f4a);
      color: #ffffff !important;
      padding: 15px 30px;
      text-decoration: none;
      border-radius: 25px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px rgba(13, 79, 60, 0.3);
      margin-top: 10px;
      font-family: 'Cairo', sans-serif;
    }

    .link-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(13, 79, 60, 0.4);
      color: #ffffff !important;
    }

    .download-button {
      display: inline-block;
      background: linear-gradient(135deg, #0d4f3c, #1a5f4a);
      color: #ffffff !important;
      padding: 15px 30px;
      text-decoration: none;
      border-radius: 25px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px rgba(13, 79, 60, 0.3);
      margin-top: 10px;
      font-family: 'Cairo', sans-serif;
    }

    .download-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(13, 79, 60, 0.4);
      color: #ffffff !important;
    }

    .download-button::before {
      content: "⬇️ ";
      margin-left: 8px;
    }

    .footer {
      background: linear-gradient(135deg, #f8fffe, #e8f5e8);
      padding: 30px;
      text-align: center;
      border-top: 3px solid #0d4f3c;
    }

    .footer-content {
      color: #0d4f3c;
      font-size: 1rem;
      line-height: 1.6;
      font-family: 'Cairo', sans-serif;
    }

    .aseer-pattern {
      font-size: 2rem;
      margin: 15px 0;
      opacity: 0.8;
    }

    .copyright {
      font-size: 0.9rem;
      color: #2d5a4a;
      margin-top: 15px;
      font-weight: 500;
    }

    .date-stamp {
      background: #0d4f3c;
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      display: inline-block;
      margin-top: 15px;
    }

    /* RESPONSIVE STYLES */
    @media screen and (max-width: 600px) {
      .email-wrapper { 
        margin: 10px;
        border-radius: 15px;
      }
      
      .header, .content-area, .footer { 
        padding: 25px 20px;
      }
      
      .logo { 
        font-size: 2.2rem;
      }
      
      .block {
        padding: 20px;
        margin-bottom: 20px;
      }
      
      .block h3 {
        font-size: 1.1rem;
      }
      
      .link-button, .download-button {
        padding: 12px 25px;
        font-size: 1rem;
      }
    }

    /* EMAIL CLIENT COMPATIBILITY */
    @media screen and (max-width: 480px) {
      .main-table { width: 100% !important; }
      .content { padding: 15px !important; }
    }
  </style>
</head>
<body>
  <div class="main-container">
    <div class="email-wrapper">
      <!-- HEADER -->
      <div class="header">
        <div class="logo">رسائل عسير</div>
        <div class="subtitle">من قلب الجبال الشامخة إلى قلبك الكريم</div>
      </div>

      <!-- CONTENT -->
      <div class="content-area">
        @if(isset($user))
          <div class="block greeting-block">
            <h3>السلام عليكم ورحمة الله وبركاته، {{ $user->name }}!</h3>
          </div>
        @endif

        @if(isset($customMessage) && $customMessage != '')
          <div class="block message-block">
            <h3>محتوى الرسالة:</h3>
            <p style="color: #2d5a4a; font-size: 1.1rem; line-height: 1.8; margin: 0; font-family: 'Cairo', sans-serif;">
              {{ $customMessage }}
            </p>
          </div>
        @endif

        @if(isset($link) && $link != '')
          <div class="block link-block">
            <h3>رابط مهم لك:</h3>
            <a href="{{ $link }}" class="link-button" target="_blank">
              زيارة الرابط 🚀
            </a>
          </div>
        @endif

        @if(isset($image) && $image != '')
          <div class="block image-block">
            <h3>صورة مرفقة:</h3>
            <img src="{{ asset('storage/' . $image) }}" alt="صورة مرفقة">
          </div>
        @endif

        @if(isset($file) && $file != '')
          <div class="block file-block">
            <h3>ملف مرفق:</h3>
            <a href="{{ asset('storage/' . $file) }}" class="download-button" target="_blank">
              تحميل الملف
            </a>
          </div>
        @endif
      </div>

      <!-- FOOTER -->
      <div class="footer">
        <div class="footer-content">
          <div class="aseer-pattern">🏔️ ⭐ 🌙 ⭐ 🏔️</div>
          <p><strong>من أرض عسير الحبيبة</strong></p>
          <p>حيث الجبال الشامخة والتراث العريق</p>
          <p>نرسل لك أطيب التحيات وأجمل الأمنيات</p>
          <div class="date-stamp">
            📅 {{ date('Y-m-d H:i:s') }}
          </div>
          <div class="copyright">
            &copy; {{ date('Y') }} عسير. جميع الحقوق محفوظة.
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>