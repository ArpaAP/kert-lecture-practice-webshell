# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an **educational web security practice environment** for learning about file upload vulnerabilities and web shell attacks. The project is intentionally vulnerable and designed for security training purposes only.

**WARNING**: This codebase contains intentionally vulnerable code for educational purposes. Never deploy this to production or expose it to public networks.

## Architecture

### Technology Stack
- **Web Server**: Nginx
- **Application**: PHP 8.1 + PHP-FPM
- **Container**: Docker + Docker Compose
- **Process Management**: Supervisor

### Directory Structure
- `www/index.php` - Main interface with level selection and file upload UI
- `www/upload.php` - File upload handler with 3 levels of intentionally weak validation
- `www/uploads/` - Directory where uploaded files are stored (writable)
- `www/bypass-samples/` - Sample files demonstrating bypass techniques for each level
- `nginx.conf` - Nginx configuration
- `supervisord.conf` - Supervisor process manager configuration
- `Dockerfile` - Ubuntu 22.04 based image with Nginx + PHP-FPM

### Security Levels (Intentional Vulnerabilities)

**Level 1** (www/upload.php:36-38):
- No filtering whatsoever
- All files accepted

**Level 2** (www/upload.php:41-68):
- Blocks only `.php` extension (lowercase, case-sensitive)
- Simple keyword blacklist: `system`, `exec`, `shell_exec`, `eval`, `passthru` (case-sensitive)
- Vulnerable to: extension bypass (.phtml, .php5), case mixing (sYsTeM), backtick operator

**Level 3** (www/upload.php:71-135):
- File size limit: 10KB
- Blocks `.php`, `.php3`, `.php4`, `.phar` (but not `.phtml`, `.php5`)
- MIME type validation (client-controlled, easily bypassed)
- Blocks `<?php` tag but not short tags `<?`
- Extended keyword blacklist with case insensitivity (but misses backticks, `pcntl_exec`, `assert`)

## Common Commands

### Start Environment
```bash
docker-compose up -d --build
```

### Stop Environment
```bash
docker-compose down
```

### View Logs
```bash
docker-compose logs -f
```

### Access Application
```
http://localhost:8080
```

### Test Web Shell (after successful upload)
```
http://localhost:8080/uploads/[filename].php?cmd=ls
http://localhost:8080/uploads/[filename].phtml?cmd=whoami
```

### Upload File with MIME Type Manipulation (Level 3)
```bash
curl -X POST \
  -F "level=3" \
  -F "file=@shell.phtml;type=image/png" \
  http://localhost:8080/upload.php
```

## Key Implementation Details

### Validation Flow (www/upload.php)
1. Level determination from POST parameter (line 10)
2. File upload error check (line 17)
3. Extract filename, extension, content, size, MIME type (lines 21-26)
4. Level-specific validation (lines 36-135)
5. File move and permission setting (lines 139-141)

### Nginx Configuration
- PHP files in uploads directory ARE executable (this is intentional for the practice environment)
- In real applications, you would block PHP execution in upload directories

### PHP-FPM Setup
- Configured to listen on 127.0.0.1:9000 (modified in Dockerfile line 17)
- Default socket connection replaced with TCP connection

## Code Analysis Notes

When analyzing or modifying this code:

1. **Never improve the security vulnerabilities** - they are educational features
2. The validation logic intentionally uses weak patterns (case-sensitive checks, incomplete blacklists, client-side MIME validation)
3. The `$validation_log` array provides detailed feedback about what checks passed/failed for learning purposes
4. File permissions are intentionally permissive (chmod 777 on uploads/, 644 on uploaded files)

## Testing Scenarios

Each level demonstrates specific bypass techniques:

**Level 2 Bypasses**:
- Extension: .phtml, .php5, .php3 instead of .php
- Function keywords: sYsTeM, SYSTEM, backtick operator, pcntl_exec

**Level 3 Bypasses**:
- Extension: .phtml, .php5 (not in blacklist)
- PHP tags: `<?` or `<?=` instead of `<?php`
- MIME type: Manipulate with Burp Suite, curl, or Python requests
- Functions: Backtick operator, pcntl_exec, assert

## Important Constraints

1. **No security improvements**: Do not fix vulnerabilities, add proper validation, or improve security measures
2. **Educational context**: All analysis should explain what the vulnerabilities are and how they work
3. **No malicious enhancements**: Can analyze existing bypass techniques but should not create new attack vectors
4. **Documentation only**: Can create reports, explanations, and educational materials about the vulnerabilities
