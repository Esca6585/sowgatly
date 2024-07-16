<?php

namespace App\Http\Controllers\User\Travel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Location\Coordinate;
use Location\Distance\Vincenty;
use App\Models\User;
use App\Models\Route;
use App\Models\Travel;
use App\Models\Tarif;
use Carbon;
use DateTime;
use DB;

class TravelController extends Controller
{
    public function tarifs(Request $request)
    {
        $lang = $request->header('Accept-Language');
        
        $tarifs = Tarif::select('*', "name_$lang as name")->where('additional_tarif', $request->additional_tarif)->get();
        
        return response()->json(compact('tarifs'));
    }

    public function travelStart(Request $request)
    {
        $lang = $request->header('Accept-Language');

        $tarif = Tarif::where('id', $request->tarif_id)->select("tarifs.name_$lang as name", 'tarifs.*')->first();

        $travel = new Travel();

        $travel->user_id = $request->user()->id;
        $travel->tarif_id = $request->tarif_id;
        $travel->lat = $request->lat;
        $travel->lon = $request->lon;
        $travel->price = floatval($tarif->minimum_price);
        $travel->km = floatval(0.0);
        $travel->status = 'waiting';
        $travel->minimum_price = $tarif->minimum_price;
        $travel->minute_price = $tarif->every_minute_price;
        $travel->km_price += $tarif->every_km_price;
        $travel->waiting_price = $tarif->every_waiting_price;
        $travel->minute_price_outside = $tarif->every_minute_price_outside;
        $travel->km_price_outside = $tarif->every_km_price_outside;
        $travel->tarifs = [];

        $travel->save();

        $night = $this->dayOrNight();

        return response()->json([
            'status' => $travel->status,
            'tarif' => $tarif,
            'travel' => $travel,
            'night' => $night
        ]);
    }

    public function travelFinish(Request $request)
    {
        $travel = Travel::findOrFail($request->travel_id);
        $tarif = Tarif::findOrFail($travel->tarif_id);
        
        $travel->lat_finish = $request->lat_finish;
        $travel->lon_finish = $request->lon_finish;

        $travel->tarifs = $this->getAdditionalTarifs($request->tarifs);

        $travel->km = (double) $request->all_km;
        $travel->price = (double) $request->total_price;
        $travel->minimum_price = (double) $request->minimum_price;
        $travel->minute_price = (double)($tarif->every_minute_price*$request->time);
        $travel->km_price = (double)($tarif->every_km_price*$request->all_km);
        $travel->waiting_price = (double)($tarif->every_waiting_price*$request->waiting_time);
        $travel->minute_price_outside = $tarif->every_minute_price_outside;
        $travel->km_price_outside = $tarif->every_km_price_outside;
        
        $travel->time = $request->time;
        $travel->waiting_time = $request->waiting_time;
        
        $travel->status = 'finished';
        
        $travel->update();

        $night = $this->dayOrNight();

        return response()->json([
            'success' => true
        ]);
    }

    public function coordinateSave(Request $request)
    {
        foreach($request->data as $row){
            $route = new Route;

            $route->user_id = $request->user()->id;
            $route->travel_id = $row['travel_id'];
            $route->lat = $row['latitude'];
            $route->lon = $row['langitude'];

            $route->save();

            $travel = Travel::findOrFail($row['travel_id']);

            $travel->time = $row['time'];

            $travel->update();
        }
        
        return response()->json([
            'success' => true
        ]);
    }

    public function getStatistic(Request $request)
    {
        $lang = $request->header('Accept-Language');

        if($request->user_id){
            return Travel::select('travels.*', "tarifs.name_$lang as name")
                            ->where('user_id', $request->user_id)
                            ->join('tarifs', 'travels.tarif_id', '=', 'tarifs.id')
                            ->get();
        } else {
            return Travel::select('travels.*', "tarifs.name_$lang as name")
                                ->where('user_id', $request->user()->id)
                                ->join('tarifs', 'travels.tarif_id', '=', 'tarifs.id')
                                ->get();
        }
    }

    public function dayOrNight()
    {
        $hour = date("h");
        if($hour >= env('DAY') && $hour <= env('NIGHT')){
            return 'Day';
        } else {
            return 'Night';
        }
    }

    public function getAdditionalTarifs($tarifs)
    {
        $tarifsData = array();

        if(!empty($tarifs)){
            $explode_id = array_map('intval', explode(',', $tarifs));
            
            foreach($explode_id as $id){
                if($id > 0){
                    $tarifsName = Tarif::findOrFail($id);

                    array_push($tarifsData,
                        array(
                            'id' => $tarifsName->id,
                            'minimum_price' => $tarifsName->minimum_price,
                            'name_tm' => $tarifsName->name_tm,
                            'name_ru' => $tarifsName->name_ru,
                            'type' => '+',
                        )
                    );
                } else if($id < 0){
                    $tarifsName = Tarif::findOrFail($id*-1);

                    array_push($tarifsData,
                        array(
                            'id' => $tarifsName->id,
                            'minimum_price' => $tarifsName->minimum_price,
                            'name_tm' => $tarifsName->name_tm,
                            'name_ru' => $tarifsName->name_ru,
                            'type' => '-',
                        )
                    );
                }
            }
        }

        return $tarifsData;
    }
}
