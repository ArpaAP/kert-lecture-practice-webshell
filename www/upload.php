<?php
// 취약한 파일 업로드 스크립트 (교육 목적)

$upload_dir = 'uploads/';
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // 업로드 에러 체크
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = '파일 업로드 중 오류가 발생했습니다.';
            $message_type = 'error';
        } else {
            // 취약점: 파일 확장자 및 내용 검증 없음!
            $filename = basename($file['name']);
            $target_path = $upload_dir . $filename;

            // 업로드 디렉토리가 없으면 생성
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // 파일 이동 (취약점: 모든 파일 허용)
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // 실행 권한 부여 (취약점!)
                chmod($target_path, 0644);

                $message = "파일이 성공적으로 업로드되었습니다: $filename";
                $message_type = 'success';
            } else {
                $message = '파일 업로드에 실패했습니다.';
                $message_type = 'error';
            }
        }
    } else {
        $message = '파일이 선택되지 않았습니다.';
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>업로드 결과</title>
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
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .message {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.1em;
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
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>업로드 결과</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="back-button">← 돌아가기</a>
    </div>
</body>
</html>
