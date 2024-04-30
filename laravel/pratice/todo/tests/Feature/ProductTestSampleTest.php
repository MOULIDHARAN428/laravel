<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Log;

class ProductTestSampleTest extends TestCase
{

    /** @test */
    public function task_read()
    {
        $user = factory(User::class)->create();

        $response = $this->get('api/tasks');
        
        Log::info(json_encode($response));

        $response->assertStatus(200);
        // $this->assertDatabaseHas('tasks', ['title' => 'New Task', 'description' => 'This is a new task.']);
    }
}
