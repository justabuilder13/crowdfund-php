{{ include('layouts/header.php', {title: 'Explore projects'}) }}

<main class="container">
    <section class="projects-header">
        <div>
            <h1>Explore projects</h1>
            <p>Browse crowdfunding campaigns created by our community.</p>
        </div>
        <a class="button button-dark" href="{{ base }}/project/create">Start a project</a>
    </section>

    {# Projet vedette et recommandations (seulement sans filtre de catégorie) #}
    {% if featured %}
        <section class="discovery-layout discovery-in-page">
            <div class="featured-block">
                <div class="section-title-row">
                    <h2>Featured project</h2>
                </div>

                <a class="featured-project" href="{{ base }}/project/show?id={{ featured.id }}">
                    <div class="featured-image">
                        {{ include('layouts/project-image.php', {project: featured}) }}
                    </div>

                    <div class="featured-content">
                        <h3>{{ featured.title }}</h3>
                        <p class="featured-description">{{ featured.description }}</p>
                        <p class="project-byline">By {{ featured.user_name }}</p>

                        <div class="funding-bar">
                            <span style="width: {{ min(featured.percentage, 100) }}%"></span>
                        </div>

                        <div class="funding-meta">
                            <strong>{{ featured.percentage }}% funded</strong>
                            <span>{{ featured.category_name }}</span>
                        </div>
                    </div>
                </a>
            </div>

            <aside class="recommended-block">
                <div class="section-title-row">
                    <h2>Recommended</h2>
                </div>

                <div class="recommendation-list">
                    {% for project in recommended %}
                        <a class="recommendation-item" href="{{ base }}/project/show?id={{ project.id }}">
                            <div class="recommendation-image">
                                {{ include('layouts/project-image.php') }}
                            </div>
                            <div>
                                <h3>{{ project.title }}</h3>
                                <p>{{ project.percentage }}% funded</p>
                                <span>By {{ project.user_name }}</span>
                            </div>
                        </a>
                    {% else %}
                        <p class="muted">More projects coming soon.</p>
                    {% endfor %}
                </div>
            </aside>
        </section>

        <div class="section-title-row all-projects-title">
            <h2>All projects</h2>
        </div>
    {% endif %}

    {# Filtre par catégorie (GET ?category=id) #}
    <nav class="category-filter" aria-label="Filter by category">
        <a href="{{ base }}/projects" class="{{ currentCategory ? '' : 'is-active' }}">All</a>
        {% for category in categories %}
            <a href="{{ base }}/projects?category={{ category.id }}"
               class="{{ currentCategory == category.id ? 'is-active' : '' }}">{{ category.name }}</a>
        {% endfor %}
    </nav>

    {% if projects is not empty %}
        <section class="projects-grid">
            {% for project in projects %}
                <article class="project-card">
                    <p class="project-category">{{ project.category_name }}</p>
                    <h2><a href="{{ base }}/project/show?id={{ project.id }}">{{ project.title }}</a></h2>
                    <p class="project-creator">By {{ project.user_name }}</p>
                    <p class="project-description">{{ project.description }}</p>

                    <div class="project-funding">
                        <strong>${{ project.current_amount|number_format(2) }}</strong>
                        <span>of ${{ project.goal_amount|number_format(2) }} goal</span>
                    </div>

                    <progress value="{{ min(project.percentage, 100) }}" max="100"></progress>

                    <div class="project-info">
                        <span>{{ project.percentage }}% funded</span>
                        <span>Ends {{ project.end_date|date('M j, Y') }}</span>
                    </div>

                    <div class="project-actions">
                        <a href="{{ base }}/project/show?id={{ project.id }}">View</a>
                        <a href="{{ base }}/project/edit?id={{ project.id }}">Edit</a>
                        <form action="{{ base }}/project/delete" method="post"
                              onsubmit="return confirm('Delete this project? This cannot be undone.');">
                            <input type="hidden" name="id" value="{{ project.id }}">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            {% endfor %}
        </section>
    {% else %}
        <div class="empty-state">
            <h2>No projects here yet</h2>
            <p>Be the first creator to launch a project{{ currentCategory ? ' in this category' : '' }}.</p>
            <a class="button button-dark" href="{{ base }}/project/create">Start a project</a>
        </div>
    {% endif %}
</main>

{{ include('layouts/footer.php') }}
