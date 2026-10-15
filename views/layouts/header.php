<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ title ?? 'Crowdfund' }} | Crowdfund</title>
    <link rel="stylesheet" href="{{ asset }}/css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="site-nav">
        <a class="logo" href="{{ base }}/">Crowdfund</a>

        <div class="nav-links">
            <a href="{{ base }}/projects">Discover</a>
            <a href="{{ base }}/project/create">Start a project</a>
        </div>

        <a class="profile-link" href="{{ base }}/user/show?id=1">Profile</a>
    </nav>
</header>

{# Message de confirmation ou d'erreur ajouté par View::redirect() #}
{% if flash %}
    <div class="flash-wrapper">
        <p class="message {{ flash.type == 'error' ? 'error-message' : 'success-message' }}" role="status">
            {{ flash.message }}
        </p>
    </div>
{% endif %}
