<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    public function index()
    {
        try {
            $todosJson = Storage::disk('local')->get('json/todos.json');
            $todos = json_decode($todosJson, true);

            if (!$todos) {
                $todos = []; // Initialize an empty array if $todos is null
            }

            return view('todos.index', compact('todos'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching todos: ' . $e->getMessage());

            // Redirect to an error page or display a custom error message
            return back()->with('error', 'Error fetching todos. Please try again later.');
        }
    }

    public function store(Request $request)
    {
        try {
            $newTodo = [
                'id' => uniqid(),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'completed' => false,
            ];

            $todosJson = Storage::disk('local')->get('json/todos.json');
            $todos = json_decode($todosJson, true);
            $todos[] = $newTodo;

            Storage::disk('local')->put('json/todos.json', json_encode($todos));

            return redirect()->route('todos.index');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error storing todo: ' . $e->getMessage());

            // Redirect to an error page or display a custom error message
            return back()->with('error', 'Error storing todo. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $todosJson = Storage::disk('local')->get('json/todos.json');
            $todos = json_decode($todosJson, true);

            foreach ($todos as &$todo) {
                if ($todo['id'] === $id) {
                    $todo['title'] = $request->input('title');
                    $todo['description'] = $request->input('description');
                    $todo['completed'] = $request->has('completed');
                    break;
                }
            }

            Storage::disk('local')->put('json/todos.json', json_encode($todos));

            return redirect()->route('todos.index');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating todo: ' . $e->getMessage());

            // Redirect to an error page or display a custom error message
            return back()->with('error', 'Error updating todo. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $todosJson = Storage::disk('local')->get('json/todos.json');
            $todos = json_decode($todosJson, true);

            $updatedTodos = array_filter($todos, function ($todo) use ($id) {
                return $todo['id'] !== $id;
            });

            Storage::disk('local')->put('json/todos.json', json_encode($updatedTodos));

            return redirect()->route('todos.index');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error deleting todo: ' . $e->getMessage());

            // Redirect to an error page or display a custom error message
            return back()->with('error', 'Error deleting todo. Please try again.');
        }
    }
}
