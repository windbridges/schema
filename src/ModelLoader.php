<?php

namespace WindBridges\Schema;

use Symfony\Component\Yaml\Yaml;

class ModelLoader
{
    public function fromArray(array $schema)
    {
        return new Model($schema);
    }

    public function fromYamlFile($schemaFilePath, $rootNode = null)
    {
        $schema = Yaml::parseFile($schemaFilePath);

        if ($rootNode) {
            $schema = $schema[$rootNode];
        }

        return $this->fromArray($schema);
    }

    public function fromYaml($schemaYaml)
    {
        $schema = Yaml::parse($schemaYaml);
        return $this->fromArray($schema);
    }

    public function fromJson($schemaJson)
    {
        $schema = \GuzzleHttp\json_decode($schemaJson);
        return $this->fromArray($schema);
    }
}