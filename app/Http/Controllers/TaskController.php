<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;

class TaskController extends Controller
{
    public function index() {
        // get all tasks

        $tasks = Task::paginate(10);

        return response()->json($tasks);
    }

    public function store(Request $request) {
        // create a new task

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'done' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 422);
        }

        $task = new Task;
        $task->name = $request->name;
        $task->done = $request->done;
        $task->due_date = $request->due_date;
        $task->save();

        return response()->json($task, 201);
    }

    public function show($id) {

        //get a single task
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return response()->json('Task not found', 404);
        }

        return response()->json($task, 200);

    }

    public function update($id, Request $request) {

        // update a task
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 422);
        }

        $task = Task::where('id', $id)->first();
        if (!$task) {
            return response()->json('Task not found', 404);
        }

        $task->name = $request->name;
        $task->done = $request->done;
        $task->due_date = $request->due_date;
        $task->save();

        return response()->json($task, 201);

    }

    public function destroy($id) {

        // delete a task
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return response()->json('Task not found', 404);
        }
        
        $task->delete();
        return response()->json('Task deleted!');
    }
}
