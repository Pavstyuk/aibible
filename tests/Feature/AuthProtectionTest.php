<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthProtectionTest extends TestCase
{
    public function test_ai_endpoints_require_authentication(): void
    {
        $this->post('/htmx/ask-local')->assertRedirect('/login');
        $this->post('/htmx/ask-giga')->assertRedirect('/login');
        $this->post('/htmx/ask-yandex')->assertRedirect('/login');
        $this->get('/htmx/comments-ai')->assertRedirect('/login');
        $this->post('/htmx/ask-ai')->assertRedirect('/login');
        $this->post('/htmx/save-ai')->assertRedirect('/login');
        $this->delete('/htmx/delete-ai')->assertRedirect('/login');
    }

    public function test_user_and_favorite_endpoints_require_authentication(): void
    {
        $this->get('/user/1')->assertRedirect('/login');
        $this->get('/user/1/edit')->assertRedirect('/login');
        $this->get('/user/1/favorites')->assertRedirect('/login');
        $this->get('/user/1/ai-comments')->assertRedirect('/login');
        $this->post('/htmx/add-favorite')->assertRedirect('/login');
    }
}
