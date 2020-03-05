import $ from 'jquery';

class Request {

  constructor( url ) {
    this._url = url;
    this._isRequesting = false;
    this._lastRequest = null;
  }

  get( q ) {
    q = Request._getSafeQuery( q );
    if ( ! q.length || this._isRequesting || q == this._lastRequest ) return;

    $.ajax( {
      url: this._url + '/q=' + q,
      beforeSend: () => {
        this.before();
        this._isRequesting = true;
        this._lastRequest = q;
      },
      success: ( data ) => {
        this.success( [ data ] );
        Request._showErrors( data );
      },
      error: ( err ) => {
        this.error( [ err ] );
        console.log( err )
      },
      complete: () => {
        this.complete();
        this._isRequesting = false;
      }
    } );
  }

  reset() {
    this._lastRequest = null;
    this._isRequesting = false;
  }

  static _showErrors( data ) {
    if ( data.errors ) {
      for ( let i = data.errors.length - 1; i >= 0; i-- ) {
        console.group( data.errors[ i ][0] )
        console.info( data.errors[ i ][1] )
        console.groupEnd()
      };
    }
  }

  static _getSafeQuery( q ) {
    var _q = '',
    match;

    while ( match = q.match(/"(?:\\\\.|[^\\\\"])*"|\S+/) ) {
      _q += match[0].length > 3 ? ( ( _q != '' ? ' ' : '') + match[0] ) : '';
      q = q.replace( match[0], '' );
    }
    return _q;
  }

}

[ 'before', 'success', 'error', 'complete' ].forEach( function ( method ) {
  Request.prototype[ method ] = function ( callback ) {
    if ( typeof callback == 'function' ) {
      this[ '_' + method ] = callback;
    } else if ( typeof this[ '_' + method ] == 'function' ) {
      this[ '_' + method ].apply( this, callback );
    }
    return this;
  }
} );

export default Request;