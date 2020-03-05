/*
 * Updates window.location
 */

import $ from 'jquery';

class QueryVar {

  constructor ( element ) {
    this._element = element;
  }

  remove ( handler ) {
    const query_var = $( handler ).data( 'query-var' );
    let href = window.location.href;
    href = href.replace( query_var + '/', '' );
    window.location.href = href;
  }

  toggle ( handler ) {
    const query_var = $( handler ).data( 'query-var' );
    const var_name = query_var.substr( 0, query_var.indexOf( '=') );
    const regexp = new RegExp( var_name + '=.+\/' );
    const path = handler.href.replace( window.location.origin, '' );
    let href = window.location.href;

    if ( href.indexOf( path ) == -1 ) {
      href = handler.href;
    }

    if ( regexp.test( href ) ) {
      href = href.replace( regexp, query_var + '/' );
    } else {
      href += query_var + '/';
    }
    window.location.href = href;
  }

  static _handleDismiss( varInstance ) {
    return function ( event ) {
      if ( event ) {
        event.preventDefault();
      }

      varInstance.remove( this );
    }
  }

  static _handleToggle( varInstance ) {
    return function ( event ) {
      if ( event ) {
        event.preventDefault();
      }

      varInstance.toggle( this );
    }
  }

  static _jQueryInterface( method ) {
    return this.each( function () {
      let data = $( this ).data( '_query-var' );

      if ( ! data ) {
        data = new QueryVar( this )
        $( this ).data( '_query-var', data )
      }

      if ( typeof method === 'string' ) {
        if ( typeof data[method] === 'undefined' ) {
          throw new TypeError( `No method named "${method}"` )
        }
        data[ method ]()
      }
    });
  }
}

/**
 * ------------------------------------------------------------------------
 * Data Api implementation
 * ------------------------------------------------------------------------
 */
$(document).on(
  'click',
  '[data-dismiss="query-var"]',
  QueryVar._handleDismiss(new QueryVar())
);

$(document).on(
  'click', 
  'a[data-toggle="query-var"]',
  QueryVar._handleToggle(new QueryVar())
);

/**
 * ------------------------------------------------------------------------
 * jQuery
 * ------------------------------------------------------------------------
 */

$.fn.query_var             = QueryVar._jQueryInterface
$.fn.query_var.Constructor = QueryVar

export default QueryVar;