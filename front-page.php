<?php get_header(); ?>

<main class="page page--default page--front">
    <div class="container">
        <h1 class="page__title"><?php the_title(); ?></h1>
        <div class="page__content">
            <?php
            /*
            // Initialize the service
            $mailchimp = new MailchimpService('5dbe8bd77df156ae2e3b92ba502f185d-us3', '409e8ec6a8');

            // Subscribe user
            $result = $mailchimp->subscribe('teszt+3@rvnd.hu', 'John', 'Doe', ['WordPress'], 'subscribed');

            // Get subscriber info
            $member = $mailchimp->getMemberByEmail('teszt+3@rvnd.hu');

            // Get first name only
            $firstName = $mailchimp->getFirstName('teszt+3@rvnd.hu');

            var_dump($result);
            */
            ?>
            <?php the_content(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>