<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class EventController extends Controller
{


    public function EventsList()
    {
        $events = Event::orderBy("dateEvent", "ASC")->paginate(9);
        return inertia("Home", [
            "events" => $events
        ]);
    }



    public function getEvent(Event $event)
    {
        return response()->json(["event" => $event]);
    }



    public function store(Request $request)
    {
        $request->validate([
            "titleEvent" => "required",
            "descriptionEvent" => "required",
            "dateEvent" => "required"
        ]);

        $slugEvent = Str::slug($request->titleEvent);

        // Check if an event with the same information already exists
        $existingEvent = Event::where('titleEvent', $request->titleEvent)
            ->where('descriptionEvent', $request->descriptionEvent)
            ->where('dateEvent', $request->dateEvent)
            ->first();

        if ($existingEvent) {
            // Existing event, return an error response
            return response()->json([
                'message' => "Un événement avec les mêmes informations existe déjà !",
                'code' => 400
            ]);
        }

        // Create a new event if it doesn't already exist
        $event = new Event();
        $event->titleEvent = $request->titleEvent;
        $event->slugEvent = $slugEvent;
        $event->descriptionEvent = $request->descriptionEvent;
        $event->dateEvent = $request->dateEvent;
        $event->save();

        return response()->json([
            'message' => "Evenement créé avec succès !",
            'code' => 200
        ]);
    }






    public function deleteEvent($id)
    {
        $event = Event::find($id);

        if ($event) {
            $event->delete();
            return response()->json([
                'message' => "Evenement supprimé avec succès !",
                'code' => 200
            ]);
        } else {
            return response()->json([
                'message' => "Evenement avec l'id:$id n'existe pas  !",
                'code' => 200
            ]);
        }
    }






    public function updateEvent($id, Request $request)
    {
        $event = Event::where('id', $id)->first();

        $request->validate([
            "titleEvent" => "required",
            "descriptionEvent" => "required",
            "dateEvent" => "required"
        ]);

        $slugEvent = Str::slug($request->titleEvent);

        // Check if there is another event with the same information and a different ID
        $existingEvent = Event::where('titleEvent', $request->titleEvent)
            ->where('descriptionEvent', $request->descriptionEvent)
            ->where('dateEvent', $request->dateEvent)
            ->where('id', '!=', $id)
            ->first();

        if ($existingEvent) {
            // Another event with the same information and a different ID exists
            return response()->json([
                'message' => "La modification a été refusée. Un autre événement avec les mêmes informations existe déjà.",
                'code' => 422
            ]);
        }

        $ok = 0;

        $noChanging = Event::where('titleEvent', $request->titleEvent)
            ->where('descriptionEvent', $request->descriptionEvent)
            ->where('dateEvent', $request->dateEvent)
            ->where('id', '=', $id)
            ->first();

        if ($noChanging) {
            // No changes detected, refuse the update
            return response()->json([
                'message' => "La mise à jour a été refusée. Aucun élément modifié.",
                'code' => 400
            ]);
        }

        if ($ok === 0) {
            // Changes have been made, proceed with the update
            $event->titleEvent = $request->titleEvent;
            $event->slugEvent = $slugEvent;
            $event->descriptionEvent = $request->descriptionEvent;
            $event->dateEvent = $request->dateEvent;
            $event->save();

            return response()->json([
                'message' => "Événement modifié avec succès !",
                'code' => 200
            ]);
        }
    }






    public function compareDates(Request $request)
    {
        // Check if the 'intervallDateArray' key exists in the request
        if (!$request->has('intervallDateArray')) {
            return response()->json(['error' => 'La clé intervallDateArray est manquante dans la requête.'], 400);
        }

        $intervallDateArray = $request->input('intervallDateArray');

        try {
            // Retrieve matching events directly in the controller
            $matchingEvents = Event::dateInterval($intervallDateArray)->orderBy("dateEvent", "ASC")->get();

            return response()->json(['matchingEvents' => $matchingEvents]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des événements.'], 500);
        }
    }



}
