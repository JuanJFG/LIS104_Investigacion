<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    public function index()
    {
        $todosJson = Storage::disk('local')->get('json/todos.json');
        $todos = json_decode($todosJson, true);
        return view('todos.index', compact('todos'));
    }

    public function store(Request $request)
    {
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
    }

    public function update(Request $request, $id)
    {
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
    }

    public function destroy($id)
    {
        $todosJson = Storage::disk('local')->get('json/todos.json');
        $todos = json_decode($todosJson, true);

        $updatedTodos = array_filter($todos, function ($todo) use ($id) {
            return $todo['id'] !== $id;
        });

        Storage::disk('local')->put('json/todos.json', json_encode($updatedTodos));

        return redirect()->route('todos.index');
    }
}
