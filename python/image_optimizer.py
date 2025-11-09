import argparse
import os
from PIL import Image

def compress_resize_webp(input_path, output_path, max_size_kb, width=None, height=None, watermark=None):
    os.makedirs(os.path.dirname(output_path), exist_ok=True)

    img = Image.open(input_path).convert("RGBA")

    # Resize if dimensions given
    if width and height:
        img = img.resize((width, height), Image.Resampling.LANCZOS)

    # Add watermark if provided
    if watermark != 'null' and os.path.exists(watermark):
        logo = Image.open(watermark).convert("RGBA")
        logo_ratio = 0.2
        logo = logo.resize((int(img.width * logo_ratio), int(img.height * logo_ratio)))
        img.alpha_composite(logo, (10, 10))

    # Start with high quality and reduce if needed
    quality = 85
    img.save(output_path, "WEBP", quality=quality, optimize=True)

    while os.path.getsize(output_path) / 1024 > max_size_kb and quality > 10:
        quality -= 5
        img.save(output_path, "WEBP", quality=quality, optimize=True)

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
