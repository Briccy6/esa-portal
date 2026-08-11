<?php

test('student registration form includes the required sex field', function () {
    $response = $this->get('/students/create');

    $response->assertOk();
    $response->assertSee('Sex');
    $response->assertSee('name="sex"');
});
