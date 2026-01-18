from PIL import Image
import sys
import os

# New input path (v2 icon)
input_path = r"C:\Users\bestb\.gemini\antigravity\brain\b883c0c0-69ec-4b33-abe8-f956fbed36a3\mithai_app_icon_v2_1768212613484.png"
# New output name to bust cache and avoid lock issues
output_ico_path = r"d:\Projects\POS System\pos-icon.ico"
build_ico_path = r"d:\Projects\POS System\build\pos-icon.ico"

try:
    img = Image.open(input_path)
    
    # Create the main icon with high quality
    # We will prioritize 256x256 for the main view
    img.save(output_ico_path, format='ICO', sizes=[(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)])
    print(f"Created {output_ico_path}")
    
    # Also copy to build folder
    if not os.path.exists("d:\\Projects\\POS System\\build"):
        os.makedirs("d:\\Projects\\POS System\\build")
        
    img.save(build_ico_path, format='ICO', sizes=[(256, 256), (128, 128), (64, 64), (48, 48), (32, 32), (16, 16)])
    print(f"Created {build_ico_path}")

except Exception as e:
    print(f"Error converting icon: {e}")
