<?php 
get_template_part( 'head' );
get_template_part( 'header', [ 'params' => $params ] );
?>
<main>
  <div class="container">
    <?php
    /*
     * Hit: add DEBUG_SQL to request arguments to see SQL and results
     */
    get_content( $paths, 'Person\Person', $params );
    ?>
  </div><!-- /.container -->
</main>
<?php get_template_part( 'footer' ); ?>