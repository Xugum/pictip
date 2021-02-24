<script>
    let hash = window.location.hash;
    if (hash !== '' || hash !== undefined) {
        window.location.href = `{{ route('login-auth') }}?${hash.replace('#', '')}`;
    } else {
        window.location.href = `{{ route('home') }}`;
    }
</script>