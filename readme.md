# Larapress

**Larapress** is a PHP framework built to create **WordPress plugins** with a development experience similar to **Laravel**.

The goal is to simplify plugin development by providing an organized structure, reusable components, and Laravel-inspired conventions.

---

## ✨ Features

- Organized folder structure (App, Resources, Components, Config, etc.)
- Composer autoload with PSR-4
- **Resources** system similar to Laravel Controllers
- Reusable **Forms and Tables** for the WordPress admin
- Contracts and Traits to standardize resources
- Integration with WordPress Hooks and Actions
- `please` command to install, generate, and manage resources

---

## 📦 Getting Started

1. **Clone the repository**

```bash
git clone https://github.com/estou-ai/larapress-framework my-plugin
cd my-plugin
```

2. **Start Docker**

```bash
docker compose up -d
```

This will launch a WordPress environment with Larapress.

3. **Configure your plugin**

Edit the file `config/app.php` with your plugin details:

```php
return [
    'slug' => 'my-plugin',
    'name' => 'My Plugin',
    'description' => 'A plugin built with the Larapress framework',
    'version' => '1.0.0',
    'author' => 'Your Name',
    'author_uri' => 'https://yoursite.com'
];
```

4. **Install the plugin**

Access the WordPress container and run:

```bash
php please.php install
```

This will:

- Create a symlink inside the WordPress `plugins` folder pointing to your framework.
- Generate the main plugin file automatically.

---

## ⚙️ Usage

### Create a Resource

```bash
php please.php createResource Customers
```

Example Resource (`App/Resources/Customers.php`):

```php
class Customers extends BaseResource implements ResourceContract, HasTableContract
{
    use HasTable;

    public function form(Form $form): Form
    {
        return $form->schema([
            Input::make('name')->setLabel('Name')->setType('text')
        ]);
    }
}
```

---

## 🚀 Roadmap

- Authentication and authorization based on WordPress capabilities
- More pre-built components (Cards, Charts, etc.)
- Simple migration system
- Integration with external APIs

---

## 🤝 Contributing

Want to help improve Larapress? Fork the repository, create a branch, and open a Pull Request.

---

## 📄 License

This project is licensed under the **MIT** license.

