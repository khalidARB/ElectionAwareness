$themeName = "election-awareness"
$version = "1.2.2"
$zipName = "$themeName-$version.zip"
$exclude = @("node_modules", ".git", ".vscode", "repackage.ps1", "create_deploy_zip.ps1", "verify_bundle.ps1", "*.zip", "temp_build")

Write-Host "--- Repackaging Theme for WordPress ($version) ---"

# Cleanup
if (Test-Path $zipName) { Remove-Item $zipName -Force }
if (Test-Path "temp_build") { Remove-Item "temp_build" -Recurse -Force }

# Create temporary directory structure
New-Item -ItemType Directory -Force -Path "temp_build/$themeName" | Out-Null

# Copy only necessary files to the theme folder
Get-ChildItem -Path . | ForEach-Object {
    if ($exclude -notcontains $_.Name) {
        Copy-Item -Path $_.FullName -Destination "temp_build/$themeName" -Recurse -Force
    }
}

# Zip the theme folder itself, so it's the root item in the zip
# We use the parent folder as the source to ensure the theme folder is included
Compress-Archive -Path "temp_build/$themeName" -DestinationPath $zipName

# Final Cleanup
Remove-Item "temp_build" -Recurse -Force

if (Test-Path $zipName) {
    $size = (Get-Item $zipName).Length / 1KB
    Write-Host "SUCCESS: Created $zipName ($($size.ToString('N2')) KB)"
    Write-Host "Structure: $zipName -> $themeName -> style.css"
}
else {
    Write-Error "FAILED to create zip"
}
