<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data);
        $content = self::getContent($view, $data);

        if ($layout) {
            $layoutFile = ROOT . "/views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }
        echo $content;
    }

    public static function getContent(string $view, array $data = []): string
    {
        extract($data);
        $file = ROOT . "/views/{$view}.php";
        if (!file_exists($file)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }
        ob_start();
        require $file;
        return ob_get_clean();
    }

    public static function partial(string $partial, array $data = []): void
    {
        extract($data);
        $file = ROOT . "/views/partials/{$partial}.php";
        if (file_exists($file)) require $file;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
