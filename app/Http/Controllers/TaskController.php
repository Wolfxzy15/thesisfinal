<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
class TaskController extends Controller
{
    public function index()
    {
        $jsonPath = storage_path('app/tasks.json');

        if (File::exists($jsonPath)) {
            $json = File::get($jsonPath);
            $tasks = json_decode($json, true);
        } else {
            $tasks = [];
        }

        return view('tasks', compact('tasks'));
    }

    public function add (Request $request) {
        $incomingFields = $request->validate([
            'name'=> 'required',
            'description'=>'required',
            'deadline'=>'required'
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['description'] = strip_tags($incomingFields['description']);
        $incomingFields['deadline'] = strip_tags($incomingFields['deadline']);

        $jsonPath = storage_path('app/tasks.json');
        
        if (File::exists($jsonPath)){
            $json = File::get($jsonPath);
            $tasks = json_decode($json, true);
        } else {
            $tasks = [];
        }

        $tasks[] = [
            'name' => $incomingFields['name'],
            'description' => $incomingFields['description'],
            'deadline' => $incomingFields['deadline']
        ];


        File::put($jsonPath, json_encode($tasks, JSON_PRETTY_PRINT));

        return redirect('/');
    }

    public function delete ($name){
        $jsonPath = storage_path('app/tasks.json');

        if (File::exists($jsonPath)) {
            $json = File::get($jsonPath);
            $tasks = json_decode($json, true);
        } else {
            $tasks = [];
        }

        $tasks = array_filter($tasks, function ($task) use ($name) {
            return $task['name'] !== $name; 
        });
    
        $tasks = array_values($tasks);
    
        File::put($jsonPath, json_encode($tasks, JSON_PRETTY_PRINT));
    
        return redirect('/');


    }

    public function toggleCompleted(Request $request, $name)
    {
        $jsonPath = storage_path('app/tasks.json');
        
        if (File::exists($jsonPath)) {
            $json = File::get($jsonPath);
            $tasks = json_decode($json, true);
        } else {
            $tasks = [];
        }
    
        $taskFound = false;
    
        foreach ($tasks as &$task) {
            if ($task['name'] == $name) {
                $task['completed'] = $request->has('completed') ? true : false;
                $taskFound = true;
                break;
            }
        }
    
        if (!$taskFound) {
            return redirect('/')->with('error', 'Task not found!');
        }
    
        File::put($jsonPath, json_encode($tasks, JSON_PRETTY_PRINT));
    
        return redirect('/')->with('success', 'Task completion status updated successfully!');
    }
    

}

