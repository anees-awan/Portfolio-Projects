<?php
/*
Template Name: Video Listing Post Template2
*/
get_header(); ?>

<div id="primary" class="content-area">

    <main id="content"
        class="site-main post-435 post type-post status-publish format-standard post-password-protected hentry category-quotes">
        <?php
        while (have_posts()) :
            the_post();

            // Your custom post content goes here
        ?>

        <header class="page-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>

        <div class="page-content">

            <?php the_content(); ?>

            <?php 
                // Get the quote code from the custom field
                $video_code = get_post_meta(get_the_ID(), 'video_code', true);

                // Check if a quote code is present
                if ($video_code && !empty($video_code)) {
                    $quote_url = 'https://dubb.com/v/'.$video_code.'/embed?width=auto&amp;height=auto&amp;autoplay=0&amp;no_cta=1&amp;no_controls=0&amp;muted=0';
            ?>

            <div style="position: relative; height: 0; padding-bottom: 56.33%;">
                <iframe style="position: absolute; width: 100%; height: 100%; left: 0;"
                    allow="autoplay; encrypted-media; picture-in-picture" src="<?php echo $quote_url;?>" width="auto"
                    height="auto" frameborder="0" allowfullscreen="">
                </iframe>
            </div>
            <?php 
                }
            ?>

            <div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>

            <hr class="wp-block-separator has-alpha-channel-opacity">



            <div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>



            <div
                class="wp-block-columns is-layout-flex wp-container-core-columns-layout-1 wp-block-columns-is-layout-flex">
                <div class="wp-block-column is-layout-flow wp-block-column-is-layout-flow" style="flex-basis:33.33%">
                    <figure class="wp-block-image size-full is-resized"><img fetchpriority="high" decoding="async"
                            width="500" height="500"
                            src="https://medicareindy.com/wp-content/uploads/2024/01/headshot.webp" alt=""
                            class="wp-image-409" style="aspect-ratio:1;width:150px;height:auto"
                            srcset="https://medicareindy.com/wp-content/uploads/2024/01/headshot.webp 500w, https://medicareindy.com/wp-content/uploads/2024/01/headshot-300x300.webp 300w, https://medicareindy.com/wp-content/uploads/2024/01/headshot-150x150.webp 150w, https://medicareindy.com/wp-content/uploads/2024/01/headshot-96x96.webp 96w"
                            sizes="(max-width: 500px) 100vw, 500px"></figure>
                </div>



                <div class="wp-block-column is-layout-flow wp-block-column-is-layout-flow" style="flex-basis:66.66%">
                    <p>This video quote was prepared for you by:</p>



                    <p>Jason Denniston</p>



                    <p>To schedule a follow-up conversation, please go to <a href="https://medicareindy.info/meet"
                            target="_blank" rel="noopener">https://medicareindy.info/meet</a> to make an appointment.
                    </p>
                </div>
            </div>

            <div class="post-tags">
            </div>

        </div>
        <?php endwhile; // End of the loop. ?>
    </main>

</div><!-- #primary -->

<?php
get_footer(); // Display footer