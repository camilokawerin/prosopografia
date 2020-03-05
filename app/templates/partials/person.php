<?php if ( has_rows() ) : ?>
  <section class="products">
    <h2><?php echo $title ?></h2>
    <div class="content">
    <?php
    while ( $row = fetch_row() ) :
    ?>
      <article class="item">
        <a class="link" href="<?php echo sprintf( $link_format, $row->id ); ?>">
          <span class="image-container">
          <?php if ( $image = fetch_image( sprintf( $image_format, $row->imagen ) ) ) : ?>
            <img class="image" src="/<?php echo $image->file ?>" width="<?php echo $image->width ?>" height="<?php echo $image->height ?>" alt="">
          <?php else : ?>
            <img class="image-placeholder" src="/assets/dist/img/image-placeholder.png" height="250" width="250" alt="">
          <?php endif ?>
          </span>
          <strong class="name">
            <?php echo $row->name; ?>
          </strong>
          <span class="price">
            <del><?php echo price_format( $row->price ); ?></del>
            <strong><?php echo price_format( $row->sale_price  ); ?></strong>
          </span>
        </a>
      </article>
    <?php
    endwhile; 
    ?>
    </div>
  </section><!-- /.home-products -->
<?php endif ?>