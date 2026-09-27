<?php
/**
 * Tannic Studio: Final Content Seeder
 * Status: MASTER DECK (The "Unicorn" & "Beer and Ballet" Edition)
 *
 * This script programmatically injects the approved copy into the ACF fields.
 *
 * INSTRUCTIONS:
 * 1. Include in functions.php: require_once('seeder_content.php');
 * 2. Refresh any page.
 * 3. Remove immediately to prevent unnecessary database calls on every load.
 */
function tannic_seed_master_deck() {

    // Safety check: Ensure ACF is active
    if ( ! function_exists('update_field') ) {
        error_log('Tannic Seeder: ACF not active.');
        return;
    }

    // ====================================================
    // 1. PHILOSOPHY PAGE (The "Unicorn" Bio)
    // ====================================================
    $about_page = get_pages(array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-philosophy.php'
    ));
    $about_id = !empty($about_page) ? $about_page[0]->ID : null;

    if ( $about_id ) {
        // HEADLINE
        update_field('about_desk_label', 'The Art of Architecture', $about_id);
        update_field('about_headline', 'Decanting digital', $about_id);
        update_field('about_headline_highlight', 'experiences', $about_id);

        // LEAD PARAGRAPH
        $lead = 'Your brand and digital presence should be as elevated and hospitality focused as the experiences at your brick-and-mortar. In fact, your digital presence is the first touch any customer will have with your brand - it deserves to have the same care and artistry as every other aspect of your business.';
        update_field('about_lead', $lead, $about_id);

        // BIO BODY (HTML)
        $bio_html = '
        <p>At Tannic Studio, we are building a digital agency for the AI age with old world artistry in mind. We blend modern tech stacks with pencil-to-paper work, crafting bespoke digital experiences with hospitality brands in mind.</p>
        <p><strong>Aesthetics are not just decoration. They are storytelling.</strong></p>
        <p>There is a misconception in our industry that "branding" is just slapping a fun color on a template. But if you run a physical space, you know that the story is told through the lighting, the texture of the menu, the way the host greets you. Your website must do the same.</p>
        <p><strong>The Logic:</strong> My superpower is listening. I don\'t just talk to the owner; I look at how the busser moves, how the reservation system talks to the POS, and where the manual work is draining your energy. We build systems that save you time.</p>
        <p><strong>The Magic:</strong> Because I am an <strong>illustrator, designer, and developer in one</strong>, I can weave magic into that system without the bloat of a massive agency. I bridge the gap between "it works" and "it feels like you." Whether it\'s a semi-custom build or a flagship project, I ensure your digital front door has the same soul as your real one.</p>
        <p>You don\'t have to choose between a high-converting machine and a beautiful work of art. You just need someone who speaks both languages.</p>';
        update_field('about_bio', $bio_html, $about_id);

        // SIGNATURE
        update_field('about_sign_name', 'Kate Kight', $about_id);
        update_field('about_sign_role', 'Founder / Systems Architect', $about_id);
    }

    // ====================================================
    // 2. SERVICES (The Menu Vignettes)
    // ====================================================
    if ( ! function_exists( 'media_sideload_image' ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    /**
     * Cache and sideload a remote image once, then reuse its attachment ID.
     */
    $seeded_media_cache = array();
    $tannic_seed_media_id = function( $url ) use ( &$seeded_media_cache ) {
        if ( empty( $url ) ) {
            return 0;
        }

        if ( isset( $seeded_media_cache[ $url ] ) ) {
            return $seeded_media_cache[ $url ];
        }

        $existing_id = attachment_url_to_postid( $url );
        if ( $existing_id ) {
            $seeded_media_cache[ $url ] = (int) $existing_id;
            return (int) $existing_id;
        }

        $attach_id = media_sideload_image( $url, 0, null, 'id' );
        if ( is_wp_error( $attach_id ) ) {
            error_log( 'Tannic Seeder: Failed to sideload image: ' . $url . ' (' . $attach_id->get_error_message() . ')' );
            $seeded_media_cache[ $url ] = 0;
            return 0;
        }

        $seeded_media_cache[ $url ] = (int) $attach_id;
        return (int) $attach_id;
    };

    // Hard replace all existing services.
    $existing_services = get_posts( array(
        'post_type'      => 'service',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );

    foreach ( $existing_services as $service_post_id ) {
        wp_delete_post( $service_post_id, true );
    }

    $service_payloads = array(
        array(
            'title'                => 'Templates Tier 1: Buy + Implement',
            'category_number'      => '01',
            'price'                => 4500,
            'price_label'          => 'From $4.5k',
            'weeks'                => 2,
            'strategic_heading'    => 'Fast Launch Foundation',
            'description'          => 'A premium template implementation for businesses who need to launch quickly without sacrificing polish. We handle setup, quality checks, and baseline optimization so you can go live with confidence.',
            'specs'                => "+ Buy + implement template build\n+ Baseline SEO and analytics setup\n+ QA and launch support\n+ Mix-and-match ready with strategy and connector services",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1497366412874-3415097a27e7?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Templates Tier 2: Buy + Customize + Strategy',
            'category_number'      => '02',
            'price'                => 7800,
            'price_label'          => 'From $7.8k',
            'weeks'                => 4,
            'strategic_heading'    => 'Conversion + Customization',
            'description'          => 'A template-led build with strategic customization to align brand, search visibility, and systems. Ideal for teams ready to layer in stronger growth strategy without going fully bespoke.',
            'specs'                => "+ Template customization with strategic UX direction\n+ SEO/content structure and integration planning\n+ Performance and conversion tuning\n+ Bundle discount eligible when paired with Brand Refresh",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Custom Build: Bespoke Site',
            'category_number'      => '03',
            'price'                => 18500,
            'price_label'          => 'From $18.5k',
            'weeks'                => 8,
            'strategic_heading'    => 'Flagship Bespoke Experience',
            'description'          => 'A fully custom digital presence built around your operations, audience, and brand world. Designed for ambitious teams who need flexibility, storytelling depth, and scalable architecture.',
            'specs'                => "+ Fully bespoke UX/UI and development\n+ Content architecture + conversion strategy\n+ Integrations and launch QA\n+ Can be bundled with Full Brand Design for package discount",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1487014679447-9f8336841d58?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1545239351-1141bd82e8a6?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Connectors: Integration + Workflow Audit',
            'category_number'      => '04',
            'price'                => 12000,
            'price_label'          => 'From $12k',
            'weeks'                => 6,
            'strategic_heading'    => 'Operational Systems Alignment',
            'description'          => 'Audit and improve mission-critical workflows across CRM, POS, ecommerce, and supporting tools. We remove friction points so your team moves faster and customers experience fewer drop-offs.',
            'specs'                => "+ Integration and workflow audit\n+ CRM/POS/ecommerce systems mapping\n+ Process redesign recommendations\n+ Add-on monthly maintenance and strategy services available",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Full Brand Design + Guidelines',
            'category_number'      => '05',
            'price'                => 14000,
            'price_label'          => 'From $14k',
            'weeks'                => 7,
            'strategic_heading'    => 'Brand System Creation',
            'description'          => 'A complete brand identity system with strategic positioning and practical usage guidance. Built for teams who need consistency across digital, print, and customer touchpoints.',
            'specs'                => "+ Full brand identity and visual guidelines\n+ Logo, typography, color, and usage system\n+ Messaging direction for cross-channel consistency\n+ Optional add-on templates for social and email",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1523726491678-bf852e717f6a?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Brand Refresh',
            'category_number'      => '06',
            'price'                => 6500,
            'price_label'          => 'From $6.5k',
            'weeks'                => 3,
            'strategic_heading'    => 'Modernization Without Losing Equity',
            'description'          => 'Refresh your current brand with targeted upgrades to logo options, color palette, and supporting expression. Perfect when you need sharper positioning without a full rebrand timeline.',
            'specs'                => "+ Update logo options and color palette\n+ Refresh core visual system for modern channels\n+ Improve brand consistency across digital touchpoints\n+ Optional add-on templates and marketing assets",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1533750349088-cd871a92f312?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1504274066651-8d31a536b11a?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Marketing Strategy Audit',
            'category_number'      => '07',
            'price'                => 7200,
            'price_label'          => 'From $7.2k',
            'weeks'                => 3,
            'strategic_heading'    => 'Growth Strategy Diagnostics',
            'description'          => 'A focused review of your current marketing channels, messaging, and customer journey to identify where strategy and execution are leaking momentum.',
            'specs'                => "+ Marketing strategy audit and recommendations\n+ Channel + messaging performance review\n+ Action roadmap tied to measurable goals\n+ Works best when bundled with digital integration services",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1531498860502-7c67cf02f657?auto=format&fit=crop&q=80&w=1200',
        ),
        array(
            'title'                => 'Print + Merch Design',
            'category_number'      => '08',
            'price'                => 9000,
            'price_label'          => 'From $9k',
            'weeks'                => 5,
            'strategic_heading'    => 'Physical Brand Extensions',
            'description'          => 'Design support for printed collateral and merchandise systems that extend your brand into tactile, high-impact moments for customers and communities.',
            'specs'                => "+ Print collateral and merch design suite\n+ Production-ready design files and specs\n+ Cohesion with digital + brand system\n+ Eligible for mix-and-match bundle discounts",
            'deliverables_heading' => 'Deliverables',
            'context_image'        => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&q=80&w=1600',
            'gallery_1'            => 'https://images.unsplash.com/photo-1519638831568-d9897f3b2384?auto=format&fit=crop&q=80&w=1200',
            'gallery_2'            => 'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?auto=format&fit=crop&q=80&w=1200',
            'gallery_3'            => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=1200',
        ),
    );

    foreach ( $service_payloads as $index => $service_data ) {
        $media_keys = array( 'context_image', 'gallery_1', 'gallery_2', 'gallery_3' );
        $media_ids  = array();

        foreach ( $media_keys as $media_key ) {
            $media_id = $tannic_seed_media_id( $service_data[ $media_key ] );
            // Keep seed deterministic even if a placeholder media URL is temporarily unavailable.
            $media_ids[ $media_key ] = $media_id;
        }

        $service_id = wp_insert_post( array(
            'post_title'   => $service_data['title'],
            'post_type'    => 'service',
            'post_status'  => 'publish',
            'menu_order'   => $index,
        ) );

        if ( ! $service_id || is_wp_error( $service_id ) ) {
            error_log( 'Tannic Seeder: Failed to create service post: ' . $service_data['title'] );
            continue;
        }

        update_field( 'service_category_number', $service_data['category_number'], $service_id );
        update_field( 'service_price', $service_data['price'], $service_id );
        update_field( 'service_price_label', $service_data['price_label'], $service_id );
        update_field( 'service_weeks', $service_data['weeks'], $service_id );
        update_field( 'service_strategic_heading', $service_data['strategic_heading'], $service_id );
        update_field( 'service_description', $service_data['description'], $service_id );
        update_field( 'service_specs', $service_data['specs'], $service_id );
        update_field( 'service_deliverables_heading', $service_data['deliverables_heading'], $service_id );
        update_field( 'service_context_image', $media_ids['context_image'], $service_id );
        update_field( 'service_gallery_1', $media_ids['gallery_1'], $service_id );
        update_field( 'service_gallery_2', $media_ids['gallery_2'], $service_id );
        update_field( 'service_gallery_3', $media_ids['gallery_3'], $service_id );
    }

    // ====================================================
    // 3. FRONT PAGE (The Process Arc)
    // ====================================================
    $front_page_id = get_option('page_on_front');

    if ( $front_page_id ) {
        // Step 1: The Introduction
        update_field('process_step_1_title', 'The Introduction', $front_page_id);
        update_field('process_step_1_body', 'Getting to know your brand and goals is the foundation of what we do. Who is your customer? Where are the bottlenecks in your current service? We audit your tech stack to ensure we are building on solid ground and can create solutions that scale - beautifully.', $front_page_id);

        // Step 2: The Architecture
        update_field('process_step_2_title', 'The Architecture', $front_page_id);
        update_field('process_step_2_body', 'Our design and development process takes the foundation we built and adds in our special touches, from hand-drawn animations to high-converting landing pages, our team turns your brand into an immersive digital experience that builds a loyal fanbase before any even walks through your front door.', $front_page_id);

        // Step 3: The Service
        update_field('process_step_3_title', 'The Service', $front_page_id);
        update_field('process_step_3_body', 'Launch day is not the finish line; it is opening night. We build analytics dashboards from the get-go that help us identify what resonates and how we can adapt and scale based on customer activities and feedback.', $front_page_id);

        // Step 4: The Keys
        update_field('process_step_4_title', 'The Keys', $front_page_id);
        update_field('process_step_4_body', 'Gatekeeping is the enemy at Tannic Studio. We want your staff to be able to maintain and scale your digital experience, whether it\'s providing a full set of templates for social media or easy-to-edit landing pages, we want to ensure that your site is as easy to use as it is easy to impress.', $front_page_id);
    }

    // ====================================================
    // 4. CONTACT PAGE (Reservation Desk)
    // ====================================================
    $contact_page = get_pages(array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-contact.php'
    ));
    $contact_id = !empty($contact_page) ? $contact_page[0]->ID : null;

    if ( $contact_id ) {
        update_field('contact_heading', 'Let\'s translate your vision.', $contact_id);
        update_field('contact_subheading', 'We are looking for partners who value the craft. Tell us about your space, your story, and where you want to go next.', $contact_id);
    }

    // ====================================================
    // 5. PROJECT: "BEER AND BALLET"
    // ====================================================
    $project_title = 'Beer and Ballet';
    $existing_project = get_page_by_title( $project_title, OBJECT, 'project' );

    // Create if doesn't exist
    if ( ! $existing_project ) {
        $proj_id = wp_insert_post([
            'post_title'    => $project_title,
            'post_type'     => 'project',
            'post_status'   => 'publish',
            'post_excerpt'  => 'A franchise-ready digital platform for an iconic dance brand, balancing artistry with complex registration systems.'
        ]);
    } else {
        $proj_id = $existing_project->ID;
    }

    if ( $proj_id ) {
        // Meta
        update_field('project_client', 'Beer and Ballet', $proj_id);
        update_field('project_year', '2025', $proj_id);
        update_field('project_tech_stack', 'Webflow, Custom Code, Ticket Integration', $proj_id);
        update_field('project_tech_badge', 'loc: baltimore_md', $proj_id);
        update_field('featured_on_homepage', 1, $proj_id);
        update_field('project_featured_homepage', 1, $proj_id);

        // The Challenge
        $challenge_html = '
        <p>Beer and Ballet had a strong brand and concept, but a difficult challenge—as a growing franchise who relies on tickets and registrations, how do we build a high-converting website that busy dance teachers can use across multiple states and locations? How does that website also tell the story of the artistry and partnerships Beer and Ballet has built, and encourage new communities to get involved?</p>';
        update_field('project_challenge', $challenge_html, $proj_id);

        // The Solution
        $solution_html = '
        <p>We built a multiphase project to expand their brand from a logo to a full suite of illustration, merch, and an iconic digital presence. The modular site, built with custom Webflow components, allows the founder to share her story and inspire new partners, while the ticket integration system uses bespoke code to handle the complex needs of Beer and Ballet franchise classes.</p>';
        update_field('project_solution', $solution_html, $proj_id);

        // The Results
        $results_html = '
        <p>Everyone loves beer and ballet!</p>';
        update_field('project_results', $results_html, $proj_id);
    }

    error_log('Tannic Seeder: Master Copy Deck Applied Successfully.');
}

add_action('init', 'tannic_seed_master_deck');
