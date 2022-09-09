<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_all_tasks()
    {
        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['current_page' => 1]);
    }

    public function test_create_a_task() {
        $response = $this->postJson(route('tasks.store'), [
            'name' => 'My new task',
            'done' => true,
            'due_date' => '2022-01-01'
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'My new task']);
    }

    public function test_cannot_create_a_task_validation_error() {
        $response = $this->postJson(route('tasks.store'), [
            'done' => true,
            'due_date' => '2022-01-01'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "The name field is required."
        ]);
    }

    public function test_cannot_create_a_task_without_done() {
        $response = $this->postJson(route('tasks.store'), [
            'name' => 'My new task',
            'due_date' => '2022-01-01'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "The done field is required."
        ]);
    }

    public function test_get_task() {

        $task = Task::factory()->create();
        $response = $this->getJson(route('tasks.show', $task->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $task->name]);
        $response->assertJsonFragment(['done' => $task->done]);

    }
    public function test_cannot_get_task() {

        $response = $this->getJson(route('tasks.show', 100));
        $response->assertStatus(404);

    }

    public function test_update_task() {

        $task = Task::factory()->create();

        $response = $this->putJson(route('tasks.update', $task->id), [
            'name' => 'My new name',
            'done' => $task->done,
            'due_date' => $task->due_date
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_update_task() {

        $task = Task::factory()->create();

        $response = $this->putJson(route('tasks.update', 1000), [
            'name' => 'My new name',
            'done' => $task->done,
            'due_date' => $task->due_date
        ]);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "Task not found"
        ]);
    }
    public function test_delete_task() {

        $task = Task::factory()->create();

        $response = $this->deleteJson(route('tasks.delete', $task->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "Task deleted!"
        ]);
    }
    public function test_cannot_delete_task() {

        $response = $this->deleteJson(route('tasks.delete', 1000));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "Task not found"
        ]);
    }
}
