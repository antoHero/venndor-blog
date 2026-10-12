<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Social/link-preview tags: first in <head> so crawlers reach them early --}}
        @isset($meta)
            <meta property="og:type" content="article">
            <meta property="og:site_name" content="The Venndor Blog">
            <meta property="og:url" content="{{ $meta['url'] }}">
            <meta property="og:title" content="{{ $meta['title'] }}">
            <meta property="og:description" content="{{ $meta['description'] ?? '' }}">
            @if (! empty($meta['image']))
                <meta property="og:image" content="{{ $meta['image'] }}">
                <meta property="og:image:alt" content="{{ $meta['title'] }}">
                <meta name="twitter:card" content="summary_large_image">
                <meta name="twitter:image" content="{{ $meta['image'] }}">
            @else
                <meta name="twitter:card" content="summary">
            @endif
            <meta name="twitter:title" content="{{ $meta['title'] }}">
            <meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
        @else
            {{-- Fallback for pages that don't share $meta (home, blog list, etc.) --}}
            <meta property="og:type" content="website">
            <meta property="og:site_name" content="The Venndor Blog">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:title" content="{{ config('app.name', 'The Venndor Blog') }}">
            <meta name="twitter:card" content="summary">
        @endisset

        <link rel="icon" href="/assets/img/favicon/favicon.ico" sizes="any">
        <link rel="icon" href="/assets/img/favicon/favicon-32x32.png" type="image/png" sizes="32x32">
        <link rel="icon" href="/assets/img/favicon/favicon-16x16.png" type="image/png" sizes="16x16">
        <link rel="apple-touch-icon" href="/assets/img/favicon/apple-touch-icon.png">
        <link rel="manifest" href="/assets/img/favicon/site.webmanifest">

        @fonts

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
