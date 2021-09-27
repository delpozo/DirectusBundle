<?php

namespace Bundle\DirectusBundle\Client;

use Symfony\Component\HttpFoundation\JsonResponse;

class RemoteReaderClient extends AbstractClient {
    /**
     * @var string[]
     */
    protected $allowedHttpMethods = ['GET','POST','SEARCH'];

    public function getOneItem(string $collection, int $id): array
    {
        $responseArray = $this->doEndpointRequest(AbstractClient::ITEMS_MANY_ENDPOINT, [
            'collection' => $collection,
            'id' => $id
        ]);

        return $responseArray[0] ?? [];
    }

    public function getAll(string $collection): array
    {
        return $this->doEndpointRequest(AbstractClient::ITEMS_MANY_ENDPOINT, ['collection' => $collection]) ?: [];
    }

    public function findAllBy(string $collection): array
    {
        return $this->doEndpointRequest(AbstractClient::ITEMS_MANY_ENDPOINT, ['collection' => $collection]) ?: [];
    }
}
