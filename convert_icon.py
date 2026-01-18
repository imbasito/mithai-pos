from PIL import Image
import sys
import os

# Input and output paths
input_path = r"C:\Users\bestb\.gemini\antigravity\brain\b883c0c0-69ec-4b33-abe8-f956fbed36a3\mithai_app_icon_v2_1768212613484.png"
output_ico_path = r"d:\Projects\POS System\icon.ico"
output_favicon_path = r"d:\Projects\POS System\public\favicon.ico"
output_logo_dir = r"d:\Projects\POS System\public\assets\images\logo"

try:
    img = Image.open(input_path)
    
    # 1. Update Desktop Icon (icon.ico)
    img.save(output_ico_path, format='ICO', sizes=[(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)])
    print(f"Updated {output_ico_path}")

    # 2. Update Public Favicon (favicon.ico)
    img.save(output_favicon_path, format='ICO', sizes=[(64, 64), (32, 32), (16, 16)])
    print(f"Updated {output_favicon_path}")

    # 3. Create PNG versions for the app to use
    if not os.path.exists(output_logo_dir):
        os.makedirs(output_logo_dir)
    
    img.save(os.path.join(output_logo_dir, "app-logo.png"), format='PNG')
    img.save(os.path.join(output_logo_dir, "favicon.png"), format='PNG')
    print(f"Created app-logo.png and favicon.png in {output_logo_dir}")

except Exception as e:
    print(f"Error processing icons: {e}")
