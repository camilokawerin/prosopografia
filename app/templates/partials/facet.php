<?php if ( has_rows() ) : ?>
  <aside>
    <h2><?php echo $title ?></h2>
    <ul>
      <?php while ( $row = fetch_row() ) : ?>
      <li>
        <a href="<?php echo get_link( $link_format, $row->category_id ) ?>">
          <?php echo $row->category_name ?> <small>(<?php echo $row->total ?>)</small>
        </a>
      </li>
      <?php endwhile ?>
    </ul>
  </aside>
<?php endif ?>