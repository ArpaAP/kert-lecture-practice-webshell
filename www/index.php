<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>웹셸 실습 사이트</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .warning strong {
            display: block;
            margin-bottom: 5px;
            font-size: 1.1em;
        }
        .upload-form {
            margin: 30px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #667eea;
            border-radius: 10px;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s;
        }
        input[type="file"]:hover {
            background: #e8e9ff;
            border-color: #764ba2;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .message {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }
        .file-list {
            margin-top: 30px;
        }
        .file-list h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .file-item {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        .file-item:hover {
            background: #e9ecef;
        }
        .file-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .vulnerability-info {
            background: #f8d7da;
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .vulnerability-info h3 {
            color: #721c24;
            margin-bottom: 10px;
        }
        .vulnerability-info ul {
            margin-left: 20px;
            color: #721c24;
        }
        .vulnerability-info li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 웹셸 실습 사이트</h1>

        <div class="warning">
            <strong>⚠️ 경고: 교육 목적 전용</strong>
            이 사이트는 웹 보안 취약점 학습을 위한 실습 환경입니다.
            실제 운영 환경에서는 절대 사용하지 마세요!
        </div>

        <div class="upload-form">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">📁 파일 업로드:</label>
                    <input type="file" name="file" id="file" required>
                </div>
                <button type="submit">업로드</button>
            </form>
        </div>

        <?php
        $upload_dir = 'uploads/';
        if (is_dir($upload_dir)) {
            $files = array_diff(scandir($upload_dir), array('.', '..'));
            if (count($files) > 0) {
                echo '<div class="file-list">';
                echo '<h2>📂 업로드된 파일 목록:</h2>';
                foreach ($files as $file) {
                    echo '<div class="file-item">';
                    echo '<a href="uploads/' . htmlspecialchars($file) . '" target="_blank">' . htmlspecialchars($file) . '</a>';
                    echo '<span style="color: #999; font-size: 0.9em;">' . date("Y-m-d H:i:s", filemtime($upload_dir . $file)) . '</span>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }
        ?>

        <div class="vulnerability-info">
            <h3>🔓 취약점 정보</h3>
            <ul>
                <li>파일 확장자 검증 없음</li>
                <li>파일 내용 검증 없음</li>
                <li>업로드 디렉토리 실행 권한 허용</li>
                <li>파일명 필터링 없음</li>
            </ul>
            <p style="margin-top: 10px; color: #721c24;">
                <strong>실습 목표:</strong> PHP 웹셸을 업로드하고 실행해보세요!
            </p>
        </div>
    </div>
</body>
</html>
