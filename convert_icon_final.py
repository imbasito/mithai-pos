from PIL import Image
import sys
import os

# New input path (v3 icon)
input_path = r"C:\Users\bestb\.gemini\antigravity\brain\b883c0c0-69ec-4b33-abe8-f956fbed36a3\mithai_app_icon_v3_1768213354497.png"
# New output name to again bust cache and ensure we have unique file
output_ico_path = r"d:\Projects\POS System\pos-icon-final.ico"
build_ico_path = r"d:\Projects\POS System\build\pos-icon-final.ico"
public_favicon_path = r"d:\Projects\POS System\public\favicon.ico"
logo_dir = r"d:\Projects\POS System\public\assets\images\logo"

try:
    img = Image.open(input_path)
    
    # 1. Create unique final icon
    img.save(output_ico_path, format='ICO', sizes=[(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)])
    print(f"Created {output_ico_path}")
    
    # 2. Copy to build
    if not os.path.exists("d:\\Projects\\POS System\\build"):
        os.makedirs("d:\\Projects\\POS System\\build")
    img.save(build_ico_path, format='ICO', sizes=[(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)])
    print(f"Created {build_ico_path}")

    # 3. Update public favicon directly
    img.save(public_favicon_path, format='ICO', sizes=[(64, 64), (32, 32), (16, 16)])
    print(f"Updated {public_favicon_path}")

    # 4. Create PNG versions
    if not os.path.exists(logo_dir):
        os.makedirs(logo_dir)
    img.save(os.path.join(logo_dir, "app-logo.png"), format='PNG')
    img.save(os.path.join(logo_dir, "favicon.png"), format='PNG')
    print(f"Created PNG assets in {logo_dir}")

except Exception as e:
    print(f"Error converting icon: {e}")
