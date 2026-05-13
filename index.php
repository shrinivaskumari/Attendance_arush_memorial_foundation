<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arush Memorial Convent School - Main</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            min-height: 100vh;
        }
        .founder-images-row {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2vw;
            padding-top: 24px;
            padding-bottom: 8px;
        }
        .founder-img {
            max-height: 180px;
            width: auto;
            border-radius: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            background: #fff;
            object-fit: contain;
        }
        .founder-caption {
            text-align: center;
            font-size: 1.1rem;
            color: #444;
            font-weight: 500;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .school-header {
            position: relative;
            color: #fff;
            padding: 60px 0 40px 0;
            text-align: center;
            border-radius: 0 0 40px 40px;
            background: #222;
            overflow: hidden;
        }
        .header-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(120deg, rgba(44,62,80,0.7) 0%, rgba(79,140,255,0.5) 100%);
            z-index: 2;
        }
        .school-header-content {
            position: relative;
            z-index: 3;
        }
        .school-logo { width: 120px; margin-bottom: 20px; }
        .school-title { font-size: 2.5rem; font-weight: 700; letter-spacing: 1px; }
        .school-info { font-size: 1.1rem; margin-bottom: 10px; }
        .main-btn { background: #4f8cff; color: #fff; border-radius: 30px; padding: 12px 36px; font-size: 1.2rem; font-weight: 600; transition: 0.2s, box-shadow 0.4s; box-shadow: 0 0 0 0 #2563eb; animation: btn-glow 2s infinite alternate; }
        .main-btn:hover { background: #2563eb; color: #fff; box-shadow: 0 0 16px 4px #4f8cff66; animation: none; }
        @keyframes btn-glow { 0% { box-shadow: 0 0 0 0 #4f8cff44; } 100% { box-shadow: 0 0 24px 8px #4f8cff99; } }
        .svg-wave { display: block; width: 100%; height: 60px; margin-bottom: -1px; position: relative; z-index: 3; }
        .students-section { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 40px 0; margin-top: -40px; position: relative; z-index: 2; }
        .student-img { width: 100%; max-width: 220px; border-radius: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .footer { background: #4f8cff; color: #fff; text-align: center; padding: 18px 0; margin-top: 40px; border-radius: 30px 30px 0 0; position: relative; z-index: 2; }
        @media (max-width: 991px) {
            .founder-img { max-height: 110px; }
        }
        @media (max-width: 767px) {
            .school-title { font-size: 1.5rem; }
            .students-section { padding: 20px 0; }
            .founder-img { max-height: 60px; }
            .founder-caption { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
    <div class="founder-images-row">
        <img src="student1.png" alt="Arush Memorial Son 1" class="founder-img">
        <img src="student2.png" alt="Arush Memorial Son 2" class="founder-img">
        <img src="student3.png" alt="Arush Memorial Son 3" class="founder-img">
    </div>
    <div class="founder-caption">In Loving Memory of the Founder</div>
    <div class="school-header">
        <div class="header-overlay"></div>
        <div class="school-header-content">
            <img src="school_logo.png" alt="School Logo" class="school-logo d-none">
            <div class="school-title">Arush Memorial Convent School</div>
            <div class="school-info">At Post: Zinganoor, Ta. Sironcha, District: Gadchiroli 442504 (MS)</div>
            <div class="school-info">Regd. No: F-0007544(GDC) | Run by: Arush Memorial Foundation Zinganoor</div>
            <div class="school-info">Mobile: <a href="tel:9423902024" style="color:#fff;text-decoration:underline;">9423902024</a></div>
            <a href="main.php" class="btn main-btn mt-4">Attendance Management System</a>
        </div>
        <svg class="svg-wave" viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#fff" fill-opacity="1" d="M0,32L60,37.3C120,43,240,53,360,58.7C480,64,600,64,720,58.7C840,53,960,43,1080,42.7C1200,43,1320,53,1380,58.7L1440,64L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
        </svg>
    </div>
    <div class="container students-section text-center">
        <h2 class="mb-4" style="font-weight:600;">Our Happy Students</h2>
        <div class="row justify-content-center">
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <img src="student4.png" alt="Student 1" class="student-img">
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <img src="student5.png" alt="Student 2" class="student-img">
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <img src="student6.png" alt="Student 3" class="student-img">
            </div>
        </div>
    </div>
    <div class="footer">
        &copy; <?php echo date('Y'); ?> Arush Memorial Convent School | Powered by Arush Memorial Foundation Zinganoor
    </div>
</body>
</html> 