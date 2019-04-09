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

    public function getAnimalProperties(Request $request){ // function to display Add Gross Morphology page
        $animal = Animal::where("registryid", $request->registry_id)->first();
        $properties = $animal->getAnimalProperties();

        return json_encode(compact('animal', 'properties'));
    }

    public function getViewSowPage(Request $request){ // function to display View Sow page
        $sow = Animal::where("registryid", $request->registry_id)->first();
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

    public function fetchGrossMorphology(Request $request){ // function to add gross morphology data
        $animal = Animal::where("registryid", $request->registry_id)->first();
        $animalid = $animal->id;

        // $animal = Animal::where("registryid", $request->registry_id)->first();
        // $properties = $animal->getAnimalProperties();

        // creates new properties
        $dcgross = $animal->getAnimalProperties()->where("property_id", 10)->first();
        $hairtype = $animal->getAnimalProperties()->where("property_id", 11)->first();
        $hairlength = $animal->getAnimalProperties()->where("property_id", 12)->first();
        $coatcolor = $animal->getAnimalProperties()->where("property_id", 13)->first();
        $colorpattern = $animal->getAnimalProperties()->where("property_id", 14)->first();
        $headshape = $animal->getAnimalProperties()->where("property_id", 15)->first();
        $skintype = $animal->getAnimalProperties()->where("property_id", 16)->first();
        $eartype = $animal->getAnimalProperties()->where("property_id", 17)->first();
        $backline = $animal->getAnimalProperties()->where("property_id", 18)->first();
        $tailtype = $animal->getAnimalProperties()->where("property_id", 19)->first();
        $othermarks = $animal->getAnimalProperties()->where("property_id", 20)->first();

        if($dcgross==null) $dcgross = new AnimalProperty;
        if($hairtype==null) $hairtype = new AnimalProperty;
        if($hairlength==null) $hairlength = new AnimalProperty;
        if($coatcolor==null) $coatcolor = new AnimalProperty;
        if($colorpattern==null) $colorpattern = new AnimalProperty;
        if($headshape==null) $headshape = new AnimalProperty;
        if($skintype==null) $skintype = new AnimalProperty;
        if($eartype==null) $eartype = new AnimalProperty;
        if($backline==null) $backline = new AnimalProperty;
        if($tailtype==null) $tailtype = new AnimalProperty;
        if($othermarks==null) $othermarks = new AnimalProperty;

        if(is_null($request->date_collected_gross)){
            $dateCollectedGrossValue = new Carbon();
            $dateCollectedGrossValue = $dateCollectedGrossValue->format('Y-m-d');
        }
        else{
            $dateCollectedGrossValue = $request->date_collected_gross;
        }

        $dcgross->animal_id = $animalid;
        $dcgross->property_id = 10;
        $dcgross->value = $dateCollectedGrossValue;

        if(is_null($request->hair_type)){
            $hairTypeValue = "Not specified";
        }
        else{
            $hairTypeValue = $request->hair_type;
        }

        $hairtype->animal_id = $animalid;
        $hairtype->property_id = 11;
        $hairtype->value = $hairTypeValue;

        if(is_null($request->hair_length)){
            $hairLengthValue = "Not specified";
        }
        else{
            $hairLengthValue = $request->hair_length;
        }

        $hairlength->animal_id = $animalid;
        $hairlength->property_id = 12;
        $hairlength->value = $hairLengthValue;

        if(is_null($request->coat_color)){
            $coatColorValue = "Not specified";
        }
        else{
            $coatColorValue = $request->coat_color;
        }

        $coatcolor->animal_id = $animalid;
        $coatcolor->property_id = 13;
        $coatcolor->value = $coatColorValue;

        if(is_null($request->color_pattern)){
            $colorPatternValue = "Not specified";
        }
        else{
            $colorPatternValue = $request->color_pattern;
        }

        $colorpattern->animal_id = $animalid;
        $colorpattern->property_id = 14;
        $colorpattern->value = $colorPatternValue;

        if(is_null($request->head_shape)){
            $headShapeValue = "Not specified";
        }
        else{
            $headShapeValue = $request->head_shape;
        }

        $headshape->animal_id = $animalid;
        $headshape->property_id = 15;
        $headshape->value = $headShapeValue;

        if(is_null($request->skin_type)){
            $skinTypeValue = "Not specified";
        }
        else{
            $skinTypeValue = $request->skin_type;
        }

        $skintype->animal_id = $animalid;
        $skintype->property_id = 16;
        $skintype->value = $skinTypeValue;

        if(is_null($request->ear_type)){
            $earTypeValue = "Not specified";
        }
        else{
            $earTypeValue = $request->ear_type;
        }

        $eartype->animal_id = $animalid;
        $eartype->property_id = 17;
        $eartype->value = $earTypeValue;

        if(is_null($request->tail_type)){
            $tailTypeValue = "Not specified";
        }
        else{
            $tailTypeValue = $request->tail_type;
        }

        $tailtype->animal_id = $animalid;
        $tailtype->property_id = 18;
        $tailtype->value = $tailTypeValue;

        if(is_null($request->backline)){
            $backlineValue = "Not specified";
        }
        else{
            $backlineValue = $request->backline;
        }

        $backline->animal_id = $animalid;
        $backline->property_id = 19;
        $backline->value = $backlineValue;

        if(is_null($request->other_marks)){
            $otherMarksValue = "None";
        }
        else{
            $otherMarksValue = $request->other_marks;
        }

        $othermarks->animal_id = $animalid;
        $othermarks->property_id = 20;
        $othermarks->value = $otherMarksValue;

        $dcgross->save();
        $hairtype->save();
        $hairlength->save();
        $coatcolor->save();
        $colorpattern->save();
        $headshape->save();
        $skintype->save();
        $eartype->save();
        $tailtype->save();
        $backline->save();
        $othermarks->save();

        $animal = Animal::find($animalid);
        $animal->grossmorpho = 1;
        $animal->save();

        // if(!is_null($request->display_photo)){
        //     $image = $request->file('display_photo');
        //     $input['image_name'] = $animal->id.'-'.$animal->registryid.'-display-photo'.'.'.$image->getClientOriginalExtension();
        //     $destination = public_path('/images');
        //     $image->move($destination, $input['image_name']);

        //     DB::table('uploads')->insert(['animal_id' => $animal->id, 'animaltype_id' => 3, 'breed_id' => $animal->breed_id, 'filename' => $input['image_name']]);
        // }

        //return Redirect::back()->with('message','Animal record successfully saved');
    }

    public function fetchMorphometricCharacteristics(Request $request){ // function to add morphometric characteristics
        $animal = Animal::where("registryid", $request->registry_id)->first();
        $animalid = $animal->id;

        // creates new properties
        $dcmorpho = $animal->getAnimalProperties()->where("property_id", 21)->first();
        $earlength = $animal->getAnimalProperties()->where("property_id", 22)->first();
        $headlength = $animal->getAnimalProperties()->where("property_id", 23)->first();
        $snoutlength = $animal->getAnimalProperties()->where("property_id", 24)->first();
        $bodylength = $animal->getAnimalProperties()->where("property_id", 25)->first();
        $heartgirth = $animal->getAnimalProperties()->where("property_id", 26)->first();
        $pelvicwidth = $animal->getAnimalProperties()->where("property_id", 27)->first();
        $taillength = $animal->getAnimalProperties()->where("property_id", 28)->first();
        $heightatwithers = $animal->getAnimalProperties()->where("property_id", 29)->first();
        $normalteats = $animal->getAnimalProperties()->where("property_id", 30)->first();
        //$othermarks = $animal->getAnimalProperties()->where("property_id", 20)->first();

        if($dcmorpho==null) $dcmorpho = new AnimalProperty;
        if($earlength==null) $earlength = new AnimalProperty;
        if($headlength==null) $headlength = new AnimalProperty;
        if($bodylength==null) $bodylength = new AnimalProperty;
        if($snoutlength==null) $snoutlength = new AnimalProperty;
        if($heartgirth==null) $heartgirth = new AnimalProperty;
        if($pelvicwidth==null) $pelvicwidth = new AnimalProperty;
        if($taillength==null) $taillength = new AnimalProperty;
        if($heightatwithers==null) $heightatwithers = new AnimalProperty;
        if($normalteats==null) $normalteats = new AnimalProperty;
        //if($othermarks==null) $othermarks = new AnimalProperty;

        if(is_null($request->date_collected_morpho)){
            $dateCollectedMorphoValue = "";
        }
        else{
            $dateCollectedMorphoValue = $request->date_collected_morpho;
        }

        $dcmorpho->animal_id = $animalid;
        $dcmorpho->property_id = 21;
        $dcmorpho->value = $dateCollectedMorphoValue;

        if(is_null($request->ear_length)){
            $earLengthValue = "";
        }
        else{
            $earLengthValue = $request->ear_length;
        }

        $earlength->animal_id = $animalid;
        $earlength->property_id = 22;
        $earlength->value = $earLengthValue;

        if(is_null($request->head_length)){
            $headLengthValue = "";
        }
        else{
            $headLengthValue = $request->head_length;
        }

        $headlength->animal_id = $animalid;
        $headlength->property_id = 23;
        $headlength->value = $headLengthValue;

        if(is_null($request->snout_length)){
            $snoutLengthValue = "";
        }
        else{
            $snoutLengthValue = $request->snout_length;
        }

        $snoutlength->animal_id = $animalid;
        $snoutlength->property_id = 24;
        $snoutlength->value = $snoutLengthValue;

        if(is_null($request->body_length)){
            $bodyLengthValue = "";
        }
        else{
            $bodyLengthValue = $request->body_length;
        }

        $bodylength->animal_id = $animalid;
        $bodylength->property_id = 25;
        $bodylength->value = $bodyLengthValue;

        if(is_null($request->heart_girth)){
            $heartGirthValue = "";
        }
        else{
            $heartGirthValue = $request->heart_girth;
        }

        $heartgirth->animal_id = $animalid;
        $heartgirth->property_id = 26;
        $heartgirth->value = $heartGirthValue;

        if(is_null($request->pelvic_width)){
            $pelvicWidthValue = "";
        }
        else{
            $pelvicWidthValue = $request->pelvic_width;
        }

        $pelvicwidth->animal_id = $animalid;
        $pelvicwidth->property_id = 27;
        $pelvicwidth->value = $pelvicWidthValue;

        if(is_null($request->tail_length)){
            $tailLengthValue = "";
        }
        else{
            $tailLengthValue = $request->tail_length;
        }

        $taillength->animal_id = $animalid;
        $taillength->property_id = 28;
        $taillength->value = $tailLengthValue;

        if(is_null($request->height_at_withers)){
            $heightAtWithersValue = "";
        }
        else{
            $heightAtWithersValue = $request->height_at_withers;
        }

        $heightatwithers->animal_id = $animalid;
        $heightatwithers->property_id = 29;
        $heightatwithers->value = $heightAtWithersValue;


        $animal = Animal::find($animalid);

        if(is_null($request->number_normal_teats)){
            $normalTeatsValue = "";
        }
        else{
            $normalTeatsValue = $request->number_normal_teats;
        }

        $normalteats->animal_id = $animalid;
        $normalteats->property_id = 30;
        $normalteats->value = $normalTeatsValue;
        $normalteats->save();
     
        $dcmorpho->save();
        $earlength->save();
        $headlength->save();
        $snoutlength->save();
        $bodylength->save();
        $pelvicwidth->save();
        $heartgirth->save();
        $taillength->save();
        $heightatwithers->save();
        
        $animal->morphochars = 1;
        $animal->save();

        //return Redirect::back()->with('message','Animal record successfully saved');
    }

    public function fetchWeightRecords(Request $request){ // function to add weight records
            $animal = Animal::where("registryid", $request->registry_id)->first();
            $animalid = $animal->id;
            $animal = Animal::find($animalid);
            $properties = $animal->getAnimalProperties();

            // used when date collected was not provided
            $bday = $properties->where("property_id", 3)->first();

            $bw45d = $animal->getAnimalProperties()->where("property_id", 32)->first();
            $dc45d = $animal->getAnimalProperties()->where("property_id", 37)->first();
            $bw60d = $animal->getAnimalProperties()->where("property_id", 33)->first();
            $dc60d = $animal->getAnimalProperties()->where("property_id", 38)->first();
            $bw90d = $animal->getAnimalProperties()->where("property_id", 34)->first();
            $dc90d = $animal->getAnimalProperties()->where("property_id", 39)->first();
            $bw150d = $animal->getAnimalProperties()->where("property_id", 35)->first();
            $dc150d = $animal->getAnimalProperties()->where("property_id", 40)->first();
            $bw180d = $animal->getAnimalProperties()->where("property_id", 36)->first();
            $dc180d = $animal->getAnimalProperties()->where("property_id", 41)->first();
            //$othermarks = $animal->getAnimalProperties()->where("property_id", 20)->first();

            if($bw45d==null) $bw45d = new AnimalProperty;
            if($dc45d==null) $dc45d = new AnimalProperty;
            if($bw60d==null) $bw60d = new AnimalProperty;
            if($dc60d==null) $dc60d = new AnimalProperty;
            if($bw90d==null) $bw90d = new AnimalProperty;
            if($dc90d==null) $dc90d = new AnimalProperty;
            if($bw150d==null) $bw150d = new AnimalProperty;
            if($dc150d==null) $dc150d = new AnimalProperty;
            if($bw180d==null) $bw180d = new AnimalProperty;
            if($dc180d==null) $dc180d = new AnimalProperty;

            $datefarrowedprop = $properties->where("property_id", 3)->first();
            $dateweanedprop = $properties->where("property_id", 6)->first();

            if(!is_null($datefarrowedprop) && !is_null($dateweanedprop)){
                if($datefarrowedprop->value != "Not specified" && $dateweanedprop->value != "Not specified"){
                    $datefarrowed = Carbon::parse($datefarrowedprop->value);
                    $dateweaned = Carbon::parse($dateweanedprop->value);
                    $age_weaned = $dateweaned->diffInDays($datefarrowed);
                }
                else{
                    $age_weaned = "";
                }
            }
            else{
                $age_weaned = "";
            }

            if(is_null($request->body_weight_at_45_days)){
                if($age_weaned == 45){
                    $bw45dValue = $request->body_weight_at_45_days;
                }
                else{
                    $bw45dValue = "";
                }
            }
            else{
                $bw45dValue = $request->body_weight_at_45_days;
            }

            $bw45d->animal_id = $animalid;
            $bw45d->property_id = 32;
            $bw45d->value = $bw45dValue;

            if(is_null($request->date_collected_45_days)){
                if(!is_null($bday) && $bday->value != "Not specified"){
                    $dc45dValue = Carbon::parse($bday->value)->addDays(45)->toDateString();
                }
                else{
                    $dc45dValue = "";
                }
            }
            else{
                $dc45dValue = $request->date_collected_45_days;
            }

            $dc45d->animal_id = $animalid;
            $dc45d->property_id = 37;
            $dc45d->value = $dc45dValue;

            if(is_null($request->body_weight_at_60_days)){
                if($age_weaned == 60){
                    $bw60dValue = $request->body_weight_at_60_days;
                }
                else{
                    $bw60dValue = "";
                }
            }
            else{
                $bw60dValue = $request->body_weight_at_60_days;
            }

            $bw60d->animal_id = $animalid;
            $bw60d->property_id = 33;
            $bw60d->value = $bw60dValue;

            if(is_null($request->date_collected_60_days)){
                if(!is_null($bday) && $bday->value != "Not specified"){
                    $dc60dValue = Carbon::parse($bday->value)->addDays(60)->toDateString();
                }
                else{
                    $dc60dValue = "";
                }
            }
            else{
                $dc60dValue = $request->date_collected_60_days;
            }

            $dc60d->animal_id = $animalid;
            $dc60d->property_id = 38;
            $dc60d->value = $dc60dValue;

            if(is_null($request->body_weight_at_90_days)){
                $bw90dValue = "";
            }
            else{
                $bw90dValue = $request->body_weight_at_90_days;
            }

            $bw90d->animal_id = $animalid;
            $bw90d->property_id = 34;
            $bw90d->value = $bw90dValue;

            if(is_null($request->date_collected_90_days)){
                if(!is_null($bday) && $bday->value != "Not specified"){
                    $dc90dValue = Carbon::parse($bday->value)->addDays(90)->toDateString();
                }
                else{
                    $dc90dValue = "";
                }
            }
            else{
                $dc90dValue = $request->date_collected_90_days;
            }

            $dc90d->animal_id = $animalid;
            $dc90d->property_id = 39;
            $dc90d->value = $dc90dValue;

            if(is_null($request->body_weight_at_150_days)){
                $bw150dValue = "";
            }
            else{
                $bw150dValue = $request->body_weight_at_150_days;
            }

            $bw150d->animal_id = $animalid;
            $bw150d->property_id = 35;
            $bw150d->value = $bw150dValue;

            if(is_null($request->date_collected_150_days)){
                if(!is_null($bday) && $bday->value != "Not specified"){
                    $dc150dValue = Carbon::parse($bday->value)->addDays(150)->toDateString();
                }
                else{
                    $dc150dValue = "";
                }
            }
            else{
                $dc150dValue = $request->date_collected_150_days;
            }

            $dc150d->animal_id = $animalid;
            $dc150d->property_id = 40;
            $dc150d->value = $dc150dValue;

            if(is_null($request->body_weight_at_180_days)){
                $bw180dValue = "";
            }
            else{
                $bw180dValue = $request->body_weight_at_180_days;
            }

            $bw180d->animal_id = $animalid;
            $bw180d->property_id = 36;
            $bw180d->value = $bw180dValue;

            if(is_null($request->date_collected_180_days)){
                if(!is_null($bday) && $bday->value != "Not specified"){
                    $dc180dValue = Carbon::parse($bday->value)->addDays(180)->toDateString();
                }
                else{
                    $dc180dValue = "";
                }
            }
            else{
                $dc180dValue = $request->date_collected_180_days;
            }

            $dc180d->animal_id = $animalid;
            $dc180d->property_id = 41;
            $dc180d->value = $dc180dValue;

            $bw45d->save();
            $dc45d->save();
            $bw60d->save();
            $dc60d->save();
            $bw90d->save();
            $dc90d->save();
            $bw150d->save();
            $dc150d->save();
            $bw180d->save();
            $dc180d->save();

            $animal = Animal::find($animalid);
            $animal->weightrecord = 1;
            $animal->save();
        }

    public function getAllCount()
    {
        $sow = 0;
        $boar = 0;
        $maleGrower = 0;
        $femaleGrower = 0;

        $breeders = Animal::where('status', "breeder")->get();
        $growers = Animal::where('status', "active")->get();

        foreach ($breeders as $breeder) {
            if(substr($breeder->registryid, -7, 1) == 'F'){
               $sow++; 
            }else{
                $boar++;
            }
        }

        foreach ($growers as $grower) {
            if(substr($grower->registryid, -7, 1) == 'F'){
               $femaleGrower++; 
            }else{
                $maleGrower++;
            }
        }

        $countArray = array('sowCount' => $sow,
                    'boarCount' => $boar,
                    'femaleGrowerCount' => $femaleGrower,
                    'maleGrowerCount' => $maleGrower
        );

        return json_encode($countArray);    
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
