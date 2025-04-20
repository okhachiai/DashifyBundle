<?php

namespace Dashify\DashifyBundle\Registry;

use Dashify\DashifyBundle\Attribute\AsDashifyResource;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class ResourceRegistry
{
    private array $resources = [];

    public function __construct(
        private readonly ParameterBagInterface $params
    ) {
        $this->loadResources();
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getResource(string $class): ?array
    {
        return $this->resources[$class] ?? null;
    }

    private function loadResources(): void
    {
        $projectDir = $this->params->get('kernel.project_dir');
        $finder = new Finder();
        $finder->files()->in($projectDir . '/src/Entity')->name('*.php');

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file);
            if (!$className || !class_exists($className)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            $attributes = $reflection->getAttributes(AsDashifyResource::class);

            if (empty($attributes)) {
                continue;
            }

            $attribute = $attributes[0]->newInstance();
            $this->resources[$className] = [
                'class' => $className,
                'name' => $attribute->name ?? $this->getDefaultResourceName($className),
                'pluralName' => $attribute->pluralName ?? $this->getDefaultPluralName($className),
                'icon' => $attribute->icon,
                'group' => $attribute->group,
                'actions' => $attribute->actions,
                'searchableFields' => $attribute->searchableFields,
                'sortableFields' => $attribute->sortableFields,
                'filterableFields' => $attribute->filterableFields,
            ];
        }
    }

    private function getClassNameFromFile(\SplFileInfo $file): ?string
    {
        $contents = file_get_contents($file->getRealPath());
        if (preg_match('/namespace\s+(.+?);/', $contents, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/', $contents, $matches)) {
                return $namespace . '\\' . $matches[1];
            }
        }
        return null;
    }

    private function getDefaultResourceName(string $class): string
    {
        $parts = explode('\\', $class);
        return end($parts);
    }

    private function getDefaultPluralName(string $class): string
    {
        return $this->getDefaultResourceName($class) . 's';
    }
} 