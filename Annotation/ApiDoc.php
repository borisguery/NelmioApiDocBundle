<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\ApiDocBundle\Annotation;

use Symfony\Component\Routing\Route;

/**
 * @Annotation
 */
class ApiDoc
{
    /**
     * Requirements are mandatory parameters in a route.
     *
     * @var array
     */
    private $requirements = array();

    /**
     * Filters are optional parameters in the query string.
     *
     * @var array
     */
    private $filters  = array();

    /**
     * Parameters are data a client can send.
     *
     * @var array
     */
    private $parameters = array();

    /**
     * @var string
     */
    private $input = null;

    /**
     * @var string
     */
    private $output = null;

    /**
     * Most of the time, a single line of text describing the action.
     *
     * @var string
     */
    private $description = null;

    /**
     * Extended documentation.
     *
     * @var string
     */
    private $documentation = null;

    /**
     * @var Boolean
     */
    private $isResource = false;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $response = array();

    /**
     * @var Route
     */
    private $route;

    /**
     * @var boolean
     */
    private $https = false;

    /**
     * @var string
     */
    private $resourceKey;

    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $statusCodes = array();

    public function __construct(array $data)
    {
        $this->isResource = isset($data['resource']) && $data['resource'];

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        if (isset($data['input'])) {
            $this->input = $data['input'];
        } elseif (isset($data['filters'])) {
            foreach ($data['filters'] as $filter) {
                if (!isset($filter['name'])) {
                    throw new \InvalidArgumentException('A "filter" element has to contain a "name" attribute');
                }

                $name = $filter['name'];
                unset($filter['name']);

                $this->addFilter($name, $filter);
            }
        }

        if (isset($data['output'])) {
            $this->output = $data['output'];
        }

        if (isset($data['statusCodes'])) {
            $this->statusCodes = $data['statusCodes'];
        }

        $this->isResource = isset($data['resource']) && $data['resource'];

        if ($this->isResource && isset($data['resourceKey'])) {
            $this->resourceKey = $data['resourceKey'];
        }

        $this->model = isset($data['model']) ? $data['model'] : null;
    }

    /**
     * @param string $name
     * @param array  $filter
     */
    public function addFilter($name, array $filter)
    {
        $this->filters[$name] = $filter;
    }

    /**
     * @param string $name
     * @param array  $requirement
     */
    public function addRequirement($name, array $requirement)
    {
        $this->requirements[$name] = $requirement;
    }

    /**
     * @param array $requirements
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = array_merge($this->requirements, $requirements);
    }

    /**
     * @return string|null
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return string|null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $documentation
     */
    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }

    /**
     * @return Boolean
     */
    public function isResource()
    {
        return $this->isResource;
    }

    /**
     * @param string $name
     * @param array  $parameter
     */
    public function addParameter($name, array $parameter)
    {
        $this->parameters[$name] = $parameter;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets the responsed data as processed by the parsers - same format as parameters
     *
     * @param array $response
     */
    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * @param Route $route
     */
    public function setRoute(Route $route)
    {
        $this->route  = $route;
        $this->uri    = $route->getPattern();
        $this->method = $route->getRequirement('_method') ?: 'ANY';
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return boolean
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * @param boolean $https
     */
    public function setHttps($https)
    {
        $this->https = $https;
    }

    public function getResourceKey()
    {
        return $this->resourceKey;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'method' => $this->method,
            'uri'    => $this->uri,
        );

        if ($description = $this->description) {
            $data['description'] = $description;
        }

        if ($documentation = $this->documentation) {
            $data['documentation'] = $documentation;
        }

        if ($filters = $this->filters) {
            $data['filters'] = $filters;
        }

        if ($parameters = $this->parameters) {
            $data['parameters'] = $parameters;
        }

        if ($requirements = $this->requirements) {
            $data['requirements'] = $requirements;
        }

        if ($response = $this->response) {
            $data['response'] = $response;
        }

        if ($statusCodes = $this->statusCodes) {
            $data['statusCodes'] = $statusCodes;
        }

        $data['https'] = $this->https;

        if ($resourceKey = $this->getResourceKey()) {
            $data['resourceKey'] = $resourceKey;
        }

        if ($model = $this->getModel()) {
            $data['model'] = $model;
        }

        return $data;
    }
}
