<?php

namespace Bundle\DirectusBundle\Client;

use Sba\DirectusSymfonyBundle\Exception\NotSubstitutedVariableException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function Symfony\Component\String\u;

abstract class AbstractClient {
    const AUTH_AUTHENTICATE_ENDPOINT = 'auth/authenticate';
    const AUTH_REFRESH_ENDPOINT = 'auth/refresh';
    const ITEMS_MANY_ENDPOINT = 'items/:collection';
    const ITEMS_ONE_ENDPOINT = 'items/:collection/:id';

    /**
     * @var string[]
     */
    protected $allowedHttpMethods = []; // Empty array here to enforce specification per child class.
    /**
     * @var ParameterBagInterface
     */
    private $params;
    /**
     * @var string
     */
    private $projectName;
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;

        if ($this->params->has('sba_directus_symfony.project_name')) {
            $this->projectName = $this->params->get('sba_directus_symfony.project_name');
        }
    }

    /**
     * Get the base url
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        if (null !== $this->baseUrl) {
            return $this->baseUrl;
        }

        $baseUrl = $this->params->get('sba_directus_symfony.base_url');

        if (null !== $this->projectName) {
            $baseUrl = u($baseUrl)->ensureEnd('/')->append($this->params->get('sba_directus_symfony.project_name'));
        }

        $this->baseUrl = $baseUrl;

        return $baseUrl;
    }

    public function doEndpointRequest(string $urlEndpoint, array $urlVariables): array
    {
        $url = u($this->getBaseUrl())->append($this->processEndpointUrl($urlEndpoint, $urlVariables));

        return [];
    }

    private function processEndpointUrl(string $urlEndpoint, array $urlVariables): string
    {
        $substitutedUrl = u($urlEndpoint);
        $allowedVariables = [
            'collections',
            'id'
        ];

        foreach ($urlVariables as $variable => $value) {
            $urlVariable = u($variable)->ensureStart(':');
            $substitutedUrl = $substitutedUrl->replace($urlVariable, $value);
        }

        if (null !== $substitutedUrl->indexOf(':')) {
            throw new NotSubstitutedVariableException('Not all variables in the endpoint "'.$urlEndpoint.'" are substituted. Check for missing variables in the partial substituted endpoint: "'.$substitutedUrl.'"');
        }

        return $substitutedUrl;
    }
}
