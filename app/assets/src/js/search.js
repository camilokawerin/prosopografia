/*
 * Performs requests for getting data from backend
 */

import $ from 'jquery';
import Mustache from 'mustache';
import Request from './request.js';

class Search {

  constructor( element ) {
    this._element  = element;

    const $element = $( this._element );
    const $form = $element.closest( 'form' );
    this._source = $element.data('search');
    this._target = $( $element.data('target') );
    this._template = $( $element.data('target') + '__template').html();
    this._url      = $form.data( 'action' );
    this._timer    = null;

    this._request = new Request( this._url + this._source );
    this._request
      .success( ( data ) => {
        this._target
          .html( Mustache.render( this._template, data ) )
          .removeClass( 'is-requesting' )
          .addClass( 'show' );
        })
      .before( () => {
        this._target.addClass( 'is-requesting' );
      });

    this._addEventListeners();
  }

  search( query ) {
    if ( query.length == 0 ) {
      this._request.reset();
      this._target.html( '' ).removeClass( 'show' );
    }
    this._request.get( query );
  }

  empty() {
    $( this._element ).val( '' ).trigger( 'keyup' );
  }

  // Private

  _addEventListeners() {
    const $element = $( this._element );
    const $form = $element.closest( 'form' );

    $element
      .on( 'change', ( e ) => {
        this.search( $element.val() )
      } )
      .on( 'keyup', ( e ) => {
        if ( this._timer ) {
          clearTimeout( this._timer );
        }

        if ( e.keyCode == 27) {
          this.empty();
        }
        if ( /(38|40|37|39)/.test( e.keyCode ) ) {
          return;
        }
        if ( /^\d+$/.test( $element.val() ) && e.keyCode != 13 ) {
          return;
        }

        this._timer = setTimeout( () => {
          this._timer = null;
          this.search( $element.val() );
        }, 250 );
      } )
  }

  static _jQueryInterface( method ) {
    return this.each( function () {
      let data = $( this ).data( '_search' );

      if ( ! data ) {
        data = new Search( this )
        $( this ).data( '_search', data )
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

$( document ).on( 'focus', 'input[data-search]', function ( e ) {
  Search._jQueryInterface.call( $( this ) );
} );


/**
 * ------------------------------------------------------------------------
 * jQuery
 * ------------------------------------------------------------------------
 */

$.fn.search             = Search._jQueryInterface
$.fn.search.Constructor = Search

export default Search;