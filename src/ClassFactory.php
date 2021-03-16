<?php

namespace WindBridges\Schema;


use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use WindBridges\Schema\Type\ValidationException;

class ClassFactory
{
    protected $className;
    protected $model;


    public function __construct(string $className, array $schema = null)
    {
        $this->className = $className;

        if (!$schema) {
            # convert annotation schema to regular
            $parser = new AnnotationParser($className);
            $schema = $parser->getSchema();
        }

        # getting validated and type-casted data
        $this->model = new Model($schema);
    }

    public function create(array $data, string $displayPath = null)
    {
        # this may be extended in future to provide required arguments to construct
        $object = new $this->className;
        $displayPath = $displayPath ?: $this->className;

        $data = $this->callPreValidate($object, $data, $displayPath);
        $validatedData = $this->model->create($data, $displayPath);

        # remove properties that are not specified in $data to keep object's default values
        $validatedData = array_filter($validatedData, function (string $key) use ($data) {
            return array_key_exists($key, $data);// || array_key_exists('default', $this->model->getSchema()['props'][$key]);
        }, ARRAY_FILTER_USE_KEY);

        $this->apply($object, $validatedData, $displayPath);

        $this->callPostValidate($object, $displayPath);

        return $object;
    }

    /**
     * Applies array of data to this object's properties with 'type' annotation.
     * If property has a setter, then it will be used.
     * Otherwise value is written directly to property.
     *
     * @param $object
     * @param array $data
     * @param string|null $displayPath
     * @param bool $useSetters
     * @throws ValidationException|ReflectionException
     */
    protected function apply($object, array $data, ?string $displayPath = null, bool $useSetters = true)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $schema = $this->model->getSchema();

        foreach ($data as $name => $value) {
            if (array_key_exists($name, $schema['props'])) {
                if ($useSetters && $accessor->isWritable($object, $name)) {
                    $accessor->setValue($object, $name, $value);
                } else {
                    $ref = new ReflectionProperty($object, $name);

                    if ($ref->isProtected() || $ref->isPrivate()) {
                        $ref->setAccessible(true);
                        $ref->setValue($object, $value);
                        $ref->setAccessible(false);
                    } else {
                        $object->{$name} = $value;
                    }
                }
            } else {
                $displayPath = $displayPath ? $displayPath . '.' : '';
                throw new ValidationException("Unknown property: {$displayPath}{$name}");
            }
        }
    }

    /**
     * Handler prototype:
     * protected function preValidate(array &$data, string $displayPath)
     *
     * @param $object
     * @param array $data
     * @param string $displayPath
     * @return array|mixed
     * @throws ReflectionException
     */
    protected function callPreValidate(object $object, array $data, string $displayPath)
    {
        if (method_exists($object, 'preValidate')) {
            $ref = new ReflectionObject($object);
            $method = $ref->getMethod('preValidate');
            $method->setAccessible(true);
            $resultData = $method->invoke($object, $data, $displayPath);

            if ($resultData !== null) {
                $data = $resultData;
            }

            $method->setAccessible(false);
        }

        return $data;
    }

    /**
     * Handler prototype:
     * protected function postValidate($displayPath)
     *
     * @param object $object
     * @param string $displayPath
     * @throws ReflectionException
     */
    protected function callPostValidate(object $object, string $displayPath)
    {
        if (method_exists($object, 'postValidate')) {
            $ref = new ReflectionObject($object);
            $method = $ref->getMethod('postValidate');
            $method->setAccessible(true);
            $method->invoke($object, $displayPath);
            $method->setAccessible(false);
        }
    }
}