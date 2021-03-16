<?php


namespace WindBridges\Schema;


trait WithModelExport
{
    function export()
    {
        # convert annotation schema to regular
        $parser = new AnnotationParser($this);
        $schema = $parser->getSchema();

        # getting validated and type-casted data
        $model = new Model($schema);
        $data = [];

        foreach ($model->getSchema()['props'] as $name => $schema) {
            $value = $this->$name;

            if (is_object($value) && method_exists($value, 'export')) {
                $value = $value->export();
            }

            $data[$name] = $value;
        }

        return $model->create($data, get_class($this));
    }
}