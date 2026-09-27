<?php
/**
 * Archive: Projects — The Cellar (Work Index)
 *
 * Horizontal ledger rows with hover-reveal preview images.
 * Wine-cellar-inspired project directory.
 *
 * @package TannicStudio
 */

get_header();

$total_projects = wp_count_posts( 'project' )->publish;
?>

<!-- Hero Banner -->
<section class="work-hero">
    <p class="work-hero-label mono"><?php esc_html_e( 'Portfolio', 'tannic-studio' ); ?></p>
    <h1 class="work-hero-title">
        <?php esc_html_e( 'Our', 'tannic-studio' ); ?> <span class="accent"><?php esc_html_e( 'Work.', 'tannic-studio' ); ?></span>
    </h1>
    <span class="work-hero-count">
        <?php
        /* translators: %d: number of projects */
        printf( esc_html__( '%d projects', 'tannic-studio' ), intval( $total_projects ) );
        ?>
    </span>
</section>

<!-- Project List -->
<?php if ( have_posts() ) : ?>
<div class="work-list">
    <?php while ( have_posts() ) : the_post();
        $year       = get_field( 'project_year' );
        $tech_stack = get_field( 'project_tech_stack' );
        $thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'tannic-editorial' );
    ?>
        <a href="<?php the_permalink(); ?>" class="work-row" data-reveal>
            <span class="work-row-year mono"><?php echo esc_html( $year ?: '&mdash;' ); ?></span>
            <span class="work-row-title"><?php the_title(); ?></span>
            <?php if ( $tech_stack ) : ?>
                <span class="work-row-stack mono"><?php echo esc_html( $tech_stack ); ?></span>
            <?php endif; ?>
            <span class="work-row-action mono"><?php esc_html_e( 'View', 'tannic-studio' ); ?> &rarr;</span>
            <?php if ( $thumb_url ) : ?>
                <div class="work-row-preview" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>')"></div>
            <?php endif; ?>
        </a>
    <?php endwhile; ?>
</div>

<!-- Pagination -->
<?php
$pagination = paginate_links( array(
    'prev_text' => '&larr;',
    'next_text' => '&rarr;',
    'type'      => 'list',
) );

if ( $pagination ) :
?>
    <div class="work-pagination">
        <?php echo $pagination; ?>
    </div>
<?php endif; ?>

<?php else : ?>
<div class="work-list" style="text-align: center; padding: var(--space-3xl) var(--container-pad);">
    <p class="mono" style="color: rgba(var(--col-dark-rgb), 0.5);">
        <?php esc_html_e( 'No projects found. Check back soon.', 'tannic-studio' ); ?>
    </p>
</div>
<?php endif; ?>

<?php get_footer(); ?>
