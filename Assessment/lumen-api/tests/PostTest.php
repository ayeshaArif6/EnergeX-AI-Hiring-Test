<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class PostTest extends TestCase
{
    use DatabaseMigrations;

    public function test_posts_index_is_public()
    {
        $this->get('/api/posts')->seeStatusCode(200);
    }

    public function test_register_and_login_and_create_post()
    {
        $this->post('/api/register', [
            'name' => 'T', 'email' => 't@example.com', 'password' => 'secret123'
        ])->seeStatusCode(200)->seeJsonStructure(['id']);

        $login = $this->post('/api/login', [
            'email' => 't@example.com', 'password' => 'secret123'
        ])->response->getOriginalContent();

        $this->post('/api/posts', [
            'title' => 'Hello', 'content' => 'World'
        ], ['Authorization' => 'Bearer '.$login['token']])->seeStatusCode(200);
    }
}
