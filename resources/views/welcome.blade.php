<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
<div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
    <form method="POST" action="{{ route('export') }}">
        @csrf

        <div>
            <label>
                Enter Personal Access Token
                <input type="text" name="access_token" id="access_token"/>
            </label>
        </div>
        <div>
            <label>
                Select a File Extension
                <select name="file_extension" id="file_extension">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                </select>
            </label>
        </div>
        <div>
            <button type="submit">Export Transactions</button>
        </div>
    </form>
</div>
</body>
</html>
