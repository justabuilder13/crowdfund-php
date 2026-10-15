{{ include('layouts/header.php', {title: 'Start a project'}) }}

<main class="container form-page">
    <div class="form-heading">
        <h1>Start a project</h1>
        <p>Enter the main information for your crowdfunding campaign.</p>
    </div>

    {% if errors is defined %}
        <p class="message error-message">Please fix the errors below.</p>
    {% endif %}

    <form class="main-form" method="post" action="{{ base }}/project/create" novalidate>
        <label for="user_id">Creator</label>
        <select name="user_id" id="user_id" class="{{ errors.user_id is defined ? 'has-error' }}">
            <option value="">Select a creator</option>
            {% for user in users %}
                <option value="{{ user.id }}" {{ user.id == project.user_id ? 'selected' }}>{{ user.name }}</option>
            {% endfor %}
        </select>
        {% if errors.user_id is defined %}<span class="field-error">{{ errors.user_id }}</span>{% endif %}

        {{ include('project/form-fields.php') }}

        <button class="button button-dark" type="submit">Launch project</button>
    </form>
</main>

{{ include('layouts/footer.php') }}
