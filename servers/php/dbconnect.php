<?php

const DB_MYSQL = 0;
const DB_POSTGRESQL = 1;
const DB_SQLITE3 = 2;

$dbType = DB_POSTGRESQL;

$params = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    throw new Exception('DATABASE_URL is not configured');
}

$url = parse_url($databaseUrl);

$host = $url['host'];
$port = $url['port'] ?? 5432;
$dbname = ltrim($url['path'], '/');
$user = urldecode($url['user']);
$password = urldecode($url['pass']);

/*
 * Force PostgreSQL to use encrypted connection
 * without looking for ~/.postgresql/root.crt.
 */
putenv('PGSSLMODE=require');
putenv('PGSSLROOTCERT=');

/*
 * Neon endpoint ID for SNI.
 */
$endpoint = explode('.', $host)[0];

/*
 * PostgreSQL connection to Neon.
 */
$dsn = "pgsql:"
     . "host={$host};"
     . "port={$port};"
     . "dbname={$dbname};"
     . "sslmode=require;"
     . "options='endpoint={$endpoint}'";

$pdo = new PDO(
    $dsn,
    $user,
    $password,
    $params
);

$sqlFunctionCallMethod = 'select ';

?>
