<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package newsstats
 */

get_header(); ?>

<style type="text/css">
#table_owners { margin: 1rem 0; }
.google-visualization-table-td { white-space: nowrap; }
</style>

	<div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>

            <?php
            $counts = 0;
            $owners = 0;
            $terms  = get_terms( 'owner' );
            $rows   = '';

            foreach ( $terms as $term ) {
                if ( $term->count < 20 ) {
                    continue;
                }

                $counts += $term->count;
                $owners++;
                // Get Owner score data.
                $args = array(
                    'post_type' => 'publication',
                    'post_per_page' => -300,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'owner',
                            'fields' => 'ids',
                            'terms' => $term,
                        )
                    )
                );
                $query = new WP_Query( $args );

                $pubs_data = netrics_get_pubs_pagespeed_query( $query );

                if ( $pubs_data ) {
                    $rows .= "['{$term->name}',{$term->count},";

                    foreach ( $pubs_data as $key => $data ) {
                        $num = nstats_mean( $data );

                        switch ( $key ) {
                            case 'score':
                                $num = $num * 100;
                                break;
                            case 'speed':
                                $num = $num / 1000;
                                break;
                            case 'tti':
                                $num = $num / 1000;
                                break;
                            case 'size':
                                $num = $num / 1000000;
                                break;
                            default:
                                break;
                        }

                        $rows .= $num . ',';

                    }
                $rows .= "],\n";
                }
            }
            ?>



            <h2>Owners by score</h2>
            <p>Table of Owners with 20+ daily newspapers (<?php echo $owners; ?> owners of <?php echo $counts; ?> dailies), in order of their average PageSpeed Insights scores for 2015-05 articles.</p>

            <figure id="table_owners"></figure>

            <hr>

            <h2>Detailed results</h2>
            <p>PageSpeed Insights results averages for the daily newspapers of these owner groups:
            <?php
            // Stats tables for each Owner.
            foreach ( $terms as $term ) {
                if ( $term->count < 20 ) {
                    continue;
                }

                // Get Owner score data.
                $args = array(
                    'post_type' => 'publication',
                    'post_per_page' => -300,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'owner',
                            'fields' => 'ids',
                            'terms' => $term,
                        )
                    )
                );
                $query = new WP_Query( $args );

                $pubs_data = netrics_get_pubs_pagespeed_query( $query );

                if ( $pubs_data ) {
            ?>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <table class="tabular">
                <caption><a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a>: PageSpeed average results (2019-05)</caption>
                <?php echo netrics_pagespeed( $pubs_data ); // Populated <thead> and <tbody>. ?>
                <tfoot>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;"><?php echo count( $pubs_data['score'] ) ?> articles from <?php echo $query->found_posts; ?> newspapers</td>
                    </tr>
                </tfoot>
            </table>
                <?php } // if ( $pubs_data ) ?>
            <?php } // foreach ( $terms as $term ) ?>

<script type="text/javascript">
google.charts.load('current', {'packages':['table']});
google.charts.setOnLoadCallback(drawTableOwners);

function drawTableOwners() {
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Owner Group');
        data.addColumn('number', 'Papers');
        data.addColumn('number', 'Dom');
        data.addColumn('number', 'Requests');
        data.addColumn('number', 'MB');
        data.addColumn('number', 'Speed(s)');
        data.addColumn('number', 'TTI(s)');
        data.addColumn('number', 'Score');
        data.addRows([
<?php echo $rows; ?>
    ]);

    // Print number with separators for Ks, Ms, etc.; apply to specified columns
    // var numsepFormat = new google.visualization.NumberFormat({pattern: '#,###'});
    // numsepFormat.format(data, 3); // Circulation
    // numsepFormat.format(data, 4); // Rank

    // Print number with to one decimal place; apply to specified columns
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 2);
    numdecFormat.format(data, 3);
    numdecFormat.format(data, 4);
    numdecFormat.format(data, 5);
    numdecFormat.format(data, 6);
    numdecFormat.format(data, 7);

    var options = {
        sortColumn: 7,
        sortAscending: false,
        showRowNumber: true,
        width: '100%',
        height: '100%',
    };

    var table = new google.visualization.Table(document.getElementById('table_owners'));

    table.draw(data, options);
}
</script>
        </main><!-- #main -->

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<!-- =file: page-sitemap -->
<?php get_footer(); ?>

