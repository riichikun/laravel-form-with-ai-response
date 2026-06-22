<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_contact_controller_succesful(): void
    {
        $response = $this->post('/api/contact', [
            'name' => 'John Doe',
            'phone' => '0123456789',
            'email' => 'john@doe.com',
            'comment' => 'огромное спасибо за вашу работу! Вы лучший!',
        ]);

        $response->assertStatus(200);
    }

    public function test_contact_controller_invalid(): void
    {
        $response = $this->post('/api/contact');

        $response->assertStatus(422);
    }
}
