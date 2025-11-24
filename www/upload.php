<?php
// 취약한 파일 업로드 스크립트 (교육 목적)

$upload_dir = 'uploads/';
$message = '';
$message_type = '';
$validation_log = [];

// 난이도 레벨 받기 (기본값: 1)
$level = isset($_POST['level']) ? intval($_POST['level']) : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // 업로드 에러 체크
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = '파일 업로드 중 오류가 발생했습니다.';
            $message_type = 'error';
        } else {
            $filename = basename($file['name']);
            $target_path = $upload_dir . $filename;
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $file_content = file_get_contents($file['tmp_name']);
            $file_size = $file['size'];
            $mime_type = $file['type'];

            $is_valid = true;

            // 업로드 디렉토리가 없으면 생성
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Level 1: 필터링 없음 (초급)
            if ($level == 1) {
                $validation_log[] = '✓ Level 1: 검증 없음 - 모든 파일 허용';
            }

            // Level 2: 확장자 + MIME 타입 검증 (중급 - 우회 가능)
            elseif ($level == 2) {
                $validation_log[] = 'Level 2 검증 시작...';

                // 취약점 1: .php 확장자만 차단 (.php5, .phtml 등은 허용)
                if ($file_ext === 'php') {
                    $is_valid = false;
                    $message = '보안 정책: .php 파일은 업로드할 수 없습니다.';
                    $validation_log[] = '✗ 확장자 검증 실패: .php 파일 차단됨';
                } else {
                    $validation_log[] = '✓ 확장자 검증 통과: .' . $file_ext;
                }

                // 취약점 2: MIME 타입에 'php' 포함 여부만 확인 (조작 가능)
                if ($is_valid) {
                    if (stripos($mime_type, 'php') !== false) {
                        $is_valid = false;
                        $message = "보안 정책: PHP 관련 파일은 업로드할 수 없습니다. (MIME: {$mime_type})";
                        $validation_log[] = "✗ MIME 타입 검증 실패: {$mime_type}";
                    } else {
                        $validation_log[] = "✓ MIME 타입 검증 통과: {$mime_type}";
                    }
                }
            }

            // 파일 업로드 실행
            if ($is_valid) {
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    chmod($target_path, 0644);
                    $message = "✓ 파일이 성공적으로 업로드되었습니다: {$filename}";
                    $message_type = 'success';
                    $validation_log[] = '✓ 파일 업로드 완료!';
                } else {
                    $message = '파일 업로드에 실패했습니다.';
                    $message_type = 'error';
                    $validation_log[] = '✗ 파일 이동 실패';
                }
            } else {
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border: 1px solid #e0e0e0;
            max-width: 800px;
            width: 100%;
            margin: 40px auto;
            padding: 40px;
        }
        h1 {
            color: #222;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
        }
        .level-badge {
            display: inline-block;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid #d0d0d0;
            background: #fafafa;
            color: #333;
        }
        .message {
            padding: 16px;
            border: 1px solid #d0d0d0;
            margin: 20px 0;
            font-size: 15px;
        }
        .success {
            background: #f0fff4;
            border-color: #90ee90;
            color: #155724;
        }
        .error {
            background: #fff5f5;
            border-color: #ffb3ba;
            color: #721c24;
        }
        .validation-log {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .validation-log h3 {
            color: #222;
            margin-bottom: 12px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            font-weight: 600;
        }
        .validation-log div {
            padding: 4px 0;
            color: #555;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 32px;
            background: #333;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }
        .back-button:hover {
            background: #000;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>업로드 결과</h1>

        <div class="center">
            <span class="level-badge level-<?php echo $level; ?>">
                Level <?php echo $level; ?> -
                <?php
                    echo $level == 1 ? '초급 (필터링 없음)' : '중급 (확장자 + MIME 검증)';
                ?>
            </span>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($validation_log)): ?>
            <div class="validation-log">
                <h3>🔍 검증 로그</h3>
                <?php foreach ($validation_log as $log): ?>
                    <div><?php echo htmlspecialchars($log); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="center">
            <a href="index.php" class="back-button">← 돌아가기</a>
        </div>
    </div>
</body>
</html>
