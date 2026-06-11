# DocuTrack Professional Quality Check Script
# This script runs static analysis and tests for both Laravel and Flutter.

$ErrorActionPreference = "Stop"

Write-Host "`n>>> [1/4] Running Laravel Linting (Pint) <<<" -ForegroundColor Cyan
php artisan optimize:clear | Out-Null
& vendor/bin/pint --test
if ($LASTEXITCODE -eq 0) {
    Write-Host "PASS: Laravel coding style is perfect." -ForegroundColor Green
} else {
    Write-Host "FAIL: Laravel coding style issues found. Run 'vendor/bin/pint' to fix." -ForegroundColor Yellow
}

Write-Host "`n>>> [2/4] Running Laravel Unit & Feature Tests <<<" -ForegroundColor Cyan
php artisan test
if ($LASTEXITCODE -eq 0) {
    Write-Host "PASS: All Laravel backend tests passed." -ForegroundColor Green
} else {
    Write-Host "FAIL: Some Laravel backend tests failed." -ForegroundColor Red
}

Write-Host "`n>>> [3/4] Running Flutter Static Analysis <<<" -ForegroundColor Cyan
Set-Location mobile_app
flutter analyze
if ($LASTEXITCODE -eq 0) {
    Write-Host "PASS: Flutter static analysis found no issues." -ForegroundColor Green
} else {
    Write-Host "FAIL: Flutter analysis found warnings/errors." -ForegroundColor Red
}

Write-Host "`n>>> [4/4] Running Flutter Unit & Widget Tests <<<" -ForegroundColor Cyan
flutter test
if ($LASTEXITCODE -eq 0) {
    Write-Host "PASS: All Flutter mobile tests passed." -ForegroundColor Green
} else {
    Write-Host "FAIL: Some Flutter mobile tests failed." -ForegroundColor Red
}

Set-Location ..
Write-Host "`n>>> Quality Check Complete <<<" -ForegroundColor Magenta
