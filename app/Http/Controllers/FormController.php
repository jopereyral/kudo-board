<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Option;
use App\Kudo;
use Exception;

class FormController extends Controller
{
    public function index()
    {
        $kudos = Kudo::all();

        return view('index', compact('kudos'));
    }


    public function show()
    {

    }


    public function store(Request $request)
    {
        $kudos = Kudo::all();

        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'option' => 'required',
            // 'date' => 'date|date_format:dd/mm/yyyy',
            'message' => 'required|max:240',
        ]);

        try
        {
            // dd($request->option);
            $kudo = Kudo::create([
                'to' => $request->to,
                'from' => $request->from,
                'date' => $request->date,
                'message' => $request->message,
            ]);

            $options = Option::find($request->option);
            $kudo->options()->attach($options);
        }
        catch(Exception $e)
        {
            return redirect()->route('kudo.index')->with([
                'message' => 'Hubo un error: '.$e->getMessage(),
                'kudos' => $kudos,
            ]);

        }

        return redirect()->route('kudo.index')->with([
            'message' => 'exito',
            'kudos' => $kudos,
        ]);

    }

    public function create()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://api.tksuite.dev.apus.tektonlabs.com',
        ]);
        $collaborators = $client->get('/api/v1/collaborators', [
            'headers' => [
                'TK-KEY' => '2e5bb0c217c1b1bd6cd9774862eb5f77053bd0d3',
            ]
        ]);
        $collaborators = collect(json_decode($collaborators->getBody()));
        $activeCollaborators = $collaborators->random(5)->map(function($c) {
            return [
                'key' => "{$c->first_name} {$c->last_name}",
                'value' => "{$c->first_name} {$c->last_name}",
            ];
        });
        $inactiveCollaborators = $collaborators->random(5)->map(function($c) {
            return [
                'key' => "{$c->first_name} {$c->last_name}",
                'value' => "{$c->first_name} {$c->last_name}",
            ];
        });
        $options = Option::all();

        return view('welcome', compact('options', 'activeCollaborators', 'inactiveCollaborators'));
    }
}
