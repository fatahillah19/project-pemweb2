<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog SMA NURUL FIKRI</title>

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

        @if ($featuredBlog)
            <section class="blog-hero" aria-label="Artikel utama">
                <div class="blog-hero-copy">
                    <p class="eyebrow">Artikel terbaru</p>
                    <h1>{{ $featuredBlog->title }}</h1>
                    <p>
                        {{ $featuredBlog->excerpt ?: Str::limit(strip_tags($featuredBlog->content), 180) }}
                    </p>
                    <div class="blog-meta">
                        <span>{{ $featuredBlog->author?->name ?? 'Admin Sekolah' }}</span>
                        <span>{{ ($featuredBlog->published_at ?? $featuredBlog->created_at)?->translatedFormat('d M Y') ?? 'Belum bertanggal' }}</span>
                    </div>
                    <a class="primary-action" href="{{ route('blog.show', $featuredBlog) }}">Baca Artikel</a>
                </div>

                <a class="blog-hero-media" href="{{ route('blog.show', $featuredBlog) }}" aria-label="Baca {{ $featuredBlog->title }}">
                    @if ($featuredBlog->cover_image)
                        <img src="{{ asset('storage/' . $featuredBlog->cover_image) }}" alt="{{ $featuredBlog->title }}">
                    @else
                        <div class="blog-placeholder">
                            <span>Blog</span>
                            <strong>SMA NURUL FIKRI</strong>
                        </div>
                    @endif
                </a>
            </section>
        @else
            <section class="empty-blog-state">
                <p class="eyebrow">Blog sekolah</p>
                <h1>Belum ada artikel published.</h1>
                <p>Tambahkan artikel dari panel Filament, ubah status menjadi Published, lalu artikel akan tampil di halaman utama.</p>
                <a class="primary-action" href="{{ url('/admin/blogs') }}">Kelola Blog</a>
            </section>
        @endif

        <section class="blog-list-section" aria-label="Daftar artikel">
            <div class="section-heading">
                <p class="section-kicker">Semua artikel</p>
                <h2>Kabar terbaru dari sekolah</h2>
            </div>

            @if ($blogs->isNotEmpty())
                <div class="blog-grid">
                    @foreach ($blogs as $blog)
                        <article class="blog-card">
                            <a class="blog-card-image" href="{{ route('blog.show', $blog) }}">
                                @if ($blog->cover_image)
                                    <img src="{{ asset('storage/' . $blog->cover_image) }}" alt="{{ $blog->title }}">
                                @else
                                    <span>{{ strtoupper(Str::substr($blog->title, 0, 2)) }}</span>
                                @endif
                            </a>
                            <div class="blog-card-body">
                                <div class="blog-meta">
                                    <span>{{ $blog->author?->name ?? 'Admin Sekolah' }}</span>
                                    <span>{{ ($blog->published_at ?? $blog->created_at)?->translatedFormat('d M Y') ?? 'Belum bertanggal' }}</span>
                                </div>
                                <h3><a href="{{ route('blog.show', $blog) }}">{{ $blog->title }}</a></h3>
                                <p>{{ $blog->excerpt ?: Str::limit(strip_tags($blog->content), 120) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $blogs->links() }}
                </div>
            @elseif ($featuredBlog)
                <p class="blog-list-empty">Artikel lain belum tersedia.</p>
            @endif
        </section>
    </main>
</body>
</html>
