from PIL import Image, ImageOps
import os

# New input path
input_path = r"d:\Projects\POS System\public\assets\images\branding\sinyx-icon-new.png"
# Output paths
output_ico_path = r"d:\Projects\POS System\pos-icon.ico"
public_favicon_path = r"d:\Projects\POS System\public\favicon.ico"
logo_dir = r"d:\Projects\POS System\public\assets\images\logo"
branding_dir = r"d:\Projects\POS System\public\assets\images\branding"

def generate_square_icon(image, size):
    """
    Auto-crops image to remove whitespace, then fits it into a square.
    """
    # 1. Auto-crop transparent boundaries
    # Get the bounding box of the non-zero alpha regions
    bbox = image.getbbox()
    if bbox:
        image = image.crop(bbox)
    
    # 2. Create a square canvas
    square = Image.new('RGBA', (size, size), (255, 255, 255, 0))
    
    # 3. Use ImageOps.fit to make it zoom/fill the square better if desired, 
    # but traditionally icons are centered with minimal padding.
    # User said "crop and remove extra spaces to fit better", 
    # so we will use the full square with a tiny bit of padding (e.g. 5%)
    # to avoid the logo touching the very edge.
    
    padding = int(size * 0.05)
    inner_size = size - (padding * 2)
    
    image.thumbnail((inner_size, inner_size), Image.Resampling.LANCZOS)
    
    # Center it
    offset = ((size - image.size[0]) // 2, (size - image.size[1]) // 2)
    square.paste(image, offset)
    
    return square

try:
    source_img = Image.open(input_path)
    
    if source_img.mode != 'RGBA':
        source_img = source_img.convert('RGBA')

    # 1. Generate multi-resolution ICO
    ico_resolutions = [256, 128, 64, 48, 32, 16]
    ico_images = []
    for res in ico_resolutions:
        ico_images.append(generate_square_icon(source_img.copy(), res))
    
    ico_images[0].save(output_ico_path, format='ICO', append_images=ico_images[1:])
    print(f"Created {output_ico_path} with auto-crop and zoom.")
    
    # 2. Update public favicon
    favicon_img = generate_square_icon(source_img.copy(), 64)
    favicon_img.save(public_favicon_path, format='ICO', sizes=[(64,64), (32,32), (16,16)])
    print(f"Updated {public_favicon_path}")

    # 3. Create PNG versions
    if not os.path.exists(logo_dir):
        os.makedirs(logo_dir)
    app_logo = generate_square_icon(source_img.copy(), 512)
    app_logo.save(os.path.join(logo_dir, "app-logo.png"), format='PNG')
    app_logo.save(os.path.join(logo_dir, "favicon.png"), format='PNG')
    print(f"Created PNG assets in {logo_dir}")

    # 4. Update the sinyx-icon.jpg (keeping same name for style compatibility)
    # Background for JPEG should be white for the 'multiply' effect to work best
    jpg_bg = Image.new('RGB', (512, 512), (255, 255, 255))
    # For JPG, we'll use a slightly smaller padding for a "large" feel
    img_cropped = source_img.copy().crop(source_img.getbbox()) if source_img.getbbox() else source_img
    img_cropped.thumbnail((512, 512), Image.Resampling.LANCZOS)
    offset = ((512 - img_cropped.size[0]) // 2, (512 - img_cropped.size[1]) // 2)
    jpg_bg.paste(img_cropped, offset, img_cropped)
    jpg_bg.save(os.path.join(branding_dir, "sinyx-icon.jpg"), format='JPEG', quality=100)
    print(f"Updated {os.path.join(branding_dir, 'sinyx-icon.jpg')}")

except Exception as e:
    print(f"Error converting icon: {e}")
