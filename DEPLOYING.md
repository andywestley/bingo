# Deployment Guide

## Requirements
- PHP 7.4+
- Apache Web Server (with `mod_rewrite` if using .htaccess, though simple usage just needs `DirectoryIndex`)
- SQLite3 extension for PHP

## Installation
1.  **Copy Files**: Upload the entire `bingo` directory to your web server.
2.  **Permissions**: The application uses a SQLite database in the `data/` directory.
    - The `data/` directory **MUST** be writable by the web server user (e.g., `www-data`).
    - The web server needs to create and write to `data/bingo.sqlite`.
    - **Command**: `chmod -R 775 data/` (or `777` if necessary).
    - **Ownership**: `chown -R www-data:www-data data/` (recommended).

## Configuration
- **Default Page**: An `.htaccess` file is included to set `index.php` as the default page. Ensure `AllowOverride Indexes` (or `All`) is enabled in your Apache config.

## Troubleshooting
- **Database connection failed**: Check permissions on the `data/` folder.
- **ReferenceError**: If you see JS errors after an update, clear your browser cache. The application now uses cache-busting version parameters to mitigate this.
