<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();

?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', 'page' ); ?>

        </main><!-- #main -->

        <figure id="table_div" style="display: block; padding-top: 30px; width: 100%"></figure>

        <?php endwhile; // End of the loop. ?>
            <?php $pubs_data = netrics_get_pubs_query_data(); ?>
            <table class="tabular" style="margin-top: 2rem;">
                <caption>All U.S. daily newspapers: Averages of Google Pagespeed results (2019-08)</caption>
                <?php netrics_pagespeed_mean( $pubs_data ); ?>
                <tfoot>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;">3,073 articles from 1,043 newspapers</td>
                    </tr>
                </tfoot>
            </table>

<p class="content-col">(FYI, this page's <a href="https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fnews.pubmedia.us%2Fresults%2F&amp;tab=desktop">PSI scores</a> are 69 mobile and 91 desktop.)</p>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current');
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // var table = new google.visualization.Table(document.getElementById('table_div'));

    // Data cols and rows.
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Domain');
        data.addColumn('string', 'Paper&nbsp;&nbsp;&nbsp; &mdash; &nbsp;&nbsp;results link: &#9432;');
        data.addColumn('number', 'Circ.');
        data.addColumn('number', 'Rank');
        data.addColumn('string', 'State');
        data.addColumn('string', 'City');
        data.addColumn('number', 'Population');
        data.addColumn('string', 'Owner');
        data.addColumn('string', 'CMS');
        data.addColumn('number', 'DOM');
        data.addColumn('number', 'Requests');
        data.addColumn('number', 'Size (MB)');
        data.addColumn('number', 'Speed (s)');
        data.addColumn('number', 'TTI (s)');
        data.addColumn('number', 'Score');
        data.addRows([
[{v:'amny.com',f:'<a href="https://www.amny.com/">amny.com</a>'},'amNew York <a class="info-link" href="https://news.pubmedia.us/publication/amny-com/">&#9432;</a>',184583,57231,'NY','New York',19354922,'Newsday Media','',347.7,99,1.3,5.7,14.8,43.7],
[{v:'sandiegouniontribune.com',f:'<a href="https://www.sandiegouniontribune.com/">sandiegouniontribune.com</a>'},'The San Diego Union-Tribune <a class="info-link" href="https://news.pubmedia.us/publication/sandiegouniontribune-com/">&#9432;</a>',153132,22217,'CA','San Diego',3210314,'Nant Capital, LLC','',1542.7,239,2.0,6.8,22,36],
[{v:'dailygazette.com',f:'<a href="https://dailygazette.com/">dailygazette.com</a>'},'The Daily Gazette <a class="info-link" href="https://news.pubmedia.us/publication/dailygazette-com/">&#9432;</a>',52202,150184,'NY','Schenectady',65625,'The Daily Gazette Co.','Drupal',715,119.7,2.9,10.8,17.6,32],
[{v:'washingtontimes.com',f:'<a href="https://www.washingtontimes.com/">washingtontimes.com</a>'},'The Washington Times <a class="info-link" href="https://news.pubmedia.us/publication/washingtontimes-com/">&#9432;</a>',67148,9585,'DC','Washington',5289420,'The Washington Times LLC','',1618,324.7,2.4,14,33.6,30.7],
[{v:'nytimes.com',f:'<a href="https://www.nytimes.com/">nytimes.com</a>'},'The New York Times <a class="info-link" href="https://news.pubmedia.us/publication/nytimes-com/">&#9432;</a>',513776,117,'NY','New York',19354922,'The New York Times Co.','',756.7,200.7,4.6,13.6,35,29.3],
[{v:'latimes.com',f:'<a href="https://www.latimes.com/">latimes.com</a>'},'Los Angeles Times <a class="info-link" href="https://news.pubmedia.us/publication/latimes-com/">&#9432;</a>',449959,1490,'CA','Los Angeles',3990456,'Nant Capital, LLC','',1303,466,4.4,15.3,43.2,28],
[{v:'washingtonpost.com',f:'<a href="https://www.washingtonpost.com/">washingtonpost.com</a>'},'The Washington Post <a class="info-link" href="https://news.pubmedia.us/publication/washingtonpost-com/">&#9432;</a>',349586,282,'DC','Washington',5289420,'Nash Holdings','',550.3,210.3,4.2,11.5,28.3,28],
[{v:'baltimoresun.com',f:'<a href="https://www.baltimoresun.com/">baltimoresun.com</a>'},'The Baltimore Sun <a class="info-link" href="https://news.pubmedia.us/publication/baltimoresun-com/">&#9432;</a>',106443,15097,'MD','Baltimore',2170504,'Tribune Publishing Co.','',545,811,14.6,15.8,36.7,27],
[{v:'spokesman.com',f:'<a href="http://www.spokesman.com/">spokesman.com</a>'},'The Spokesman-Review <a class="info-link" href="https://news.pubmedia.us/publication/spokesman-com/">&#9432;</a>',71127,36365,'WA','Spokane',403043,'Cowles Company','',719,225.7,3.7,13,25.9,27],
[{v:'syracuse.com',f:'<a href="https://www.syracuse.com/">syracuse.com</a>'},'The Post-Standard <a class="info-link" href="https://news.pubmedia.us/publication/syracuse-com/">&#9432;</a>',69951,17472,'NY','Syracuse',407259,'Advance Publications, Inc.','',843,431,4.9,23.4,41.9,27],
[{v:'abqjournal.com',f:'<a href="https://www.abqjournal.com/">abqjournal.com</a>'},'Albuquerque Journal <a class="info-link" href="https://news.pubmedia.us/publication/abqjournal-com/">&#9432;</a>',83795,74977,'NM','Albuquerque',758523,'Albuquerque Publishing Company','',872,196,2.4,25.4,19.4,26],
[{v:'post-gazette.com',f:'<a href="https://www.post-gazette.com/">post-gazette.com</a>'},'Pittsburgh Post-Gazette <a class="info-link" href="https://news.pubmedia.us/publication/post-gazette-com/">&#9432;</a>',153738,14327,'PA','Pittsburgh',301048,'Block Communications, Inc.','',1360,604.3,3.9,13.4,35.8,24.7],
[{v:'newsday.com',f:'<a href="https://www.newsday.com/">newsday.com</a>'},'Newsday <a class="info-link" href="https://news.pubmedia.us/publication/newsday-com/">&#9432;</a>',309047,14957,'NY','Melville',18985,'Cablevision Systems Corporation','',791.5,298.5,2.4,19,24.8,24.5],
[{v:'toledoblade.com',f:'<a href="https://www.toledoblade.com/">toledoblade.com</a>'},'The Blade <a class="info-link" href="https://news.pubmedia.us/publication/toledoblade-com/">&#9432;</a>',69118,77036,'OH','Toledo',488672,'Block Communications, Inc.','',1302.3,484,4.3,12,29.6,24.3],
[{v:'dallasnews.com',f:'<a href="http://www.dallasnews.com/">dallasnews.com</a>'},'The Dallas Morning News <a class="info-link" href="https://news.pubmedia.us/publication/dallasnews-com/">&#9432;</a>',202480,11025,'TX','Dallas',5733259,'A.H. Belo Corporation','',1495,689,8.8,20.7,56.6,24],
[{v:'providencejournal.com',f:'<a href="https://www.providencejournal.com/">providencejournal.com</a>'},'The Providence Journal <a class="info-link" href="https://news.pubmedia.us/publication/providencejournal-com/">&#9432;</a>',64432,59479,'RI','Providence',1206642,'GateHouse Media','NewsCycle',1147.7,477,6.0,20.6,47.2,24],
[{v:'wsj.com',f:'<a href="https://www.wsj.com/">wsj.com</a>'},'The Wall Street Journal <a class="info-link" href="https://news.pubmedia.us/publication/wsj-com/">&#9432;</a>',1111167,628,'NY','New York',19354922,'Dow Jones & Company','',636,275.7,3.5,12.5,31.1,23.3],
[{v:'metro.us',f:'<a href="https://www.metro.us/">metro.us</a>'},'Metro New York <a class="info-link" href="https://news.pubmedia.us/publication/metro-us/">&#9432;</a>',186185,59071,'NY','New York',19354922,'SB New York','Drupal',1668.3,345.3,3.2,14,24.2,23.3],
[{v:'cleveland.com',f:'<a href="https://www.cleveland.com/">cleveland.com</a>'},'The Plain Dealer <a class="info-link" href="https://news.pubmedia.us/publication/cleveland-com/">&#9432;</a>',171919,11222,'OH','Brooklyn',10792,'Advance Publications, Inc.','',1096,418,6.9,21.8,40.2,23],
[{v:'oregonlive.com',f:'<a href="https://www.oregonlive.com/">oregonlive.com</a>'},'The Oregonian <a class="info-link" href="https://news.pubmedia.us/publication/oregonlive-com/">&#9432;</a>',128141,8034,'OR','Portland',2052796,'Advance Publications, Inc.','',906,300,3.9,12.3,30.3,22],
[{v:'columbiatribune.com',f:'<a href="https://www.columbiatribune.com/">columbiatribune.com</a>'},'Columbia Daily Tribune <a class="info-link" href="https://news.pubmedia.us/publication/columbiatribune-com/">&#9432;</a>',114672,199387,'MO','Columbia',139945,'GateHouse Media','NewsCycle',1314,341.7,4.7,19.9,32.6,22],
[{v:'registerguard.com',f:'<a href="https://www.registerguard.com/">registerguard.com</a>'},'The Register-Guard <a class="info-link" href="https://news.pubmedia.us/publication/registerguard-com/">&#9432;</a>',46247,120912,'OR','Eugene',267568,'GateHouse Media','CMS',1212,361.7,4.6,17.1,35.4,22],
[{v:'palmbeachpost.com',f:'<a href="https://www.palmbeachpost.com/">palmbeachpost.com</a>'},'The Palm Beach Post <a class="info-link" href="https://news.pubmedia.us/publication/palmbeachpost-com/">&#9432;</a>',45054,43765,'FL','West Palm Beach',110222,'GateHouse Media','NewsCycle',1340.3,472,6.6,20.9,47.1,22],
[{v:'dispatch.com',f:'<a href="https://www.dispatch.com/">dispatch.com</a>'},'The Columbus Dispatch <a class="info-link" href="https://news.pubmedia.us/publication/dispatch-com/">&#9432;</a>',105055,41054,'OH','Columbus',1528314,'GateHouse Media','NewsCycle',1837.3,498.7,7.1,18.1,44.9,21.7],
[{v:'augustachronicle.com',f:'<a href="https://www.augustachronicle.com/">augustachronicle.com</a>'},'The Augusta Chronicle <a class="info-link" href="https://news.pubmedia.us/publication/augustachronicle-com/">&#9432;</a>',41663,247490,'GA','Augusta',389383,'GateHouse Media','NewsCycle',1357,359.7,4.7,14.7,33.5,21.7]
    ]);
/*
    // Format number to one decimal place; apply to specified columns.
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 3);
    numdecFormat.format(data, 4);
    numdecFormat.format(data, 5);
    numdecFormat.format(data, 6);
    numdecFormat.format(data, 7);
    numdecFormat.format(data, 8);
    numdecFormat.format(data, 9);

*/
    // Google Visualization: Table chart.
    var wrapper = new google.visualization.ChartWrapper({
        chartType:   'Table',
        containerId: 'table_div',
        dataTable: data,
        options: {
            'allowHtml': true,
            'sortColumn': 14,
            'sortAscending': false,
            'showRowNumber': true,
            'width': '100%',
            'height': '100%',
        },
    });
    // Attach controls to charts.
    wrapper.draw();
}
</script>

    </div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
