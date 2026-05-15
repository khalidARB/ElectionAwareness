import os
import zipfile
import shutil

THEME_NAME = 'election-awareness'
ZIP_NAME = 'election-awareness-DEPLOY.zip'
EXCLUDES = [
    'node_modules', '.git', '.vscode', '.gitignore',
    'package-lock.json', 'bundle_theme.ps1', 
    'create_deploy_zip.ps1', 'verify_bundle.ps1',
    'repackage.ps1', 'debug_check', 'debug_extract',
    'temp_build', '.DS_Store', 'Thumbs.db', '__pycache__',
    ZIP_NAME, 'election-awareness.zip', 'build_deploy.py'
]

def should_exclude(path, is_dir=False):
    basename = os.path.basename(path)
    if basename in EXCLUDES:
        return True
    # Check if any parent dir is excluded
    parts = path.split(os.sep)
    for part in parts:
        if part in EXCLUDES:
            return True
    return False

def create_zip():
    # Remove existing zip
    if os.path.exists(ZIP_NAME):
        os.remove(ZIP_NAME)
        print(f"Removed existing {ZIP_NAME}")

    print(f"Creating {ZIP_NAME}...")
    
    with zipfile.ZipFile(ZIP_NAME, 'w', zipfile.ZIP_DEFLATED) as zipf:
        # Walk the current directory
        for root, dirs, files in os.walk('.'):
            # Modify dirs in-place to skip excluded directories during traversal
            dirs[:] = [d for d in dirs if d not in EXCLUDES]
            
            for file in files:
                file_path = os.path.join(root, file)
                
                # Double check excludes
                if should_exclude(file_path):
                    continue
                
                # Calculate arcname (path inside zip)
                # We want: election-awareness/style.css
                rel_path = os.path.relpath(file_path, '.').replace(os.sep, '/')
                arcname = f"{THEME_NAME}/{rel_path}"
                
                print(f"Adding: {arcname}")
                zipf.write(file_path, arcname)
    
    print(f"Successfully created {ZIP_NAME}")

    # Verify
    print("\nVerifying Zip Structure:")
    with zipfile.ZipFile(ZIP_NAME, 'r') as zipf:
        names = zipf.namelist()
        if f"{THEME_NAME}/style.css" in names:
            print(f"CONFIRMED: {THEME_NAME}/style.css exists.")
        else:
            print(f"ERROR: {THEME_NAME}/style.css NOT found!")
            for n in names[:10]:
                print(f" - {n}")

if __name__ == "__main__":
    create_zip()
