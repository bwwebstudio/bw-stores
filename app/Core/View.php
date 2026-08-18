<?php

namespace App\Core;

/**
 * View
 * 
 * Simple template engine with layout support.
 * Supports sections, yields, includes, and auto-escaping.
 */
class View
{
    private static ?string $layoutFile = null;
    private static array $sections = [];
    private static ?string $currentSection = null;

    /**
     * Render a view with data.
     * 
     * @param string $view  View path using dot notation (e.g., 'auth.login')
     * @param array  $data  Variables to make available in the view
     */
    public static function render(string $view, array $data = []): void
    {
        $viewPath = self::resolveViewPath($view);

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View '{$view}' not found at: {$viewPath}");
        }

        // Reset layout state
        self::$layoutFile = null;
        self::$sections = [];
        self::$currentSection = null;

        // Extract data variables into view scope
        extract($data, EXTR_SKIP);

        // Capture view content
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // If a layout was specified, render it with sections
        if (self::$layoutFile !== null) {
            // Store the main content as the 'content' section if not already set
            if (!isset(self::$sections['content'])) {
                self::$sections['content'] = $content;
            }

            $layoutPath = self::resolveViewPath(self::$layoutFile);
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout '" . self::$layoutFile . "' not found at: {$layoutPath}");
            }

            // Re-extract data for layout scope
            extract($data, EXTR_SKIP);
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Set the layout for the current view.
     * Call this from within a view file.
     */
    public static function layout(string $layout): void
    {
        self::$layoutFile = $layout;
    }

    /**
     * Start a named section.
     * Call this from within a view file.
     */
    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    /**
     * End the current section.
     */
    public static function endSection(): void
    {
        if (self::$currentSection === null) {
            throw new \RuntimeException("No section is currently open.");
        }

        self::$sections[self::$currentSection] = ob_get_clean();
        self::$currentSection = null;
    }

    /**
     * Output a section's content in a layout.
     * If the section doesn't exist, output the default.
     */
    public static function yield(string $name, string $default = ''): void
    {
        echo self::$sections[$name] ?? $default;
    }

    /**
     * Include a partial view (component).
     */
    public static function include(string $view, array $data = []): void
    {
        $viewPath = self::resolveViewPath($view);

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Partial view '{$view}' not found at: {$viewPath}");
        }

        extract($data, EXTR_SKIP);
        include $viewPath;
    }

    /**
     * Check if a section has content.
     */
    public static function hasSection(string $name): bool
    {
        return isset(self::$sections[$name]) && self::$sections[$name] !== '';
    }

    /**
     * Convert dot-notation view path to file path.
     * Example: 'auth.login' → '/resources/views/auth/login.php'
     */
    private static function resolveViewPath(string $view): string
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $view);
        return BASE_PATH . '/resources/views/' . $path . '.php';
    }
}
