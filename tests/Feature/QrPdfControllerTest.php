<?php

test('qr endpoint returns downloadable svg content for provided url', function () {
    $response = $this->get(route('qr.pdf', [
        'url' => 'https://example.com/event',
    ]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/svg+xml');
    expect($response->headers->get('Content-Disposition'))->toContain('attachment; filename="qr-code-');
    expect($response->getContent())->toContain('<svg');
});
