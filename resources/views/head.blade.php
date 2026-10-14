{{-- Applies the stored theme before first paint so the page never flashes the wrong scheme. --}}
<meta name="color-scheme" content="light dark">
<script>
    (function () {
        try {
            var theme = localStorage.getItem('theme');
            if (theme !== 'light' && theme !== 'dark') {
                theme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.dataset.theme = theme;
        } catch (e) {}
    })();
</script>
