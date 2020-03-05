<nav id="navbar" class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
	<div class="container">
	  <a class="navbar-brand" href="/">My Store</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-content" aria-controls="navbar-content" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>

	  <div id="navbar-content" class="collapse navbar-collapse justify-content-between">
	    <form action="<?php echo path( '/' . __( 'products' ) . '/' ) ?>" data-action="<?php echo LOCALHOST ? API_LOCAL_URI : API_REMOTE_URI ?>" class="form-inline">
        <div class="dropdown">
          <input class="form-control mr-sm-2" type="search" name="q" value="<?php echo inline_q_var( $params ) ?>" data-search="product/suggest" data-target="#product-search-results" placeholder="Su búsqueda..." autocomplete="off" aria-label="Search">
          <div class="dropdown-menu product-dropdown-results" role="menu" id="product-search-results"></div>
          <script type="text/template" id="product-search-results__template">
          {{ #category }}
          <a class="dropdown-item" data-query-var="<?php echo __( 'category' ) . '={{ id }}' ?>" data-toggle="query-var" href="/<?php echo __( 'products' ) ?>/" data-item-type="suggestion">
              <strong>#{{ text }}</strong>
          </a>
          {{ /category }}
          {{ #line }}
          <a class="dropdown-item" data-query-var="<?php echo __( 'line' ) . '={{ id }}' ?>" data-toggle="query-var" href="/<?php echo __( 'products' ) ?>/" data-item-type="suggestion">
              <strong>#{{ text }}</strong>
          </a>
          {{ /line }}
          {{ #name }}
          <a class="dropdown-item" data-query-var="<?php echo __( 'q' ) . '=&quot;{{ text }}&quot;' ?>" data-toggle="query-var"  href="/<?php echo __( 'products' ) ?>/" data-item-type="suggestion">
              <span>{{ text }}</span>
          </a>
          {{ /name }}
          </script>
        </div>
	      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        <?php if ( count( $params ) && isset( $params_options ) && is_array( $params_options ) ) : ?>
        <ul class="query-vars">
          <?php foreach ( $params as $key => $val ) :
            if ( isset( $params_options[ $key ] ) && is_array( $params_options[ $key ] ) && isset( $params_options[ $key ][ $val ] ) ) : ?>
          <li class="var">
            <?php echo htmlspecialchars( $params_options[ $key ][ $val ] ) ?>
            <button type="button" data-query-var="<?php echo __( $key ) . '=' . $val ?>" data-dismiss="query-var" class="close" aria-label="Remove">
              <span aria-hidden="true">&times;</span>
            </button>
          </li>
          <?php endif;
          endforeach ?>
        </ul>
        <?php endif ?>
	    </form>

	    <ul class="navbar-nav align-self-end">
	      <li class="nav-item active">
	        <a class="nav-link" href="#">Personas <span class="sr-only">(current)</span></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="/item-1/">Candidaturas</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="/item-2/">Elecciones</a>
	      </li>
	      <li class="nav-item dropdown">
	        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	          Usuario
	        </a>
	        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
	          <a class="dropdown-item" href="/user/item-1/">Perfil</a>
	          <a class="dropdown-item" href="/user/item-2/">Opciones</a>
	          <div class="dropdown-divider"></div>
	          <a class="dropdown-item" href="/user/item-3/">Salir</a>
	        </div>
	      </li>
	    </ul>
	  </div>
  </div>
</nav>
