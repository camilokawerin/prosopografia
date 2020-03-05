import $ from 'jquery';
import LazyLoad from 'vanilla-lazyload';

const options = {
  elements_selector: 'img[data-src]'
};

$( ( e ) => {
  const lazyload = new LazyLoad( options );
  $( document ).data( '_lazyload', lazyload );
} );

$( document ).ajaxComplete( ( e ) => {
  let lazyload = $( this ).data( '_lazyload' );

  if ( ! lazyload ) {
    lazyload = new LazyLoad( options );
    $( this ).data( '_lazyload', lazyload );
  }

  lazyload.update();
} );