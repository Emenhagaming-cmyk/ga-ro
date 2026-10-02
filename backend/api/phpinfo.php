<?php
// Minimal-repro diagnostic - HAPUS setelah verifikasi

$info = [
    'php_version'   => PHP_VERSION,
    'php_version_id'=> PHP_VERSION_ID,
    'tmp_writable'  => is_writable('/tmp'),
    'tmp_dir'       => sys_get_temp_dir(),
    'pdo_drivers'   => PDO::getAvailableDrivers(),
    'pdo_mysql_attr_ssl_ca' => defined('PDO::MYSQL_ATTR_SSL_CA') ? PDO::MYSQL_ATTR_SSL_CA : null,
    'env_app_env'   => getenv('APP_ENV'),
    'env_db'        => getenv('DB_CONNECTION'),
    'env_view_path' => getenv('VIEW_COMPILED_PATH'),
    'app_key_set'   => !empty(getenv('APP_KEY')),
];

// Test apakah bisa nulis ke /tmp
$testFile = '/tmp/blade_test_' . time() . '.php';
$info['tmp_write_test'] = file_put_contents($testFile, '<?php // test') !== false;
if ($info['tmp_write_test']) {
    unlink($testFile);
}

// Test apakah autoload bisa di-load
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $info['autoload'] = 'OK';
} catch (Throwable $e) {
    $info['autoload'] = 'FAIL: ' . $e->getMessage();
}

// Test apakah config database bisa di-include
try {
    $db = include __DIR__ . '/../config/database.php';
    $info['config_database'] = 'OK';
} catch (Throwable $e) {
    $info['config_database'] = 'FAIL: ' . $e->getMessage();
}

// Test apakah helpers.php bisa di-include
try {
    // helpers membutuhkan app() — test apakah parse OK saja
    $info['helpers_syntax'] = php_check_syntax(__DIR__ . '/../app/helpers.php') ? 'OK' : 'FAIL';
} catch (Throwable $e) {
    $info['helpers_syntax'] = 'FAIL: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($info, JSON_PRETTY_PRINT);
