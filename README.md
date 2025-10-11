# AllWooAddons Plugin

A modular WordPress plugin boilerplate built with **OOP**, **PSR-4 autoloading**, and **modern WordPress standards**.  
This boilerplate helps you organize plugin code into self-contained classes (Blocks, Admin, Frontend, etc.), each with a `register()` method for clean initialization.

---

## 📂 Folder Structure

all-woo-addons/
│
├── includes/
│ ├── Blocks/
│ │ └── Blocks.php
│ ├── Admin/
│ │ └── Settings.php
│ ├── Frontend/
│ │ └── Shortcodes.php
│ └── ...
│
├── src/blocks/
│ └── hello-world/ # Example Gutenberg block
│
├── vendor/ # Composer dependencies
│
├── Plugin.php # Main plugin bootstrap
├── composer.json
├── README.md
└── all-woo-addons.php # Plugin entrypoint (loader)

---

## 🚀 Features

- **PSR-4 Autoloading** via Composer  
- **Singleton pattern** for main plugin bootstrap  
- **Self-contained classes** (`Blocks`, `Settings`, `Shortcodes`, etc.)  
- **Automatic class registration** – all classes with `register()` are loaded in `Plugin.php`  
- Example Gutenberg block included (`hello-world`)  

---

## 🔧 Installation

1. Clone the repository into your WordPress `plugins` folder:

   ```bash
   cd wp-content/plugins
   git clone https://github.com/marufmks/all-woo-addons.git
Install dependencies via Composer:

bash
Copy code
cd all-woo-addons
composer install
Activate the plugin from WordPress Admin > Plugins.

🛠 Development
Add new classes under src/YourNamespace/

Each class must include a public static function register() method

The Plugin.php bootstrap automatically calls Class::register()

Example:

php
Copy code
namespace AllWooAddons\Frontend;

class Shortcodes {
    public static function register() {
        add_shortcode('all_woo_addons_demo', [self::class, 'render']);
    }

    public static function render() {
        return '<p>Hello from shortcode!</p>';
    }
}
📦 Build Blocks
If you're building custom Gutenberg blocks:

Add them inside src/blocks/{block-name}

Register in your Blocks.php class:

php
Copy code
register_block_type(__DIR__ . '/../../src/blocks/hello-world');
Run your block build process (Webpack/Vite/WordPress Scripts).

🤝 Contributing
PRs and suggestions are welcome! Please follow WordPress coding standards and PSR-4 conventions.

📜 License
This project is licensed under the GPL-2.0-or-later license.
