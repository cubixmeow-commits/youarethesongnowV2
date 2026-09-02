<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class View
{
    public static function render(string $template, array $data = []): string
    {
        $path = Config::root() . '/templates/' . ltrim($template, '/') . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('Template not found: ' . $template);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string) ob_get_clean();
    }

    public static function page(string $template, array $data = [], string $layout = 'layouts/main'): string
    {
        AssetRelease::sendHtmlNoStoreHeaders();
        $content = self::render($template, $data);
        return self::render($layout, array_merge($data, ['content' => $content]));
    }
}
