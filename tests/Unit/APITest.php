<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITest extends TestCase
{
    public function testUserCreation() {
        $response = $this->json('POST', '/api/register', [
            'name' => 'Demo User',
            'email' => str_random(10) . '@demo.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'success' => ['token', 'name']
        ]);
    }

    public function testUserLogin() {
        $response = $this->json('POST', '/api/login', [
            'email' => 'demo@demo.com',
            'password' => 'secret'
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'success' => ['token']
        ]);
    }

    public function testRoomFetch() {
        $user = \App\User::find(1);

        $response = $this->actingAs($user, 'api')
                         ->json('GET', '/api/room')
                         ->assertStatus(200)->assertJsonStructure([
                            '*' => [
                                'id',
                                'name',
                                'created_at',
                                'updated_at',
                                'deleted_at',
                            ]
                        ]);
    }

    public function testRoomCreation() {
        $this->withoutMiddleware();

        $response = $this->json('POST', '/api/room', [
            'name' => str_random(10),
        ]);

        $response->assertStatus(200)->assertJson([
            'status' => true,
            'message' => 'Room Created'
        ]);
    }

    public function testRoomDeletion() {
        $user = \App\User::find(1);
        $room = \App\MeetingRoom::create(['name' => 'To be deleted']);

        $response = $this->actingAs($user, 'api')
                         ->json('DELETE', "/api/room/{$room->id}")
                         ->assertStatus(200)->assertJson([
                            'status' => true,
                            'message' => 'Room Deleted'
                        ]);
    }
}
