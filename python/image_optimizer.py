import argparse
import os
from PIL import Image

def compress_resize_webp(input_path, output_path, max_size_kb, width=None, height=None, watermark=None):
    img = Image.open(input_path).convert("RGBA")

    # Resize if dimensions given
    if width and height:
        img = img.resize((width, height), Image.Resampling.LANCZOS)

    # Add watermark if provided
    if watermark != 'null' and os.path.exists(watermark):
        logo = Image.open(watermark).convert("RGBA")
        logo_ratio = 0.2
        logo = logo.resize((int(img.width * logo_ratio), int(img.height * logo_ratio)))
        img.paste(logo, (10, 10), logo)

    # Save initially as WebP
    img.save(output_path, "WEBP", quality=85, optimize=True)

    # Iteratively reduce size if above target KB
    while os.path.getsize(output_path) / 1024 > max_size_kb and max_size_kb > 50:
        img.save(output_path, "WEBP", quality=70, optimize=True)
        if os.path.getsize(output_path) / 1024 <= max_size_kb:
            break
        max_size_kb -= 50

    print(f'{{"status": "success", "output": "{output_path}"}}')

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--output", required=True)
    parser.add_argument("--max_size_kb", type=int, default=400)
    parser.add_argument("--width", type=int, default=None)
    parser.add_argument("--height", type=int, default=None)
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
