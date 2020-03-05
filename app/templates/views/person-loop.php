  <?php if ( has_rows() ) : ?>
    <h1><?php echo $entity::title() ?></h1>
    <table class="table">
      <thead>
        <th>ID</th>
        <th>Nombre</th>
        <th>Género</th>
        <th>Título</th>
        <th>Candidaturas</th>
      </thead>
      <tbody>
      <?php
      while ( $row = fetch_row() ) :
        ?>
        <tr>
          <td><?php echo $row->id ?></td>
          <th><?php echo $row->surname . ', ' . $row->name ?></th>
          <td><?php echo $row->gender ?></td>
          <td><?php echo $row->title ?></td>
          <td><?php var_dump( $row->candidacy ) ?></td>
        </tr>
        <?php
      endwhile;
      ?>
      </tbody>
    </table>
  <?php endif ?>