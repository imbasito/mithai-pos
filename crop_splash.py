from PIL import Image
import os

path = r"d:\Projects\POS System\public\assets\images\branding\sinyx-slogan.png"

try:
    img = Image.open(path)
    if img.mode != 'RGBA':
        img = img.convert('RGBA')
    
    # Auto-crop
    bbox = img.getbbox()
    if bbox:
        cropped_img = img.crop(bbox)
        cropped_img.save(path, format='PNG')
        print(f"Successfully cropped {path}")
    else:
        print("Nothing to crop.")

except Exception as e:
    print(f"Error: {e}")
