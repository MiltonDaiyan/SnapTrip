import argparse
import os
from PIL import Image

def compress_resize_webp(input_path, output_path, max_size_kb, width=None, height=None, watermark=None):
    # Ensure output folder exists
    os.makedirs(os.path.dirname(output_path), exist_ok=True)

    # Convert 'null' strings to None
    width = None if width == 'null' else int(width) if width else None
    height = None if height == 'null' else int(height) if height else None
    watermark = None if watermark == 'null' else watermark

    print(f"Input: {input_path}")
    print(f"Output: {output_path}")
    print(f"Width: {width}, Height: {height}, Watermark: {watermark}")

    # Open original image
    try:
        img = Image.open(input_path).convert("RGBA")
    except Exception as e:
        print(f"Error opening input image: {e}")
        return

    # Resize if dimensions given
    if width and height:
        img = img.resize((width, height), Image.Resampling.LANCZOS)

    # Add watermark if provided
    # Add watermark if provided
    if watermark and os.path.exists(watermark):
        try:
            logo = Image.open(watermark).convert("RGBA")

            # Resize watermark to 15% of image width
            logo_width = int(img.width * 0.15)
            logo_ratio = logo_width / logo.width
            logo_height = int(logo.height * logo_ratio)
            logo = logo.resize((logo_width, logo_height), Image.Resampling.LANCZOS)

            # Apply opacity 60%
            alpha = logo.split()[3]
            alpha = alpha.point(lambda p: int(p * 0.6))
            logo.putalpha(alpha)

            # Position watermark at bottom-right with 10px padding
            position = (img.width - logo.width - 10, img.height - logo.height - 10)

            # Composite watermark onto image
            img.alpha_composite(logo, position)
        except Exception as e:
            print(f"Error applying watermark: {e}")


    # Save initially as WebP
    quality = 85
    try:
        img.save(output_path, "WEBP", quality=quality, optimize=True)
    except Exception as e:
        print(f"Error saving initial WebP: {e}")
        return

    # Iteratively reduce quality if file too large
    while os.path.getsize(output_path) / 1024 > max_size_kb and quality > 10:
        quality -= 5
        try:
            img.save(output_path, "WEBP", quality=quality, optimize=True)
        except Exception as e:
            print(f"Error compressing WebP: {e}")
            break

    print(f'{{"status": "success", "output": "{output_path}"}}')

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--output", required=True)
    parser.add_argument("--max_size_kb", type=int, default=400)
    parser.add_argument("--width", type=str, default='null')
    parser.add_argument("--height", type=str, default='null')
    parser.add_argument("--watermark", type=str, default='null')
    args = parser.parse_args()

    compress_resize_webp(
        input_path=args.input,
        output_path=args.output,
        max_size_kb=args.max_size_kb,
        width=args.width,
        height=args.height,
        watermark=args.watermark
    )
