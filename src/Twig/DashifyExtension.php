<?php

namespace Dashify\DashifyBundle\Twig;

use Dashify\DashifyBundle\Registry\ResourceRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DashifyExtension extends AbstractExtension
{
    public function __construct(
        private readonly ResourceRegistry $registry
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dashify_resources', [$this, 'getResources']),
            new TwigFunction('dashify_resource', [$this, 'getResource']),
            new TwigFunction('dashify_resource_value', [$this, 'getResourceValue']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('dashify_humanize', [$this, 'humanize']),
        ];
    }

    public function getResources(): array
    {
        return $this->registry->getResources();
    }

    public function getResource(string $class): ?array
    {
        return $this->registry->getResource($class);
    }

    public function getResourceValue(object $entity, string $property): mixed
    {
        $getter = 'get' . ucfirst($property);
        if (method_exists($entity, $getter)) {
            return $entity->$getter();
        }

        $isser = 'is' . ucfirst($property);
        if (method_exists($entity, $isser)) {
            return $entity->$isser();
        }

        if (property_exists($entity, $property)) {
            return $entity->$property;
        }

        return null;
    }

    public function humanize(string $text): string
    {
        return ucfirst(strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text))));
    }
} 