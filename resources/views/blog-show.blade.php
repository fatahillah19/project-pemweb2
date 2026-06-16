<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $blog->title }} - Blog SMA NURUL FIKRI</title>

    <link rel="stylesheet" href="{{ asset('css/siakad-home.css') }}">
</head>
<body class="siakad-home">
    <main class="home-shell">
        <nav class="home-navbar" aria-label="Navigasi utama">
            <a class="brand" href="{{ url('/') }}">
                <img src="{{ asset('images/sma-nurul-fikri-logo.jpeg') }}" alt="Logo SMA NURUL FIKRI" width="50">
                <span>
                    <strong>Blog SMA NURUL FIKRI</strong>
                    <small>Artikel dan kabar sekolah</small>
                </span>
            </a>

            <div class="nav-actions">
                <a href="{{ url('/') }}">Beranda</a>
                <a class="login-button" href="{{ url('/admin/login') }}">Login Admin</a>
            </div>
        </nav>

        <article class="article-shell">
            <header class="article-header">
                <a class="back-link" href="{{ url('/') }}">Kembali ke blog</a>
                <h1>{{ $blog->title }}</h1>
                <div class="blog-meta">
                    <span>{{ $blog->author?->name ?? 'Admin Sekolah' }}</span>
                    <span>{{ ($blog->published_at ?? $blog->created_at)?->translatedFormat('d M Y') ?? 'Belum bertanggal' }}</span>
                </div>
            </header>

            @if ($blog->cover_image)
                <img class="article-cover" src="{{ asset('storage/' . $blog->cover_image) }}" alt="{{ $blog->title }}">
            @endif

            <div class="article-content">
                {!! $blog->content !!}
            </div>
        </article>
    </main>
</body>
</html>
