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
            max-width: 800px;
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
        .level-selector {
            margin: 25px 0;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .level-selector h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        .level-options {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .level-option {
            flex: 1;
        }
        .level-option input[type="radio"] {
            display: none;
        }
        .level-option label {
            display: block;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        .level-option input[type="radio"]:checked + label {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        .level-option label:hover {
            border-color: #667eea;
        }
        .level-info {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            display: none;
        }
        .level-info.active {
            display: block;
        }
        .level-info h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .level-info ul {
            margin-left: 20px;
            color: #555;
        }
        .level-info li {
            margin: 5px 0;
        }
        .hint-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 10px 15px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .hint-box strong {
            color: #1976D2;
        }
        .upload-form {
            margin: 30px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label.file-label {
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

        <div class="level-selector">
            <h3>🎮 난이도 선택</h3>
            <div class="level-options">
                <div class="level-option">
                    <input type="radio" name="level" id="level1" value="1" checked>
                    <label for="level1">
                        Level 1<br>
                        <small>초급</small>
                    </label>
                </div>
                <div class="level-option">
                    <input type="radio" name="level" id="level2" value="2">
                    <label for="level2">
                        Level 2<br>
                        <small>중급</small>
                    </label>
                </div>
            </div>

            <div id="level1-info" class="level-info active">
                <h4>📘 Level 1 - 초급 (필터링 없음)</h4>
                <ul>
                    <li>파일 확장자 검증 없음</li>
                    <li>MIME 타입 검증 없음</li>
                    <li>모든 파일 업로드 허용</li>
                </ul>
                <div class="hint-box">
                    <strong>💡 힌트:</strong> 어떤 PHP 파일이든 그대로 업로드할 수 있습니다!
                </div>
            </div>

            <div id="level2-info" class="level-info">
                <h4>📙 Level 2 - 중급 (확장자 + MIME 타입 검증)</h4>
                <ul>
                    <li>❌ <code>.php</code> 확장자 차단</li>
                    <li>❌ MIME 타입에 <code>php</code> 문자열 포함 시 차단</li>
                    <li>✅ <code>.php5</code>, <code>.phtml</code> 등 다른 PHP 확장자는 허용</li>
                    <li>✅ MIME 타입은 클라이언트에서 조작 가능</li>
                </ul>
                <div class="hint-box">
                    <strong>💡 힌트:</strong>
                    확장자를 <code>.phtml</code> 또는 <code>.php5</code>로 변경하고,
                    MIME 타입(Content-Type)을 <code>image/jpeg</code>나 <code>text/plain</code>으로 설정해보세요!
                    <br><br>
                    <strong>MIME 타입 변경 방법:</strong><br>
                    - Burp Suite로 요청 가로채서 Content-Type 수정<br>
                    - curl: <code>curl -F "file=@shell.phtml;type=image/jpeg" ...</code>
                </div>
            </div>
        </div>

        <div class="upload-form">
            <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="level" id="selectedLevel" value="1">
                <div class="form-group">
                    <label for="file" class="file-label">📁 파일 업로드:</label>
                    <input type="file" name="file" id="file" required>
                </div>
                <button type="submit">업로드</button>
            </form>
        </div>

        <?php
        $upload_dir = 'uploads/';
        if (is_dir($upload_dir)) {
            $files = array_diff(scandir($upload_dir), array('.', '..', '.gitkeep'));
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
    </div>

    <script>
        // 레벨 선택 시 정보 표시 및 hidden 필드 업데이트
        const levelRadios = document.querySelectorAll('input[name="level"]');
        const levelInfos = document.querySelectorAll('.level-info');
        const selectedLevelInput = document.getElementById('selectedLevel');

        levelRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const level = e.target.value;

                // 모든 정보 숨기기
                levelInfos.forEach(info => info.classList.remove('active'));

                // 선택된 레벨 정보 표시
                document.getElementById(`level${level}-info`).classList.add('active');

                // hidden 필드 업데이트
                selectedLevelInput.value = level;
            });
        });
    </script>
</body>
</html>
