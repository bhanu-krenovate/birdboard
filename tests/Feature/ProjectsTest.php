<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{


    use WithFaker, RefreshDatabase;

    /** @test */
    public function only_authenticated_users_can_create_project()
    {

        $attribute = Project::factory()->raw();

        $this->post('/projects', $attribute)->assertRedirect('login');
    }


    /** @test */

    public function a_user_can_create_a_project()
    {

        $attributes =  [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->actingAs(User::factory()->create());

        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }


    /** @test  */
    public function a_user_can_view_a_project()
    {
//        $this->withoutExceptionHandling();

        $project = Project::factory()->create();

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);

    }


    /** @test */
    public function a_project_requires_a_title()
    {
        $this->actingAs(User::factory()->create());

        $attribute = Project::factory()->raw(['title' => '']);

        $this->post('/projects', $attribute)->assertSessionHasErrors('title');
    }


    /** @test */
    public function a_project_requires_a_description()
    {
        $this->actingAs(User::factory()->create());

        $attribute = Project::factory()->raw(['description' => '']);

        $this->post('/projects', $attribute)->assertSessionHasErrors('description');
    }


}
