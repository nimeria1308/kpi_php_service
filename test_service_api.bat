@echo off

setlocal EnableDelayedExpansion

rem include config
call config.cmd

rem start model test script
%XAMPP_DIR%/php/php tests/test_service_api.php
