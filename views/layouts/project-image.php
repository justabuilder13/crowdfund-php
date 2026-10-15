{# Image du projet, ou un bloc gris s'il n'y en a pas #}
{% if project.image %}
    <img src="{{ project.image }}" alt="{{ project.title }}">
{% else %}
    <div class="image-placeholder"></div>
{% endif %}
