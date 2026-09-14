<?php

declare(strict_types=1);

use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Pagination\CursorPagination;
use Govia\WiseClient\Http\Paginator;
use Govia\WiseClient\Http\Response;

it('walks every cursor page until the response stops returning a cursor', function () {
    $pages = [
        new Response(200, ['activities' => [['id' => 1], ['id' => 2]], 'cursor' => 'page-2'], []),
        new Response(200, ['activities' => [['id' => 3]], 'cursor' => null], []),
    ];

    $http = new class($pages) implements HttpClientInterface
    {
        private int $call = 0;

        public function __construct(private readonly array $pages) {}

        public function get(string $uri, array $query = []): Response
        {
            return $this->pages[$this->call++];
        }

        public function post(string $uri, array $body = [], array $headers = []): Response
        {
            throw new RuntimeException('not used');
        }

        public function put(string $uri, array $body = []): Response
        {
            throw new RuntimeException('not used');
        }

        public function patch(string $uri, array $body = []): Response
        {
            throw new RuntimeException('not used');
        }

        public function delete(string $uri): Response
        {
            throw new RuntimeException('not used');
        }
    };

    $paginator = new Paginator($http, '/profiles/1/activities', new CursorPagination(itemsKey: 'activities'));

    expect(iterator_to_array($paginator))->toHaveCount(3);
});
