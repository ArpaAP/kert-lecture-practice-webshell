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

            // Level 2: 기본 필터링 (중급 - 우회 가능)
            elseif ($level == 2) {
                $validation_log[] = 'Level 2 검증 시작...';

                // 취약점 1: .php 확장자만 차단 (대소문자 구분, 다른 확장자 미차단)
                if ($file_ext === 'php') {
                    $is_valid = false;
                    $message = '보안 정책: .php 파일은 업로드할 수 없습니다.';
                    $validation_log[] = '✗ 확장자 검증 실패: .php 파일 차단됨';
                } else {
                    $validation_log[] = '✓ 확장자 검증 통과: .' . $file_ext;
                }

                // 취약점 2: 간단한 키워드 블랙리스트 (대소문자 구분)
                if ($is_valid) {
                    $dangerous_keywords = ['system', 'exec', 'shell_exec', 'eval', 'passthru'];
                    foreach ($dangerous_keywords as $keyword) {
                        if (strpos($file_content, $keyword) !== false) {
                            $is_valid = false;
                            $message = "보안 정책: 위험한 함수 '{$keyword}'가 감지되었습니다.";
                            $validation_log[] = "✗ 내용 검증 실패: '{$keyword}' 키워드 발견";
                            break;
                        }
                    }
                    if ($is_valid) {
                        $validation_log[] = '✓ 내용 검증 통과: 위험한 키워드 없음';
                    }
                }
            }

            // Level 3: 강화된 필터링 (고급 - 우회 가능)
            elseif ($level == 3) {
                $validation_log[] = 'Level 3 검증 시작...';

                // 파일 크기 제한 (10KB)
                if ($file_size > 10240) {
                    $is_valid = false;
                    $message = '보안 정책: 파일 크기는 10KB 이하여야 합니다. (현재: ' . round($file_size/1024, 2) . 'KB)';
                    $validation_log[] = '✗ 크기 검증 실패: 10KB 초과';
                } else {
                    $validation_log[] = '✓ 크기 검증 통과: ' . round($file_size/1024, 2) . 'KB';
                }

                // 취약점 1: 확장자 블랙리스트 확대 (하지만 .phtml, .php5 등은 누락)
                if ($is_valid) {
                    $blocked_extensions = ['php', 'php3', 'php4', 'phar'];
                    if (in_array($file_ext, $blocked_extensions)) {
                        $is_valid = false;
                        $message = "보안 정책: .{$file_ext} 파일은 업로드할 수 없습니다.";
                        $validation_log[] = "✗ 확장자 검증 실패: .{$file_ext} 차단됨";
                    } else {
                        $validation_log[] = '✓ 확장자 검증 통과: .' . $file_ext;
                    }
                }

                // 취약점 2: MIME 타입 검증 (조작 가능)
                if ($is_valid) {
                    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];
                    if (!in_array($mime_type, $allowed_mimes)) {
                        $is_valid = false;
                        $message = "보안 정책: 허용되지 않는 파일 형식입니다. (MIME: {$mime_type})";
                        $validation_log[] = "✗ MIME 타입 검증 실패: {$mime_type}";
                    } else {
                        $validation_log[] = "✓ MIME 타입 검증 통과: {$mime_type}";
                    }
                }

                // 취약점 3: PHP 태그 검증 (<?php만 검사, 짧은 태그 미검증)
                if ($is_valid) {
                    if (stripos($file_content, '<?php') !== false) {
                        $is_valid = false;
                        $message = '보안 정책: PHP 코드가 감지되었습니다.';
                        $validation_log[] = '✗ PHP 태그 검증 실패: <?php 발견';
                    } else {
                        $validation_log[] = '✓ PHP 태그 검증 통과';
                    }
                }

                // 취약점 4: 강화된 키워드 블랙리스트 (하지만 백틱, 다른 함수 미검증)
                if ($is_valid) {
                    $dangerous_keywords = ['system', 'exec', 'shell_exec', 'eval', 'passthru',
                                          'SYSTEM', 'EXEC', 'SHELL_EXEC', 'EVAL', 'PASSTHRU',
                                          'popen', 'proc_open'];
                    foreach ($dangerous_keywords as $keyword) {
                        if (strpos($file_content, $keyword) !== false) {
                            $is_valid = false;
                            $message = "보안 정책: 위험한 함수 '{$keyword}'가 감지되었습니다.";
                            $validation_log[] = "✗ 내용 검증 실패: '{$keyword}' 발견";
                            break;
                        }
                    }
                    if ($is_valid) {
                        $validation_log[] = '✓ 내용 검증 통과: 위험한 키워드 없음';
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
            max-width: 700px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .level-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .level-1 { background: #d4edda; color: #155724; }
        .level-2 { background: #fff3cd; color: #856404; }
        .level-3 { background: #f8d7da; color: #721c24; }
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
        .validation-log {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .validation-log h3 {
            color: #495057;
            margin-bottom: 10px;
            font-family: 'Segoe UI', sans-serif;
        }
        .validation-log div {
            padding: 5px 0;
            color: #495057;
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
                    echo $level == 1 ? '초급 (필터링 없음)' :
                        ($level == 2 ? '중급 (기본 필터링)' : '고급 (강화된 필터링)');
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
