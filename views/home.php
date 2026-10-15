{{ include('layouts/header.php', {title: 'Home'}) }}

{# Vue statique: cette page n'utilise aucune donnée de la BD #}
<main>
    <section class="home-top">
        <div>
            <h1>Discover projects worth backing.</h1>
            <p>Explore ideas from independent creators and help bring them to life.</p>
        </div>
        <a href="{{ base }}/projects">Browse all projects</a>
    </section>

    <section class="how-page">
        <div class="section-title-row">
            <h2>How Crowdfund works</h2>
        </div>

        <div class="how-steps">
            <article>
                <span>01</span>
                <h3>Start a project</h3>
                <p>Creators describe their idea, choose a category, set a funding goal and pick the campaign dates.</p>
            </article>
            <article>
                <span>02</span>
                <h3>Add rewards</h3>
                <p>Each project can offer rewards to thank its backers, with a minimum pledge and an optional limited quantity.</p>
            </article>
            <article>
                <span>03</span>
                <h3>Get backed</h3>
                <p>Backers pledge any amount they want. Every pledge is added to the project total right away.</p>
            </article>
            <article>
                <span>04</span>
                <h3>Talk with the community</h3>
                <p>Anyone can leave a comment on a project to ask questions or share encouragement.</p>
            </article>
        </div>

        <div class="how-cta">
            <h2>Have an idea?</h2>
            <a class="button button-dark" href="{{ base }}/project/create">Start a project</a>
            <a class="button button-light" href="{{ base }}/projects">Browse projects</a>
        </div>
    </section>
</main>

{{ include('layouts/footer.php') }}
