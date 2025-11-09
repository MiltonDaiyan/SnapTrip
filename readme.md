# 📸 SnapTrip

> **Smart Laravel Image Resizer, Compressor & Watermarker — powered by Python.**

SnapTrip is a Laravel package that seamlessly integrates a powerful Python-based image optimizer into your PHP workflow.  
It can **resize**, **compress** to a target file size (default `400 KB`), and optionally **apply a watermark** to your images — all in one call.  
All optimized images are automatically converted into **WebP** format for better performance and reduced file size.

---

## 🚀 Features

- 🔧 Resize images by **width** and **height**
- 📦 Compress images to a **target size (default 400 KB)**
- 🌐 Converts all outputs to **WebP format**
- 💧 Optional watermark overlay (supports logo path from DB or file)
- ⚡ Simple static facade for quick use
- 🧠 Python-powered (using Pillow library for accurate results)
- 🧩 Fully configurable & reusable across projects

---

## 📦 Installation

### 1️⃣ Install the package via Composer

```bash
composer require daiyanmozumder/snaptrip


2️⃣ Install Python dependencies

This package uses a Python script to handle image optimization.

You need to have Python 3.8+ and Pillow installed.

✅ Check if Python is installed:
python --version


If not, download and install Python
.

✅ Install required Python libraries:
pip install pillow


(Optionally, for advanced image processing)

pip install numpy

3️⃣ (Optional) Publish the config file
php artisan vendor:publish --tag=snaptrip-config


This creates config/snaptrip.php, where you can set default compression and paths.

⚙️ Configuration

config/snaptrip.php

return [
'default_target_kb' => 400, // default compression size in KB
'default_output_folder' => 'uploads/optimized', // default output path
];

🧠 Usage
✅ Example 1: Basic compression
use SnapTrip;

SnapTrip::optimize(
filePath: 'uploads/originals/image.jpg'
);


This will compress the image to 400 KB, save it as .webp, and return the optimized file path.

✅ Example 2: Custom target size
SnapTrip::optimize(
filePath: 'uploads/originals/image.png',
targetKb: 250
);


Compresses and converts to .webp at roughly 250 KB.

✅ Example 3: Resize + compress + watermark
SnapTrip::optimize(
filePath: 'uploads/originals/image.jpg',
targetKb: 350,
width: 800,
height: 600,
outputFolder: 'uploads/products',
watermarkLogo: 'storage/logos/company_logo.png'
);


This will:

Resize the image to 800x600

Compress to ~350 KB

Add watermark logo from the given path

Save result in uploads/products folder

Return .webp file path

✅ Example 4: From a controller
use App\Http\Controllers\Controller;
use SnapTrip;

class ProductController extends Controller
{
public function upload(Request $request)
{
$file = $request->file('image');
$path = $file->store('uploads/originals');

        $optimizedPath = SnapTrip::optimize(
            filePath: $path,
            targetKb: 400,
            watermarkLogo: auth()->user()->company_logo // or null
        );

        return response()->json([
            'original' => $path,
            'optimized' => $optimizedPath,
        ]);
    }
}

🧩 How It Works

Laravel calls SnapTripService from the facade.

The service triggers a Python script (image_optimizer.py) via PythonRunner.

The Python script:

Opens the image using Pillow.

Resizes it if width/height are provided.

Compresses until the target file size (KB) is reached.

Adds watermark if logo path is provided.

Converts the result to .webp format.

Returns the optimized image path back to Laravel.

🧪 Testing Environment

You can test the environment and ensure Python is available using:

php artisan snaptrip:check-environment


Expected output:

✅ Python found: Python 3.11.4
✅ Pillow installed

💡 Error Handling

SnapTrip gracefully handles:

Error	Description
Python not installed	Python is missing from system PATH
Pillow not installed	Missing dependency — install via pip install pillow
Invalid image path	Provided image does not exist or is unreadable
Permission denied	Storage folder not writable

You’ll get descriptive exceptions for each case.

🧰 Example Output
Input	Output
uploads/originals/product.jpg	uploads/optimized/product_optimized.webp
🔍 Troubleshooting
❌ Python not recognized

Make sure Python is added to your system PATH.
Try closing and reopening your terminal after installation.

❌ Permission denied

Ensure your Laravel storage and uploads directories are writable.

chmod -R 775 storage uploads

🧩 Roadmap

Multi-threaded batch compression

Custom watermark position & opacity

Integration with Laravel Queues

Support for S3 or remote storage optimization

🧑‍💻 Author

Daiyan Mozumder

📜 License

This package is open-sourced software licensed under the MIT license
.

💬 Contributing

Pull requests are welcome!
If you’d like to improve SnapTrip, please fork the repository and submit a PR.

🏁 Example Folder Structure (after install)
laravel_project/
├── vendor/
│   └── daiyanmozumder/
│       └── snaptrip/
│           ├── src/
│           ├── python/
│           │   └── image_optimizer.py
│           └── config/snaptrip.php
├── config/
│   └── snaptrip.php
└── storage/
└── app/uploads/optimized/


💡 Tip: SnapTrip is designed for effortless plug-and-play image optimization with minimal setup — perfect for high-performance Laravel apps that demand small, sharp, and optimized images in WebP format.


---

Would you like me to now generate the **actual `image_optimizer.py` file** and the **corresponding Laravel PHP service 