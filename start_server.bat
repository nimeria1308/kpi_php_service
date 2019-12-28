@echo off

setlocal EnableDelayedExpansion

rem include config
call config.cmd

rem setup database
%XAMPP_DIR%/php/php setup_database.php

rem create the logs dir if missing
if not exist logs\ mkdir logs

rem export the projects directory
set PROJECT_DIR=%CD%

rem run apache
echo Starting Apache
%XAMPP_DIR%/apache/bin/httpd -d .
