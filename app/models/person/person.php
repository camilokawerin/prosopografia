<?php
/**
 * Persons
 *
 */
namespace Person;

class Person extends \Model\Model {

  public $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  public $surname = [ 'Type\Text', [ 'label' => 'Surname' ] ];
  public $name = [ 'Type\Text', [ 'label' => 'Name' ] ];
  public $gender = [ 'Type\Set', [ 'label' => 'Gender', 'options' => [ 'M', 'F' ] ] ];
  public $title = [ 'Type\Text', [ 'label' => 'Title' ] ];
  public $other = [ 'Type\Text', [ 'label' => 'Other' ] ];
  public $alt_name = [ 'Type\Text', [ 'label' => 'Alternative name' ] ];
  public $alt_surname = [ 'Type\Text', [ 'label' => 'Alternative surname' ] ];
  public $candidacy = [ 'Model\Datalist', [ 'Candidacy\Candidacy', 'id' ], [ 'label' => 'Candidacy' ] ];

  // agregar __construct para setear cada propiedad
  // $this->id = new Type\Number([ 'label' => 'ID' ]);

  public static function title() {
    return 'Personas';
  }
  
}
