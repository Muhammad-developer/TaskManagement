<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Получение списка задач
        $tasks = Task::all();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        // Валидация данных, поступающих в API
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
        ]);

        // Создание задачи
        $task = Task::create([
            'title' => $validatedData['title'],
            'description' => $request->description,
            'status' => 'todo', // По умолчанию задача добавляется в статусе "todo"
            'deadline' => $validatedData['deadline'],
        ]);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        // Показ одной задачи
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        // Валидация данных, поступающих в API
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|in:todo,in_progress,done', // Проверка на допустимые статусы
        ]);

        // Обновление задачи
        $task->title = $validatedData['title'];
        $task->description = $request->description;
        $task->deadline = $validatedData['deadline'];
        $task->status = $validatedData['status'];
        $task->save();

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        // Удаление задачи
        $task->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        // Поиск задач по крайнему сроку и статусу
        $tasks = Task::query();

        if ($request->has('deadline')) {
            $tasks->where('deadline', $request->deadline);
        }

        if ($request->has('status')) {
            $tasks->where('status', $request->status);
        }

        $tasks = $tasks->get();
        return response()->json($tasks);
    }
}
