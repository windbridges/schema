<?php

namespace WindBridges\Schema;

use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;

trait WithAnnotationValidator
{
    /** @var array */
    private $__schema;

    /**
     * Creates new object from data array and validates it
     *
     * @param $data
     * @param string|null $displayPath
     * @return static
     * @throws Exception
     */
    static function create($data, ?string $displayPath = null)
    {
        $obj = new static;
        $obj->apply($data, $displayPath);
        $obj->validate($displayPath);

        return $obj;
    }

    /**
     * Applies array of data to this object's properties with 'type' annotation.
     * If property has a setter, then it will be used.
     * Otherwise value is written directly to property.
     *
     * @param array $data
     * @param string|null $displayPath
     * @param bool $useSetters
     * @throws Exception
     */
    public function apply(array $data, ?string $displayPath = null, bool $useSetters = true)
    {
        if (!$this->__schema) {
            $parser = new AnnotationParser(static::class);
            $this->__schema = $parser->getSchema();
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $name => $value) {
            if (array_key_exists($name, $this->__schema['props'])) {
                if ($useSetters && $accessor->isWritable($this, $name)) {
                    $accessor->setValue($this, $name, $value);
                } else {
                    $this->{$name} = $value;
                }
            } else {
                $displayPath = $displayPath ? $displayPath . '.' : '';
                throw new Exception("Unknown property: {$displayPath}{$name}");
            }
        }
    }

    /**
     * Validates object's properties against annotation rules
     *
     * @param string|null $displayPath
     * @throws Exception
     */
    public function validate(?string $displayPath = null)
    {
        if (!$this->__schema) {
            $parser = new AnnotationParser(static::class);
            $this->__schema = $parser->getSchema();
        }

        $data = [];
        $this->preValidate($displayPath);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->__schema['props'] as $name => $prop) {
            if ($accessor->isReadable($this, $name)) {
                $data[$name] = $accessor->getValue($this, $name);
            } else {
                $data[$name] = $this->{$name};
            }
        }

        $model = new Model($this->__schema);
        $data = $model->create($data, $displayPath);
        $this->apply($data, $displayPath);
        $this->postValidate($displayPath);
    }

    protected function preValidate($displayPath)
    {
        # for overriding
    }

    protected function postValidate($displayPath)
    {
        # for overriding
    }
}