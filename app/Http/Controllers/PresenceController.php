<?php

namespace App\Http\Controllers;

use App\Models\Infos;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $aujourdHui = Carbon::today();
            $collection = new Collection();
            // dd($aujourdHui);
            $presence = Presence::whereDate('created_at', $aujourdHui)->get();
            foreach ($presence as $key => $item) {
                $user = User::where('id', $item->user_id)->get();
                $collection->push($user);
            }
            $user = $collection;
            return response()->json([
                'data' => $presence,
                'user' =>  $collection  ,
                'statut' => '200'
            ]);
        } catch (\Exception $e) {
         
            return response()->json([
                'message' => 'Une erreur s\'est produite lors de la récupération des données.',
                'status' => 'error'
            ]);
        }
        
    }



    public function all()
    {

        try {
            $collection = new Collection();
            $presence = Presence::orderByDesc('created_at')->get();
            // dd($presence);
            foreach ($presence as $key => $item) {
                $user = User::where('id', $item->user_id)->get();
                $collection->push($user);
            }
            $user = $collection;
            return response()->json([
                'data' => $presence,
                'user' =>  $collection  ,
                'statut' => '200'
            ]);
        } catch (\Exception $e) {
         
            return response()->json([
                'message' => 'Une erreur s\'est produite lors de la récupération des données.',
                'status' => 'error'
            ]);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function test(Request $request)
{
    // \Illuminate\Support\Facades\Log::info($request);
   
    $infos = Infos::where('id', 1)->get();
    $infosString = strval($infos[0]->infos);
    $id =$request->id;
    $requestString = strval($request->info);
    if ($infosString === $requestString) {
        $heurearrive = Carbon::now()->format('H:i:s');
        $currentDate = Carbon::now()->toDateString();
        // $user_id = 1;
       
        // Vérifier si l'utilisateur a déjà enregistré sa présence aujourd'hui
        $aujourdHui = Carbon::today();
        $presenceExistante = Presence::where('user_id', $id)
        ->whereDate('created_at', $aujourdHui)->first();
        if ($presenceExistante) {
        //    dd($presenceExistante);
            // L'utilisateur a déjà enregistré sa présence aujourd'hui, retourner une erreur ou effectuer une action appropriée
            $presenceExistante->heuredepart = $heurearrive;
            $presenceExistante->heuredepart =  $presenceExistante->heuredepart ;
            $presenceExistante->datejour =  $presenceExistante->datejour;
            $presenceExistante->user_id =   $presenceExistante->user_id;
            $presenceExistante->save();
            return response()->json(['message' => 'La présence a déjà été enregistrée aujourd\'hui.'], 200);
        } else {
            // Enregistrer la présence de l'utilisateur
            $user = User::where('id', $id)->first();
            $presence = Presence::create([
                "heurearrive" => $heurearrive,
                "heuredepart" => $heurearrive,
                "datejour" => $currentDate,
                "user_id" =>  $id,
            ]);

            // Retourner une réponse réussie ou effectuer une action appropriée
            return response([$presence,$user], 200);
        }
    } else {
        return response("fail scan", 404);
    }
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {

        $presence = Presence::create([
            "heurearrive" => $request->heurearrive,
            "heuredepart" => $request->heuredepart,
            "datejour" => $request->datejour,
            "user_id" => $request->user_id,
        ]);

        return response()->json([
            "data" => $presence,
            'statut' => '201'
        ]);
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
        $presence = Presence::where('user_id', $id)->orderByDesc('created_at')->get();
        return response()->json([
            'data' => $presence,
            'statut' => '200',
            'message' => 'get successful'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $presence = Presence::find($id);
        $presence = Presence::update([
            "heurearrive" => $request->heurearrive,
            "heuredepart" => $request->heuredepart,
            "datejour" => $request->datejour,
            "user_id" => $request->user_id,
        ]);

        return response()->json([
            'message' => "update successful"
        ]);
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
