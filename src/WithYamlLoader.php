<?php

namespace WindBridges\Schema;


use Exception;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

trait WithYamlLoader
{
    static public function fromYaml(string $yaml, ?string $displayPath = null)
    {
        if (method_exists(static::class, 'create')) {
            $data = Yaml::parse($yaml);

            return static::create($data ?: [], $displayPath);
        } else {
            throw new Exception("Trait WithAnnotationValidator should be used together with WithYamlLoader");
        }
    }

    static public function fromYamlFile(string $path, ?string $displayPath = null)
    {
        if (method_exists(static::class, 'create')) {
            Assert::fileExists($path);
            $data = Yaml::parseFile($path);

            return static::create($data ?: [], $displayPath);
        } else {
            throw new Exception("Trait WithAnnotationValidator should be used together with WithYamlLoader");
        }
    }
}