<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Contract;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewProjectSubmitted;
use App\Mail\NewPriceProposal;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Projets;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    // Soumettre un nouveau projet par le client
    public function store(Request $request)
    {
        Log::info($request);
        // $request->validate([
        //     'service' => 'required|string',
        //     'name' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'objectives' => 'nullable|string',
        //     'deadline' => 'nullable|date',
        //     'client_price' => 'required|numeric',
        //     'file' => 'nullable|file',
        //    'specific_fields' => 'nullable|string',
        // ]);

        $project = new Projets([
            'user_id' => Auth::id(),
            'service' => $request->service,
            'name' => $request->name,
            'description' => $request->description,
            'objectives' => $request->objectives,
            'deadline' => $request->deadline,
            'client_price' => $request->client_price,
            'specific_fields' => json_encode($request->specific_fields),
            'status' => 'pending',
            'device'=>$request->device,
            'progress' => 0,
        ]);
        $project->save();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('project_files', 'public');
            File::create([
                'project_id' => $project->id,
                'name' => $request->file('file')->getClientOriginalName(),
                'path' => $path,
            ]);
        }
        
        // Envoi d'un email à l'admin
        // Mail::to('dilanetalom8@gmail.com')->send(new NewProjectSubmitted($project));

        return response()->json($project, 201);
    }
    
    // Lister les projets (pour client ou admin)
    public function index(Request $request)
    {
        $user = Auth::user(); 

    if ($user->role === 'admin') {
        // L'utilisateur est admin, on récupère tous les projets
        $projects = Projets::with('user')->get();
    } else {
        // L'utilisateur est un client, on récupère seulement ses projets
        $projects = Projets::where('user_id', $user->id)->get();
    }
    return response()->json($projects);
    }
    
    // Afficher les détails d'un projet
    public function show(Projets $project)
    {
        $project->load(['user', 'service', 'proposals', 'contracts', 'files']);
        return response()->json($project);
    }
    
    public function acceptProposal(Projets $project)
    {
        // Vérifiez que le projet est bien en attente
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Ce projet ne peut pas être accepté.'], 400);
        }

        $project->status = 'accepted';
        $project->final_price = $project->client_price;
        $project->save();

        return response()->json($project);
    }
    
    // ADMIN : Refuse et entre en négociation
    public function refuseAndNegotiate(Projets $project)
    {
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Ce projet ne peut pas être mis en négociation.'], 400);
        }
        
        $project->status = 'negotiation';
        $project->save();
        
        // Crée une conversation si elle n'existe pas.
        // Cette méthode est plus sûre pour éviter les doublons.
        Conversation::firstOrCreate(['projet_id' => $project->id]);

        return response()->json($project);
    
    }

    // CLIENT : Met à jour le prix après négociation
    public function updatePrice(Request $request, Projets $project)
    {
        // Ne permet de mettre à jour le prix que si le statut est 'negotiation'
        if ($project->status !== 'negotiation' || Auth::id() !== $project->user_id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $request->validate(['client_price' => 'required|numeric']);

        $project->client_price = $request->client_price;
        $project->save();

        return response()->json($project);
    }

    // ADMIN : Valide le nouveau prix après négociation
    public function validateNegotiation(Projets $project)
    {
        if ($project->status !== 'negotiation') {
            return response()->json(['message' => 'Ce projet n\'est pas en négociation.'], 400);
        }
        
        $project->status = 'accepted';
        $project->final_price = $project->client_price;
        $project->save();

        return response()->json($project);
    }
    
    // Le client signe et renvoie le contrat
    public function signContract(Request $request, Projets $project)
    {
        $request->validate(['signed_contract' => 'required|file|mimes:pdf,doc,docx']);

        $contract = $project->contracts()->first(); // On suppose qu'il n'y a qu'un seul contrat par projet
        $path = $request->file('signed_contract')->store('signed_contracts', 'public');
        $contract->signed = true;
        $contract->signed_file_path = $path;
        $contract->save();

        return response()->json($project);
    }
    
    // L'admin termine le projet
    public function completeProject(Request $request, Projets $project)
    {
        $request->validate(['final_link' => 'nullable|url']);

        $project->status = 'completed';
        $project->progress = 100;
        $project->final_link = $request->final_link;
        $project->save();

        return response()->json($project);
    }
}
