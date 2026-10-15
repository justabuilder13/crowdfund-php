{{ include('layouts/header.php', {title: project.title}) }}

<main class="container">
    <section class="project-detail-header">
        <div>
            <p class="project-category">
                <a href="{{ base }}/projects?category={{ project.category_id }}">{{ project.category_name }}</a>
            </p>
            <h1>{{ project.title }}</h1>
            <p class="project-creator">
                By <a href="{{ base }}/user/show?id={{ project.user_id }}">{{ project.user_name }}</a>
            </p>
            {% if project.image %}
                <div class="project-cover">
                    <img src="{{ project.image }}" alt="{{ project.title }}">
                </div>
            {% endif %}
        </div>

        <div class="project-summary">
            <strong>${{ project.current_amount|number_format(2) }}</strong>
            <span>pledged of ${{ project.goal_amount|number_format(2) }} goal</span>

            <progress value="{{ min(project.percentage, 100) }}" max="100"></progress>

            <div class="project-info">
                <span>{{ project.percentage }}% funded</span>
                <span>{{ pledges|length }} {{ pledges|length == 1 ? 'pledge' : 'pledges' }}</span>
            </div>

            <p>{{ project.start_date|date('M j, Y') }} to {{ project.end_date|date('M j, Y') }}</p>

            {% if project.status == 'active' %}
                <a class="button button-green" href="{{ base }}/pledge/create?project_id={{ project.id }}">Back this project</a>
            {% else %}
                <p class="status-badge">This project is not accepting pledges.</p>
            {% endif %}
        </div>
    </section>

    <section class="project-detail-grid">
        <div class="project-main-content">
            <h2>About this project</h2>
            <p class="project-long-description">{{ project.description|nl2br }}</p>

            {% if pledges is not empty %}
                <section class="backers-section">
                    <h2>Recent backers</h2>
                    <ul class="backers-list">
                        {% for pledge in pledges|slice(0, 5) %}
                            <li>
                                <strong>{{ pledge.user_name }}</strong>
                                pledged ${{ pledge.amount|number_format(2) }}
                                {% if pledge.reward_title %}<span class="muted">- {{ pledge.reward_title }}</span>{% endif %}
                            </li>
                        {% endfor %}
                    </ul>
                </section>
            {% endif %}

            <section class="comments-section" id="comments">
                <h2>Comments ({{ comments|length }})</h2>

                {% for comment in comments %}
                    <article class="comment-card">
                        <strong>{{ comment.user_name }}</strong>
                        <small class="muted">{{ comment.created_date|date('M j, Y') }}</small>
                        <p>{{ comment.comment }}</p>
                    </article>
                {% else %}
                    <p class="muted">No comments yet.</p>
                {% endfor %}

                <form class="main-form compact-form" action="{{ base }}/comment/create" method="post">
                    <input type="hidden" name="project_id" value="{{ project.id }}">

                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id">
                        {% for user in users %}
                            <option value="{{ user.id }}">{{ user.name }}</option>
                        {% endfor %}
                    </select>

                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" maxlength="1000"></textarea>

                    <button class="button button-dark" type="submit">Post comment</button>
                </form>
            </section>
        </div>

        <aside class="rewards-panel">
            <div class="rewards-heading">
                <h2>Rewards</h2>
                <a href="{{ base }}/reward/create?project_id={{ project.id }}">Add reward</a>
            </div>

            {% for reward in rewards %}
                <article class="reward-card">
                    <p class="reward-amount">Pledge ${{ reward.minimum_amount|number_format(2) }} or more</p>
                    <h3>{{ reward.title }}</h3>
                    <p>{{ reward.description }}</p>

                    {% if reward.quantity is not null %}
                        <small>{{ reward.quantity > 0 ? reward.quantity ~ ' left' : 'Sold out' }}</small>
                    {% else %}
                        <small>Unlimited</small>
                    {% endif %}

                    <div class="reward-actions">
                        {% if project.status == 'active' and (reward.quantity is null or reward.quantity > 0) %}
                            <a href="{{ base }}/pledge/create?project_id={{ project.id }}&reward_id={{ reward.id }}">Select</a>
                        {% endif %}
                        <form action="{{ base }}/reward/delete" method="post"
                              onsubmit="return confirm('Delete this reward?');">
                            <input type="hidden" name="id" value="{{ reward.id }}">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            {% else %}
                <p class="muted">No rewards have been added yet.</p>
            {% endfor %}
        </aside>
    </section>

    <section class="project-owner-actions">
        <a class="button button-light" href="{{ base }}/project/edit?id={{ project.id }}">Edit project</a>
        <form action="{{ base }}/project/delete" method="post"
              onsubmit="return confirm('Delete this project? Its rewards, pledges and comments will also be deleted.');">
            <input type="hidden" name="id" value="{{ project.id }}">
            <button class="button button-danger" type="submit">Delete project</button>
        </form>
    </section>
</main>

{{ include('layouts/footer.php') }}
