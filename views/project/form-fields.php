{# Champs communs à project/create et project/edit #}
<label for="category_id">Category</label>
<select name="category_id" id="category_id" class="{{ errors.category_id is defined ? 'has-error' }}">
    <option value="">Select a category</option>
    {% for category in categories %}
        <option value="{{ category.id }}" {{ category.id == project.category_id ? 'selected' }}>{{ category.name }}</option>
    {% endfor %}
</select>
{% if errors.category_id is defined %}<span class="field-error">{{ errors.category_id }}</span>{% endif %}

<label for="title">Project title</label>
<input type="text" id="title" name="title" maxlength="150" value="{{ project.title }}"
       class="{{ errors.title is defined ? 'has-error' }}">
{% if errors.title is defined %}<span class="field-error">{{ errors.title }}</span>{% endif %}

<label for="description">Description</label>
<textarea id="description" name="description"
          class="{{ errors.description is defined ? 'has-error' }}">{{ project.description }}</textarea>
{% if errors.description is defined %}<span class="field-error">{{ errors.description }}</span>{% endif %}

<label for="goal_amount">Funding goal ($)</label>
<input type="number" id="goal_amount" name="goal_amount" min="100" step="0.01" value="{{ project.goal_amount }}"
       class="{{ errors.goal_amount is defined ? 'has-error' }}">
{% if errors.goal_amount is defined %}<span class="field-error">{{ errors.goal_amount }}</span>{% endif %}

<div class="form-row">
    <div>
        <label for="start_date">Start date</label>
        <input type="date" id="start_date" name="start_date" value="{{ project.start_date }}"
               class="{{ errors.start_date is defined ? 'has-error' }}">
        {% if errors.start_date is defined %}<span class="field-error">{{ errors.start_date }}</span>{% endif %}
    </div>
    <div>
        <label for="end_date">End date</label>
        <input type="date" id="end_date" name="end_date" value="{{ project.end_date }}"
               class="{{ errors.end_date is defined ? 'has-error' }}">
        {% if errors.end_date is defined %}<span class="field-error">{{ errors.end_date }}</span>{% endif %}
    </div>
</div>
