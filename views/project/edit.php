{{ include('layouts/header.php', {title: 'Edit project'}) }}

<main class="container form-page">
    <div class="form-heading">
        <h1>Edit project</h1>
        <p>Modify the campaign information and save your changes.</p>
    </div>

    {% if errors is defined %}
        <p class="message error-message">Please fix the errors below.</p>
    {% endif %}

    <form class="main-form" method="post" action="{{ base }}/project/edit?id={{ project.id }}" novalidate>
        {{ include('project/form-fields.php') }}

        <label for="status">Status</label>
        <select name="status" id="status" class="{{ errors.status is defined ? 'has-error' }}">
            {% for status in statuses %}
                <option value="{{ status }}" {{ status == project.status ? 'selected' }}>{{ status|capitalize }}</option>
            {% endfor %}
        </select>
        {% if errors.status is defined %}<span class="field-error">{{ errors.status }}</span>{% endif %}

        <div class="form-actions">
            <button class="button button-dark" type="submit">Save changes</button>
            <a class="button button-light" href="{{ base }}/project/show?id={{ project.id }}">Cancel</a>
        </div>
    </form>
</main>

{{ include('layouts/footer.php') }}
