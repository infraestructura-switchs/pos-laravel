<?php
$envFile = __DIR__ . '/../.env';
$maintenance = __DIR__.'/../storage/framework/maintenance.php';
$vendor = __DIR__.'/../vendor/autoload.php';
$app = __DIR__.'/../bootstrap/app.php';
 
if (file_exists($envFile)) {
    echo "<h3>.env encontrado ✅</h3><hr>";
    // echo "<pre>" . htmlspecialchars(file_get_contents($envFile)) . "</pre>";
} else {
    echo "<h3>.env NO encontrado ❌</h3><hr>";
}
 
if (file_exists($vendor)) {
    echo "<h3>vendor encontrado ✅</h3><hr>";
    // echo "<pre>" . htmlspecialchars(file_get_contents($vendor)) . "</pre>";
} else {
    echo "<h3>vendor NO encontrado ❌</h3><hr>";
}
 
if (file_exists($maintenance)) {
    echo "<h3>maintenance encontrado ✅</h3><hr>";
    // echo "<pre>" . htmlspecialchars(file_get_contents($maintenance)) . "</pre>";
} else {
    echo "<h3>maintenance NO encontrado ❌</h3><hr>";
}
 
if (file_exists($app)) {
    echo "<h3>app encontrado ✅</h3><hr>";
    // echo "<pre>" . htmlspecialchars(file_get_contents($app)) . "</pre>";
} else {
    echo "<h3>app NO encontrado ❌</h3><hr>";
}
