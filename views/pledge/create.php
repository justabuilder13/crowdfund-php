{{ include('layouts/header.php', {title: 'Back this project'}) }}

<main class="container form-page">
    <div class="form-heading">
        <h1>Back this project</h1>
        <p>{{ project.title }}</p>
    </div>

    {% if errors is defined %}
        <p class="message error-message">Please fix the errors below.</p>
    {% endif %}

    <form class="main-form" method="post" action="{{ base }}/pledge/create?project_id={{ project.id }}" novalidate>
        <label for="user_id">Backer</label>
        <select name="user_id" id="user_id" class="{{ errors.user_id is defined ? 'has-error' }}">
            <option value="">Select a backer</option>
            {% for user in users %}
                <option value="{{ user.id }}" {{ user.id == pledge.user_id ? 'selected' }}>{{ user.name }}</option>
            {% endfor %}
        </select>
        {% if errors.user_id is defined %}<span class="field-error">{{ errors.user_id }}</span>{% endif %}

        <label for="reward_id">Reward</label>
        <select name="reward_id" id="reward_id" class="{{ errors.reward_id is defined ? 'has-error' }}">
            <option value="">No reward, I just want to help</option>
            {% for reward in rewards %}
                {% set soldOut = reward.quantity is not null and reward.quantity <= 0 %}
                <option value="{{ reward.id }}" {{ reward.id == pledge.reward_id ? 'selected' }} {{ soldOut ? 'disabled' }}>
                    {{ reward.title }} (${{ reward.minimum_amount|number_format(2) }}+){{ soldOut ? ' - sold out' }}
                </option>
            {% endfor %}
        </select>
        {% if errors.reward_id is defined %}<span class="field-error">{{ errors.reward_id }}</span>{% endif %}

        <label for="amount">Pledge amount ($)</label>
        <input type="number" name="amount" id="amount" min="1" step="0.01" value="{{ pledge.amount }}"
               class="{{ errors.amount is defined ? 'has-error' }}">
        {% if errors.amount is defined %}<span class="field-error">{{ errors.amount }}</span>{% endif %}

        <div class="form-actions">
            <button class="button button-green" type="submit">Back project</button>
            <a class="button button-light" href="{{ base }}/project/show?id={{ project.id }}">Cancel</a>
        </div>
    </form>
</main>

{{ include('layouts/footer.php') }}
