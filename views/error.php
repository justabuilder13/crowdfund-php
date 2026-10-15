{{ include('layouts/header.php', {title: 'Error'}) }}

<main class="container">
    <div class="empty-state">
        <h1>Oops.</h1>
        <p>{{ msg }}</p>
        <a class="button button-dark" href="{{ base }}/projects">Back to projects</a>
    </div>
</main>

{{ include('layouts/footer.php') }}
