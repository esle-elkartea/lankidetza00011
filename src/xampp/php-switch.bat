@ECHO OFF

if exist php\php.exe GOTO Normal
if not exist php\php.exe GOTO Abort

:Abort
echo Sorry ... cannot find php cli!
echo Must abort these process!
pause
GOTO END

:Normal
set PHP_BIN=php\php.exe
set CONFIG_PHP=install\php-switch.php
%PHP_BIN% -n -d output_buffering=0 %CONFIG_PHP%
GOTO END

:END
pause
