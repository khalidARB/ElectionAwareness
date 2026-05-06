$themeName = "election-awareness"
$zipName = "election-awareness-fixed.zip"
$exclude = @("node_modules", ".git", ".vscode", "bundle_theme.ps1", "verify_bundle.ps1", "*.zip", "temp_build", "package-lock.json", ".gitignore", "debug_check")

Write-Host "--- Starting Robust Bundle Verification ---"

# 1. Cleanup
if (Test-Path $zipName) { Remove-Item $zipName -Force }
if (Test-Path "temp_build") { Remove-Item "temp_build" -Recurse -Force }
if (Test-Path "debug_extract") { Remove-Item "debug_extract" -Recurse -Force }

# 2. Prepare Temp Structure
New-Item -ItemType Directory -Force -Path "temp_build/$themeName" | Out-Null

# 3. Copy Files
$items = Get-ChildItem -Path .
foreach ($item in $items) {
    if ($exclude -contains $item.Name) { 
        continue 
    }
    Copy-Item -Path $item.FullName -Destination "temp_build/$themeName" -Recurse
}

# 4. Create Zip
Write-Host "Creating $zipName..."
# Use absolute paths to be safe
$sourcePath = Join-Path $PWD "temp_build\$themeName"
$destPath = Join-Path $PWD $zipName

Compress-Archive -Path $sourcePath -DestinationPath $destPath

# 5. Verify Existence
if (-not (Test-Path $destPath)) {
    Write-Error "CRITICAL: Zip file was NOT created."
    exit 1
}
else {
    $size = (Get-Item $destPath).Length / 1KB
    Write-Host "Success: Zip created. Size: $($size.ToString('N2')) KB"
}

# 6. Verify Internal Structure (The Deep Debug)
Write-Host "Verifying internal structure..."
Expand-Archive -Path $destPath -DestinationPath "debug_extract" -Force

$stylePath = "debug_extract/$themeName/style.css"
if (Test-Path $stylePath) {
    Write-Host "VALIDATION PASSED: style.css found at expected path: $themeName/style.css"
    Get-Content $stylePath -TotalCount 5 | Out-String | Write-Host
}
else {
    Write-Error "VALIDATION FAILED: style.css NOT found at $stylePath"
    Write-Host "Listing contents of zip root:"
    Get-ChildItem -Recurse "debug_extract" | Select-Object FullName
}

# Cleanup Temp
Remove-Item "temp_build" -Recurse -Force
Remove-Item "debug_extract" -Recurse -Force
