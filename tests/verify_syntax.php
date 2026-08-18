<?php

$dir = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$errors = [];
$checked = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        if (strpos($path, 'vendor') !== false) {
            continue;
        }

        $output = [];
        $returnVar = 0;
        exec('"C:\\xampp\\php\\php.exe" -l "' . $path . '"', $output, $returnVar);

        if ($returnVar !== 0) {
            $errors[] = implode("\n", $output);
        }
        $checked++;
    }
}

echo "Checked {$checked} PHP files.\n";
if (empty($errors)) {
    echo "SUCCESS: All PHP files have valid syntax!\n";
} else {
    echo "ERRORS:\n" . implode("\n", $errors) . "\n";
}
