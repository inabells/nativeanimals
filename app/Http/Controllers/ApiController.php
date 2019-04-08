<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\Property;
use App\Models\AnimalType;
use App\Models\AnimalProperty;
use App\Models\Breed;
use App\Models\Grouping;
use App\Models\GroupingMember;
use App\Models\GroupingProperty;
use App\Mortality;
use App\Sale;
use App\RemovedAnimal;
use App\Uploads;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Http\Controllers\HelperController;
use Input;
use PDF;


class ApiController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function($request, $next){
                $this->user = Auth::user();
                return $next($request);
        });
    }

    public function getAllPigs(){
        return Farm::get();  
    }

    public function fetchNewPigRecord(Request $request){ // function to add new pig
        $farm = Farm::find($request->farmable_id);
        $breed = Breed::find($request->breedable_id);
        $pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $breed->id)
                    ->pluck('registryid')
                    ->toArray();    

        $temp_earnotch = $request->earnotch;
        $registrationid = "";
        if(strlen($temp_earnotch) > 6){
            $message = "Earnotch is up to 6 characters only!";
            return view('pigs.addpig')->withError($message);
        }
        else{
            if(strlen($temp_earnotch) == 6){
                $earnotch = $temp_earnotch;
            }
            else{
                $earnotch = str_pad($temp_earnotch, 6, "0", STR_PAD_LEFT);
            }
        }
        $sex = $request->sex;

        if(is_null($request->date_farrowed)){
            $registrationid = $farm->code.$breed->breed."-".$sex.$earnotch;
        }
        else{
            $birthdayValue = new Carbon($request->date_farrowed);
            $registrationid = $farm->code.$breed->breed."-".$birthdayValue->year.$sex.$earnotch;
        }


        $conflict = [];
        foreach ($pigs as $pig) {
            if($pig == $registrationid){
                array_push($conflict, "1");
            }
            else{
                array_push($conflict, "0");
            }
        }

        // dd($conflict, in_array("1", $conflict, false));

        if(!in_array("1", $conflict, false)){
            $newpig = new Animal;
            $newpig->animaltype_id = 3;
            $newpig->registryid = $registrationid;
            $newpig->farm_id = $farm->id;
            $newpig->breed_id = $breed->id;
            $newpig->status = $request->status;
            $newpig->save();

            $earnotchproperty = new AnimalProperty;
            $earnotchproperty->animal_id = $newpig->id;
            $earnotchproperty->property_id = 1;
            $earnotchproperty->value = $earnotch;
            $earnotchproperty->save();

            $sex = new AnimalProperty;
            $sex->animal_id = $newpig->id;
            $sex->property_id = 2;
            $sex->value = $request->sex;
            $sex->save();

            if(is_null($request->date_farrowed)){
                $bdayValue = "Not specified";
            }
            else{
                $bdayValue = $request->date_farrowed;
            }

            $birthdayproperty = new AnimalProperty;
            $birthdayproperty->animal_id = $newpig->id;
            $birthdayproperty->property_id = 3;
            $birthdayproperty->value = $bdayValue;
            $birthdayproperty->save();

            $registrationidproperty = new AnimalProperty;
            $registrationidproperty->animal_id = $newpig->id;
            $registrationidproperty->property_id = 4;
            $registrationidproperty->value = $registrationid;
            $registrationidproperty->save();


            if(is_null($request->birth_weight)){
                $birthWeightValue = "";
            }
            else{
                $birthWeightValue = $request->birth_weight;
            }

            $birthweight = new AnimalProperty;
            $birthweight->animal_id = $newpig->id;
            $birthweight->property_id = 5;
            $birthweight->value = $birthWeightValue;
            $birthweight->save();

            if(is_null($request->date_weaned)){
                $dateWeanedValue = "Not specified";
            }
            else{
                $dateWeanedValue = $request->date_weaned;
            }

            $date_weaned = new AnimalProperty;
            $date_weaned->animal_id = $newpig->id;
            $date_weaned->property_id = 6;
            $date_weaned->value = $dateWeanedValue;
            $date_weaned->save();

            if(is_null($request->weaning_weight)){
                $weaningWeightValue = "";
            }
            else{
                $weaningWeightValue = $request->weaning_weight;
            }

            $weaningweight = new AnimalProperty;
            $weaningweight->animal_id = $newpig->id;
            $weaningweight->property_id = 7;
            $weaningweight->value = $weaningWeightValue;
            $weaningweight->save();

            $pigs = Animal::where("animaltype_id", 3)->where("breed_id", $breed->id)->get();
            $founddam = 0;
            $foundsire = 0;

            if(!is_null($request->dam) && !is_null($request->sire)){
                $grouping = new Grouping;
                $temp_earnotch_dam = $request->dam;
                if(strlen($temp_earnotch_dam) == 6){
                    $earnotch_dam = $temp_earnotch_dam;
                }
                else{
                    $earnotch_dam = str_pad($temp_earnotch_dam, 6, "0", STR_PAD_LEFT);
                }
                $temp_earnotch_sire = $request->sire;
                if(strlen($temp_earnotch_sire) == 6){
                    $earnotch_sire = $temp_earnotch_sire;
                }
                else{
                    $earnotch_sire = str_pad($temp_earnotch_sire, 6, "0", STR_PAD_LEFT);
                }

                foreach ($pigs as $pig) { // searches database for pig with same earnotch
                    if(substr($pig->registryid, -6, 6) == $earnotch_dam){
                        $grouping->registryid = $pig->registryid;
                        $grouping->mother_id = $pig->id;
                        $founddam = 1;
                    }
                    if(substr($pig->registryid, -6, 6) == $earnotch_sire){
                        $grouping->father_id = $pig->id;
                        $foundsire = 1;
                    }
                }

                // if dam and/or father are not in the database, it will just be the new pig's property
                if($founddam != 1){
                    $dam = new AnimalProperty;
                    $dam->animal_id = $newpig->id;
                    $dam->property_id = 8;
                    $dam->value = $farm->code.$breed->breed."-"."F".$earnotch_dam;
                    $dam->save();
                }
                if($foundsire != 1){
                    $sire = new AnimalProperty;
                    $sire->animal_id = $newpig->id;
                    $sire->property_id = 9;
                    $sire->value = $farm->code.$breed->breed."-"."M".$earnotch_sire;
                    $sire->save();
                }
                // if parents are found, this will create a new breeding record available for viewing in the Breeding Records page
                if($founddam == 1 || $foundsire == 1){
                    $grouping->breed_id = $breed->id;
                    $grouping->members = 1;
                    $grouping->save();

                    $groupingmember = new GroupingMember;
                    $groupingmember->grouping_id = $grouping->id;
                    $groupingmember->animal_id = $newpig->id;
                    $groupingmember->save();

                    if(!is_null($request->date_farrowed)){
                        $farrowed = new GroupingProperty;
                        $farrowed->grouping_id = $grouping->id;
                        $farrowed->property_id = 3;
                        $farrowed->value = $request->date_farrowed;
                        $farrowed->save();

                        $dateFarrowedValue = new Carbon($request->date_farrowed);

                        $date_bred = new GroupingProperty;
                        $date_bred->grouping_id = $grouping->id;
                        $date_bred->property_id = 42;
                        $date_bred->value = $dateFarrowedValue->subDays(114);
                        $date_bred->save();

                        $edf = new GroupingProperty;
                        $edf->grouping_id = $grouping->id;
                        $edf->property_id = 43;
                        $edf->value = $request->date_farrowed;
                        $edf->save();

                        $status = new GroupingProperty;
                        $status->grouping_id = $grouping->id;
                        $status->property_id = 60;
                        $status->value = "Farrowed";
                        $status->save();

                        if(is_null($request->date_weaned)){
                            $dateWeanedValue = "Not specified";
                        }
                        else{
                            $dateWeanedValue = $request->date_weaned;
                        }

                        $date_weaned = new GroupingProperty;
                        $date_weaned->grouping_id = $grouping->id;
                        $date_weaned->property_id = 6;
                        $date_weaned->value = $dateWeanedValue;
                        $date_weaned->save();

                    }
                }
            }
            echo "Successfully added new pig!";
        }
        else{
            echo "Registration ID already exists!";
        }
    }

    static function addPonderalIndices($farmable_id, $breedable_id){
        $farm = Farm::find($farmable_id);
        $breed = Breed::find($breedable_id);
        $pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $breed->id)
                    ->where("status", "breeder")
                    ->get();

        // computes ponderal index = weight at 180 days divided by body length converted to meters cube
        $ponderalIndexValue = 0;
        if(!is_null($pigs)){
            foreach ($pigs as $pig) {
                $properties = $pig->getAnimalProperties();
                foreach ($properties as $property) {
                    if($property->property_id == 25){
                        if(!is_null($property) && $property->value != ""){
                            $bodylength = $property->value;
                            $bodyweight180dprop = $pig->getAnimalProperties()->where("property_id", 36)->first();
                            if(!is_null($bodyweight180dprop) && $bodyweight180dprop->value != ""){
                                $ponderalIndexValue = $bodyweight180dprop->value/(($property->value/100)**3); 
                            }
                            else{
                                $ponderalIndexValue = "";
                            }
                        }
                        else{
                            $ponderalIndexValue = "";
                        }
                    }
                }
                $ponderalprop = $pig->getAnimalProperties()->where("property_id", 31)->first();
                if(is_null($ponderalprop)){
                    $ponderalindex = new AnimalProperty;
                    $ponderalindex->animal_id = $pig->id;
                    $ponderalindex->property_id = 31;
                    $ponderalindex->value = $ponderalIndexValue;
                    $ponderalindex->save();
                }
                else{
                    $ponderalprop->value = $ponderalIndexValue;
                    $ponderalprop->save();
                }
            }
        }
    }

    public function getAllSows(Request $request)
    {
        $pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $request->breedable_id)
                    ->where("status", "breeder")
                    ->get();
        $archived_pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $request->breedable_id)
                    ->where(function ($query){$query->where("status", "dead breeder")
                                                    ->orWhere("status", "sold breeder")
                                                    ->orWhere("status", "removed breeder");
                                                    })
                    ->get();
        $sows = [];
        foreach($pigs as $pig){
            if(substr($pig->registryid, -7, 1) == 'F') 
                array_push($sows, $pig);
        }

        $archived_sows = [];
        foreach ($archived_pigs as $archived_pig) {
            if(substr($archived_pig->registryid, -7, 1) == 'F')
                array_push($archived_sows, $archived_pig);
            
        }
        return json_encode($sows);

        // static::addPonderalIndices($farm->id, $breed->id);
    }

    public function getAllBoars(Request $request)
    {
        $farm = Farm::find($request->farmable_id);
        $breed = Breed::find($request->breedable_id);
        $pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $breed->id)
                    ->where("status", "breeder")
                    ->get();

        $archived_pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $breed->id)
                    ->where(function ($query){$query->where("status", "dead breeder")
                                                    ->orWhere("status", "sold breeder")
                                                    ->orWhere("status", "removed breeder");
                                                    })
                    ->get();
        $boars = [];
        foreach($pigs as $pig){
            if(substr($pig->registryid, -7, 1) == 'M')
                array_push($boars, $pig);  
        }

        $archived_boars = [];

        foreach ($archived_pigs as $archived_pig) {
            if(substr($archived_pig->registryid, -7, 1) == 'M')
                array_push($archived_boars, $archived_pig);
            
        }
        return json_encode($boars);
        //static::addPonderalIndices($farm->id, $breed->id);
    }

     public function getAllFemaleGrowers(Request $request)
    {
        $farm = Farm::find($request->farmable_id);
        $breed = Breed::find($request->breedable_id);
        // $q = $request->q;
        $pigs = Animal::where("animaltype_id", 3)
                    ->where("breed_id", $breed->id)
                    ->where("status", "active")
                    ->get();

        $sows = [];
    
        foreach($pigs as $pig){
            if(substr($pig->registryid, -7, 1) == 'F')
                array_push($sows, $pig); 
        }

        // if($q != ' '){
        //     $growers = Animal::where("animaltype_id", 3)->where("breed_id", $breed->id)->where("status", "active")->where('registryid', 'LIKE', '%'.$q.'%')->get();
        //     // dd($growers);
        //     if(count($growers) > 0){
        //         return view('pigs.growerrecords', compact('pigs', 'sows', 'boars'))->withDetails($growers)->withQuery($q);
        //     }
        // }
        return json_encode($sows);
    }

    public function getAllMaleGrowers(Request $request)
    {
        $farm = Farm::find($request->farmable_id);
        $breed = Breed::find($request->breedable_id);
        // $q = $request->q;
        $pigs = Animal::where("animaltype_id", 3)
                        ->where("breed_id", $breed->id)
                        ->where("status", "active")
                        ->get();
        $boars = [];

        foreach($pigs as $pig){
            if(substr($pig->registryid, -7, 1) == 'M')
                array_push($boars, $pig);
        }
        // if($q != ' '){
        //     $growers = Animal::where("animaltype_id", 3)->where("breed_id", $breed->id)->where("status", "active")->where('registryid', 'LIKE', '%'.$q.'%')->get();
        //     // dd($growers);
        //     if(count($growers) > 0){
        //         return view('pigs.growerrecords', compact('pigs', 'sows', 'boars'))->withDetails($growers)->withQuery($q);
        //     }
        // }
        return json_encode($boars);
    }
    
    public function getViewSowPage(Request $request){ // function to display View Sow page
        // $sow = Animal::find($request->id);
        $sow = Animal::where("registryid", $request->registry_id);
        $properties = $sow->getAnimalProperties();
        // $properties = AnimalProperty::where('animal_id', $sow->id)->get();


        // $photo = Uploads::where("animal_id", $id)->first();

        // computes current age
        $now = Carbon::now();
        if(!is_null($properties->where("property_id", 3)->first())){
            if($properties->where("property_id", 3)->first()->value == "Not specified"){
                $age = "";
            }
            else{
                $end_date = Carbon::parse($properties->where("property_id", 3)->first()->value);
                $age = $now->diffInMonths($end_date);
            }
        }
        else{
            $age = "";
        }

        // computes age at weaning
        if(!is_null($properties->where("property_id", 3)->first()) && !is_null($properties->where("property_id", 6)->first())){
            if($properties->where("property_id", 3)->first()->value == "Not specified" || $properties->where("property_id", 6)->first()->value == "Not specified"){
                $ageAtWeaning = "";
            }
            else{
                $start_weaned = Carbon::parse($properties->where("property_id", 3)->first()->value);
                $end_weaned = Carbon::parse($properties->where("property_id", 6)->first()->value);
                $ageAtWeaning = $end_weaned->diffInMonths($start_weaned);
            }
        }
        else{
            $ageAtWeaning = "";
        }

        // computes age at first mating (only those with data of 1st parity)
        $frequency = $sow->getAnimalProperties()->where("property_id", 61)->first();
        $dates_bred = [];
        if(!is_null($frequency)){
            if($frequency->value > 1){
                $groups = Grouping::where("mother_id", $sow->id)->get();
                foreach ($groups as $group) {
                    $groupingproperties = $group->getGroupingProperties();
                    foreach ($groupingproperties as $groupingproperty) {
                        if($groupingproperty->property_id == 48){ //parity
                            if($groupingproperty->value == 1){
                                $date_bred = $group->getGroupingProperties()->where("property_id", 42)->first();
                                if(!is_null($date_bred) && $date_bred->value != "Not specified"){
                                    if(!is_null($sow->getAnimalProperties()->where("property_id", 3)->first()) && $sow->getAnimalProperties()->where("property_id", 3)->first()->value != "Not specified"){
                                        $bday = $sow->getAnimalProperties()->where("property_id", 3)->first()->value;
                                        $ageAtFirstMating = Carbon::parse($date_bred->value)->diffInMonths(Carbon::parse($bday));
                                    }
                                    else{
                                        $ageAtFirstMating = "";
                                    }
                                }
                                else{
                                    $ageAtFirstMating = "";
                                }
                            }
                            else{
                                $ageAtFirstMating = "";
                            }
                        }
                    }
                }
            }
            else{
                $ageAtFirstMating = "";
            }
        }
        else{
            $ageAtFirstMating = "";
        }

        // gets the sex ratio
        $family = $sow->getGrouping();
        if(!is_null($family)){
            $familymembers = $family->getGroupingMembers();
            $males = [];
            $females = [];
            foreach ($familymembers as $familymember) {
                $familymemberproperties = $familymember->getAnimalProperties();
                foreach ($familymemberproperties as $familymemberproperty) {
                    if($familymemberproperty->property_id == 2){
                        if($familymemberproperty->value == 'M'){
                            array_push($males, $familymember->getChild());
                        }
                        elseif($familymemberproperty->value == 'F'){
                            array_push($females, $familymember->getChild());
                        }
                    }
                }
            }
            $paritybornprop = $family->getGroupingProperties()->where("property_id", 48)->first();
            if(is_null($paritybornprop)){
                $parity_born = "";
            }
            else{
                $parity_born = $paritybornprop->value;
            }
        }
        else{
            $parity_born = "";
        }

        $grossmorphotakenprop = $properties->where("property_id", 10)->first();
        $morphocharstakenprop = $properties->where("property_id", 21)->first();
        $bday = $properties->where("property_id", 3)->first();
        if(!is_null($bday) && $bday->value != "Not specified"){
            $bdayValue = Carbon::parse($bday->value);
            if(!is_null($grossmorphotakenprop)){
                $grossmorphotaken = Carbon::parse($grossmorphotakenprop->value);
                $age_grossmorpho = $grossmorphotaken->diffInDays($bdayValue);
            }
            else{
                $age_grossmorpho = "";
            }
            if(!is_null($morphocharstakenprop)){
                $morphocharstaken = Carbon::parse($morphocharstakenprop->value);
                $age_morphochars = $morphocharstaken->diffInDays($bdayValue);
            }
            else{
                $age_morphochars = "";
            }
        }
        else{
            $age_grossmorpho = "";
            $age_morphochars = "";
        }

        return json_encode(compact('boar', 'properties', 'age', 'ageAtWeaning', 'ageAtFirstMating', 'males', 'females', 'parity_born', 'age_grossmorpho', 'age_morphochars', 'photo'));
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
