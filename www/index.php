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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border: 1px solid #e0e0e0;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #222;
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 600;
        }
        .warning {
            background: #fff9e6;
            border: 1px solid #ffcc00;
            padding: 16px;
            margin: 20px 0;
            color: #856404;
        }
        .warning strong {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .level-selector {
            margin: 30px 0;
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 20px;
        }
        .level-selector h3 {
            color: #222;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
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
            padding: 12px;
            border: 1px solid #d0d0d0;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            font-size: 14px;
        }
        .level-option input[type="radio"]:checked + label {
            border-color: #333;
            background: #333;
            color: white;
        }
        .level-option label:hover {
            border-color: #666;
        }
        .level-info {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 16px;
            margin-top: 15px;
            display: none;
        }
        .level-info.active {
            display: block;
        }
        .level-info h4 {
            color: #222;
            margin-bottom: 10px;
            font-size: 15px;
            font-weight: 600;
        }
        .level-info ul {
            margin-left: 20px;
            color: #555;
            font-size: 14px;
        }
        .level-info li {
            margin: 6px 0;
        }
        .hint-box {
            background: #f0f7ff;
            border-left: 3px solid #0066cc;
            padding: 12px 16px;
            margin-top: 12px;
            font-size: 13px;
        }
        .hint-box strong {
            color: #0066cc;
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
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d0d0d0;
            background: #fafafa;
            cursor: pointer;
            font-size: 14px;
        }
        input[type="file"]:hover {
            border-color: #999;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #333;
            color: white;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #000;
        }
        .file-list {
            margin-top: 30px;
        }
        .file-list h2 {
            color: #222;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
        .file-item {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-item:hover {
            background: #f0f0f0;
        }
        .file-item a {
            color: #0066cc;
            text-decoration: none;
            font-size: 14px;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>웹셸 실습 사이트</h1>

        <div class="level-selector">
            <h3>난이도 선택</h3>
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
                <h4>Level 1 - 초급 (필터링 없음)</h4>
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
                <h4>Level 2 - 중급 (확장자 + MIME 타입 검증)</h4>
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
                echo '<h2>업로드된 파일 목록:</h2>';
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
