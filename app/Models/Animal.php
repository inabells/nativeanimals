<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnimalProperty;
use App\Models\GroupingMember;
use App\Models\Grouping;
use Carbon\Carbon;

class Animal extends Model
{

  protected $table = 'animals';
  protected $fillable = [
      'registryid',
      'status',
      'grossmorpho',
      'morphochars',
      'weightrecord'
  ];

  /*
    Eloquent ORM
  */
  public function farms(){
    return $this->belongsTo('App\Models\Farm');
  }

  public function animaltype()
  {
      return $this->belongsTo('App\Models\AnimalType');
  }

  public function breeds()
  {
    return $this->belongsTo('App\Models\Breed');
  }

  public function groupings()
  {
    return $this->hasMany('App\Models\Grouping');
  }

  public function animalproperties()
  {
    return $this->belongsTo('App\Models\AnimalProperty');
  }

  public function sales()
  {
    return $this->hasOne('App\Models\Sale');
  }

  public function weights()
  {
    return $this->hasMany('App\Models\Weight');
  }

  public function mortalities()
  {
    return $this->hasOne('App\Models\Mortality');
  }

  public function removedanimals()
  {
    return $this->hasOne('App\Models\RemovedAnimal');
  }


  /*
    Model Functions
  */
  public function getAnimalType(){
    return $this->animaltype_id;
  }
  public function getFarmId(){
    return $this->farm_id;
  }

  public function getBreedId(){
    return $this->breed_id;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function getGrossMorpho()
  {
    return $this->grossmorpho;
  }

  public function getMorphoChars()
  {
    return $this->morphochars;
  }

  public function getWeightRecord()
  {
    return $this->weightrecord;
  }

  public function setAnimalType($animaltype_id){
    $this->animaltype = $animaltype_id;
  }

  public function setBreed($breed_id){
    $this->breed_id = $breed_id;
  }

  public function setFarm($farm_id){
    $this->farm_id = $farm_id;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function setGrossMorpho($grossmorpho)
  {
    $this->grossmorpho = $grossmorpho;
  }

  public function setMorphoChars($morphochars)
  {
    $this->morphochars = $morphochars;
  }

  public function setWeightRecord($weightrecord)
  {
    $this->weightrecord = $weightrecord;
  }

  public function getAnimalProperties()
  {
    $properties = AnimalProperty::where('animal_id', $this->id)->get();
    return $properties;
  }

  public function getAge($id)
  {
    $animal = Animal::find($id);

    if($animal->status == "sold grower" || $animal->status == "sold breeder"){
      $date_start = Carbon::parse($animal->getAnimalProperties()->where("property_id", 56)->first()->value);
    }
    if($animal->status == "dead grower" || $animal->status == "dead breeder"){
      $date_start = Carbon::parse($animal->getAnimalProperties()->where("property_id", 55)->first()->value);
    }
    if($animal->status == "removed"){
     $date_start = Carbon::parse($animal->getAnimalProperties()->where("property_id", 72)->first()->value); 
    }

    if(!is_null($animal->getAnimalProperties()->where("property_id", 25)->first())){
      if($animal->getAnimalProperties()->where("property_id", 25)->first()->value == "" || $animal->getAnimalProperties()->where("property_id", 25)->first()->value == "Not specified"){
        // dd($animal->getAnimalProperties()->where("property_id", 25)->first()->value);
        $age = "Age unavailable";
      }
      else{
        $date_end = Carbon::parse($animal->getAnimalProperties()->where("property_id", 25)->first()->value);
        $age = $date_start->diffInDays($date_end);
      }
    }
    else{
      $age = "Age unavailable";
    }

    return $age;
  }

  public function getGrouping(){
    $member = GroupingMember::where('animal_id', $this->id)->first();

    if(!is_null($member)){
      $group = Grouping::find($member->grouping_id);
      return $group;
    }

  }

}
