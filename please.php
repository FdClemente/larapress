<?php

class Please
{
    private string $basePath;
    private string $wpPath;

    public function __construct()
    {
        $this->basePath = '/var/www/html/framework';
        $this->wpPath   = $this->basePath . '/wp';
    }

    public function run($command){
        $commandMethod = 'run' . ucfirst($command) . 'Command';

        if (method_exists($this, $commandMethod)){
            $this->$commandMethod();
        }else{
            $this->error("Command not found: {$command}");
            exit(-100);
        }
    }

    public function runInstallCommand(): void
    {
        $config = $this->loadConfig();
        $slug = $config['slug'];

        $source = $this->basePath;
        $target = $this->wpPath . '/wp-content/plugins/' . $slug;


        if (!is_dir(__DIR__ . '/wp/wp-content')) {
            $this->error('Please start docker using: docker-compose up -d');
            exit(-100);
        }

        if (is_link($target) || is_dir($target)) {
            $this->error("Plugin '{$slug}' already installed at {$target}");
            exit(-100);
        }

        $this->info("Installing plugin '{$slug}'...");

        if (!$this->createSymlink($source, $target)) {
            $this->error("Failed to create symlink {$target} → {$source}");
            exit(-100);
        }

        $this->publishStub(__DIR__.'/', $config);

        $this->runComposerInstallCommand();

        $this->info("Plugin '{$slug}' installed successfully!");
    }

    private function loadConfig(): array
    {
        if (!function_exists('plugin_dir_url')) {
            function plugin_dir_url(...$var) { return ''; }
        }

        $configs = require __DIR__.'/config/app.php';
        return $configs;
    }

    private function info(string $msg): void {
        echo "\033[32m$msg\033[0m\n";
    }

    private function error(string $msg): void {
        echo "\033[31m$msg\033[0m\n";
    }

    private function createSymlink(string $source, string $target): bool
    {
        $container = 'wordpress';

        $this->info("Creating symlink inside Docker: {$target} -> {$source}");

        $this->runInDocker($container, "mkdir -p " . escapeshellarg(dirname($target)));

        $cmd = "ln -s " . escapeshellarg($source) . " " . escapeshellarg($target);
        $this->runInDocker($container, $cmd);

        $check = $this->runInDocker($container, "[ -L " . escapeshellarg($target) . " ] && echo '1' || echo '0'");
        return trim($check) === '1';
    }

    private function publishStub(string $target, array $config): void
    {
        $stubFile = __DIR__ . '/App/Core/stubs/plugin_name.stub';
        $destFile = $target . '/' . $config['slug'] . '.php';

        $content = str_replace(
            ['{{PluginName}}', '{{PluginDescription}}', '{{PluginVersion}}', '{{PluginAuthor}}', '{{PluginAuthorURI}}'],
            [
                $config['name'] ?? $config['slug'],
                $config['description'] ?? '',
                $config['version'] ?? '1.0.0',
                $config['author'] ?? '',
                $config['author_uri'] ?? ''
            ],
            file_get_contents($stubFile)
        );

        file_put_contents($destFile, $content);
    }

    public function runInDocker(string $service, string $cmd): string
    {
        $container = trim(shell_exec("docker compose ps -q {$service}"));
        if (!$container) {
            $this->error("Service {$service} not running");
            exit(-100);
        }

        $output = shell_exec("docker exec -u www-data {$container} sh -c " . escapeshellarg($cmd));

        return $output ?? '';
    }

    public function runComposerInstallCommand(): void
    {
        if (!file_exists(__DIR__.'/composer.phar')) {
            file_put_contents(__DIR__.'/composer.phar', file_get_contents('https://getcomposer.org/composer-stable.phar'));
        }

        $container = 'wordpress';
        $composerPath = '/var/www/html/framework/composer.phar';
        $frameworkDir = '/var/www/html/framework';

        $this->info("Running composer install inside Docker in {$frameworkDir}...");

        $output = $this->runInDocker(
            $container,
            "cd " . escapeshellarg($frameworkDir) . " && php " . escapeshellarg($composerPath) . " install"
        );

        echo $output;

        $this->info("Composer install completed successfully");
    }
}

$please = new Please();

$command = $argv[1] ?? null;
if (!$command) {
    echo "Usage: php please.php <command>\n";
    exit(-1);
}

$please->run($command);
