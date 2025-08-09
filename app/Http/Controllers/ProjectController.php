<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\Contract;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewProjectSubmitted;
use App\Mail\NewPriceProposal;
use Illuminate\Support\Facades\Mail;
use App\Models\Projet;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    // Soumettre un nouveau projet par le client
    public function store(Request $request)
    {
        Log::info($request);
        $request->validate([
            'service_id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'deadline' => 'nullable|date',
            'client_price' => 'required|numeric',
            'file' => 'nullable|file',
           'specific_fields' => 'nullable|string',
        ]);

        $project = new Projet([
            'user_id' => Auth::id(),
            'service' => $request->service_id,
            'name' => $request->name,
            'description' => $request->description,
            'objectives' => $request->objectives,
            'deadline' => $request->deadline,
            'client_price' => $request->client_price,
            'specific_fields' => json_encode($request->specific_fields),
            'status' => 'pending',
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
            $projects = Project::with(['user', 'service'])->get();
        } else {
            $projects = Project::where('user_id', $user->id)->with(['service'])->get();
        }
        return response()->json($projects);
    }
    
    // Afficher les détails d'un projet
    public function show(Project $project)
    {
        $project->load(['user', 'service', 'proposals', 'contracts', 'files']);
        return response()->json($project);
    }
    
    // Proposer un prix (pour client ou admin)
    public function proposePrice(Request $request, Project $project)
    {
        $request->validate(['price' => 'required|numeric']);

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'price' => $request->price,
        ]);
        
        // Envoi d'un email au client ou à l'admin
        $recipient = (Auth::user()->role === 'admin') ? $project->user : \App\Models\User::where('role', 'admin')->first();
        Mail::to($recipient->email)->send(new NewPriceProposal($project, $proposal));

        return response()->json($proposal, 201);
    }
    
    // Mettre à jour la progression du projet (admin)
    public function updateProgress(Request $request, Project $project)
    {
        // Vérification de la permission 'admin' déjà gérée par le middleware
        $request->validate(['progress' => 'required|integer|min:0|max:100']);
        
        $project->progress = $request->progress;
        $project->save();

        return response()->json($project);
    }

    // L'admin valide le projet et envoie un contrat
    public function validateProject(Request $request, Project $project)
    {
        $request->validate(['contract_file' => 'required|file|mimes:pdf,doc,docx']);

        $project->status = 'in-progress';
        $project->final_price = $project->proposals()->where('status', 'accepted')->first()->price;
        $project->save();
        
        $path = $request->file('contract_file')->store('contracts', 'public');
        Contract::create([
            'project_id' => $project->id,
            'file_path' => $path,
        ]);
        
        // Envoi d'un email au client pour le contrat
        // Mail::to($project->user->email)->send(new ContractSent($project));

        return response()->json($project);
    }

    // Le client signe et renvoie le contrat
    public function signContract(Request $request, Project $project)
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
    public function completeProject(Request $request, Project $project)
    {
        $request->validate(['final_link' => 'nullable|url']);

        $project->status = 'completed';
        $project->progress = 100;
        $project->final_link = $request->final_link;
        $project->save();

        return response()->json($project);
    }
}
