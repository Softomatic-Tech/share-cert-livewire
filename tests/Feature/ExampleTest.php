<?php

it('returns a successful response', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
    // $response->assertStatus(200);
});
