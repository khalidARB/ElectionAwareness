$themeName = "election-awareness"
$exclude = @("node_modules", ".git", ".vscode", "bundle_theme.ps1", "*.zip", "temp_build", "package-lock.json", ".gitignore")

Write-Host "Preparing to bundle $themeName..."

# Clean previous build
if (Test-Path "temp_build") { Remove-Item "temp_build" -Recurse -Force }
if (Test-Path "$themeName.zip") { Remove-Item "$themeName.zip" -Force }

# Create temp structure
New-Item -ItemType Directory -Force -Path "temp_build/$themeName" | Out-Null

# Copy files
$items = Get-ChildItem -Path .
foreach ($item in $items) {
    if ($exclude -contains $item.Name) { 
        Write-Host "Skipping $($item.Name)"
        continue 
    }
    
    Write-Host "Copying $($item.Name)..."
    Copy-Item -Path $item.FullName -Destination "temp_build/$themeName" -Recurse
}

# Zip it
Write-Host "Zipping to $themeName.zip..."
Compress-Archive -Path "temp_build/$themeName" -DestinationPath "$themeName.zip"

# Cleanup
Remove-Item "temp_build" -Recurse -Force

Write-Host "Done! Created $themeName.zip"
