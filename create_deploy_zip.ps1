$themeName = "election-awareness"
$zipName = "election-awareness-DEPLOY.zip"
$exclude = @("node_modules", ".git", ".vscode", "bundle_theme.ps1", "verify_bundle.ps1", "create_deploy_zip.ps1", "*.zip", "temp_build", "package-lock.json", ".gitignore", "debug_check", "debug_extract")

Write-Host "--- Creating DEPLOYMENT Zip ---"

# Cleanup
if (Test-Path $zipName) { Remove-Item $zipName -Force }
if (Test-Path "temp_build") { Remove-Item "temp_build" -Recurse -Force }

# Prepare Structure
# IMPORTANT: WordPress needs the folder inside the zip
New-Item -ItemType Directory -Force -Path "temp_build/$themeName" | Out-Null

# Copy Files
$items = Get-ChildItem -Path .
foreach ($item in $items) {
    if ($exclude -contains $item.Name) { 
        continue 
    }
    Copy-Item -Path $item.FullName -Destination "temp_build/$themeName" -Recurse
}

# Zip
$source = Join-Path $PWD "temp_build"
# We zip content of temp_build so valid path is zip -> election-awareness -> style.css
# Compressing "temp_build\election-awareness" to zip will create "election-awareness" folder in zip.
$target = Join-Path $PWD "temp_build\$themeName"
$dest = Join-Path $PWD $zipName

Write-Host "Zipping..."
Compress-Archive -Path $target -DestinationPath $dest

if (Test-Path $dest) {
    $size = (Get-Item $dest).Length / 1KB
    Write-Host "SUCCESS: Created $zipName ($($size.ToString('N2')) KB)"
}
else {
    Write-Error "FAILED to create zip"
}

# Cleanup
Remove-Item "temp_build" -Recurse -Force
