<?php
namespace Omeka\Api;

/**
 * API request.
 */
class Request
{
    const SEARCH = 'search';
    const CREATE = 'create';
    const BATCH_CREATE = 'batch_create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @var array
     */
    protected $operations = [
        self::SEARCH, self::CREATE, self::BATCH_CREATE,
        self::READ, self::UPDATE, self::DELETE,
    ];

    /**
     * @var string
     */
    protected $operation;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var array
     */
    protected $fileData = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $content = [];

    /**
     * Construct an API request.
     *
     * @throws Exception\BadRequestException
     * @param string $operation The request operation
     * @param string $resource The request resource
     */
    public function __construct($operation, $resource)
    {
        if (!in_array($operation, $this->operations)) {
            throw new Exception\BadRequestException(sprintf(
                'The API does not support the "%s" request operation.',
                $operation
            ));
        }
        if (!is_string($resource)) {
            throw new Exception\BadRequestException(sprintf(
                'The API request resource must be a string. Type "%s" given.',
                gettype($resource)
            ));
        }
        if ('' === $resource) {
            throw new Exception\BadRequestException('The API request must include a resource. None given.');
        }

        $this->operation = $operation;
        $this->resource = $resource;
    }

    /**
     * Get the request operation.
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Get the request resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the request resource ID.
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the request resource ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the file data for the request.
     *
     * @param array $fileData
     */
    public function setFileData(array $fileData)
    {
        $this->fileData = $fileData;
        return $this;
    }

    /**
     * Get the file data for the request.
     *
     * @return array
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Set a request option or options.
     *
     * Options that affect the execution of a request are:
     *
     * - initialize: (bool) Set whether to initialize the request during
     *      execute() (e.g. trigger API-pre events). Default is true.
     * - finalize: (bool) Set whether to finalize the request during execute()
     *      (e.g. trigger API-post events). Default is true.
     * - isPartial: (bool) Set whether this is a partial UPDATE request (aka
     *      PATCH). Default is false.
     * - continueOnError: (bool) Set whether a BATCH_CREATE operation should
     *      continue processing on error. Default is false.
     * - returnResource: (bool) Set whether to return an API resource instead of
     *      a representation of an API resource during CREATE, UPDATE, and
     *      DELETE. Default is false.
     * - flushEntityManager: (bool) Set whether to flush the entity manager
     *      during CREATE, UPDATE, and DELETE. Default is true.
     *
     * @param string|int|array $spec
     * @param mixed $value
     */
    public function setOption($spec, $value = null)
    {
        if (is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->options[$key] = $value;
            }
        } else {
            $this->options[$spec] = $value;
        }
        return $this;
    }

    /**
     * Get all options or a single option as specified by key.
     *
     * @param null|string|int $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getOption($key = null, $default = null)
    {
        if (null === $key) {
            return $this->options;
        }
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return $default;
    }

    /**
     * Set request content.
     *
     * The API request content must always be an array.
     *
     * @param array $value
     */
    public function setContent(array $value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * Get request content.
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get a value from the content by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        $data = $this->getContent();
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }
}
