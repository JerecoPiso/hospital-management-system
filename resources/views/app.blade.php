<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital Management System</title>
    <link rel="icon" type="image/svg+xml" href="/hospital-management-system/favicon.svg">
    <link rel="alternate icon" href="/hospital-management-system/favicon.ico">
    @vite('resources/css/app.css')
</head>

<body>
    <div id="app"></div>

    @vite('resources/js/app.ts')
</body>

</html>
