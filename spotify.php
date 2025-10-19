<?php
    // --- FORCE ERROR REPORTING ---
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // -----------------------------

    // Allow the page to be framed by the main simulator page
    header("Content-Security-Policy: frame-ancestors 'self'");
    header("Content-Type: text/html; charset=utf-8");

    echo "<h1>Hibakeresés aktív</h1>";
    echo "<p>Ha ezt látod, a PHP fut. A hiba valószínűleg a fájlok (CSS, JS, JSON) elérési útvonalában van.</p>";

    // Let's test a file path
    $playlists_path = 'data/playlists.json';
    echo "<p>Keresem a lejátszási listákat itt: <code>" . realpath('.') . "/" . $playlists_path . "</code></p>";
    if (file_exists($playlists_path)) {
        echo "<p style='color:green;'>A 'data/playlists.json' fájl megtalálva!</p>";
    } else {
        echo "<p style='color:red;'>HIBA: A 'data/playlists.json' fájl NEM található!</p>";
    }
?>