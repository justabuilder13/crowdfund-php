{{ include('layouts/header.php', {title: user.name}) }}

<main class="container">
    <section class="profile-header">
        <h1>{{ user.name }}</h1>
        <p>{{ user.email }}</p>
        <p class="muted">Member since {{ user.created_date|date('F Y') }}</p>
    </section>

    <section class="profile-projects">
        <div class="section-heading">
            <h2>Projects ({{ projects|length }})</h2>
            <a href="{{ base }}/project/create">Start a new project</a>
        </div>

        {% if projects is not empty %}
            <div class="projects-grid">
                {% for project in projects %}
                    <article class="project-card">
                        <p class="project-category">{{ project.category_name }}</p>
                        <h3>{{ project.title }}</h3>
                        <p class="project-description">{{ project.description }}</p>

                        <div class="project-funding">
                            <strong>${{ project.current_amount|number_format(2) }}</strong>
                            <span>of ${{ project.goal_amount|number_format(2) }} goal</span>
                        </div>

                        <progress value="{{ min(project.percentage, 100) }}" max="100"></progress>

                        <div class="project-actions">
                            <a href="{{ base }}/project/show?id={{ project.id }}">View</a>
                            <a href="{{ base }}/project/edit?id={{ project.id }}">Edit</a>
                        </div>
                    </article>
                {% endfor %}
            </div>
        {% else %}
            <div class="empty-state">
                <h3>No projects yet</h3>
                <p>This creator has not created a project yet.</p>
                <a class="button button-dark" href="{{ base }}/project/create">Start a project</a>
            </div>
        {% endif %}
    </section>

    <section class="profile-projects">
        <div class="section-heading">
            <h2>Pledges ({{ pledges|length }})</h2>
        </div>

        {% if pledges is not empty %}
            <ul class="backers-list">
                {% for pledge in pledges %}
                    <li>
                        ${{ pledge.amount|number_format(2) }} to
                        <a href="{{ base }}/project/show?id={{ pledge.project_id }}"><strong>{{ pledge.project_title }}</strong></a>
                        <span class="muted">- {{ pledge.created_date|date('M j, Y') }}</span>
                    </li>
                {% endfor %}
            </ul>
        {% else %}
            <p class="muted">No pledges yet.</p>
        {% endif %}
    </section>
</main>

{{ include('layouts/footer.php') }}
