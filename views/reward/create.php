{{ include('layouts/header.php', {title: 'Add a reward'}) }}

<main class="container form-page">
    <div class="form-heading">
        <h1>Add a reward</h1>
        <p>{{ project.title }}</p>
    </div>

    {% if errors is defined %}
        <p class="message error-message">Please fix the errors below.</p>
    {% endif %}

    <form class="main-form" method="post" action="{{ base }}/reward/create?project_id={{ project.id }}" novalidate>
        <label for="title">Reward title</label>
        <input type="text" name="title" id="title" maxlength="150" value="{{ reward.title }}"
               class="{{ errors.title is defined ? 'has-error' }}">
        {% if errors.title is defined %}<span class="field-error">{{ errors.title }}</span>{% endif %}

        <label for="description">Description</label>
        <textarea name="description" id="description"
                  class="{{ errors.description is defined ? 'has-error' }}">{{ reward.description }}</textarea>
        {% if errors.description is defined %}<span class="field-error">{{ errors.description }}</span>{% endif %}

        <label for="minimum_amount">Minimum pledge ($)</label>
        <input type="number" name="minimum_amount" id="minimum_amount" step="0.01" min="1" value="{{ reward.minimum_amount }}"
               class="{{ errors.minimum_amount is defined ? 'has-error' }}">
        {% if errors.minimum_amount is defined %}<span class="field-error">{{ errors.minimum_amount }}</span>{% endif %}

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" min="1" placeholder="Leave empty for unlimited" value="{{ reward.quantity }}"
               class="{{ errors.quantity is defined ? 'has-error' }}">
        {% if errors.quantity is defined %}<span class="field-error">{{ errors.quantity }}</span>{% endif %}

        <div class="form-actions">
            <button class="button button-dark" type="submit">Add reward</button>
            <a class="button button-light" href="{{ base }}/project/show?id={{ project.id }}">Cancel</a>
        </div>
    </form>
</main>

{{ include('layouts/footer.php') }}
