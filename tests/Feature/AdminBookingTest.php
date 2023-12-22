<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminBookingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_booking(): void
    {

        $response = $this->postJson("/api/admin/booking");

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_booking() {

        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name","customer")->value('id'));
        $response = $this->actingAs($user)->postJson("/api/admin/booking");

        $response->assertStatus(403);
    }

    public function test_saves_booking_successfully_with_valid_date() {

        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name","admin")->value('id'));
        $response = $this->actingAs($user)->postJson("/api/admin/booking", [
            "from_date"=> "2023-12-25",
            "to_date"=> "2023-12-31",
        ]);

        $response->assertStatus(201);
    }
}
