<?php

namespace Dashify\DashifyBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AsDashifyResource
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $pluralName = null,
        public readonly ?string $icon = null,
        public readonly ?string $group = null,
        public readonly ?array $actions = ['index', 'create', 'edit', 'delete'],
        public readonly ?array $searchableFields = [],
        public readonly ?array $sortableFields = [],
        public readonly ?array $filterableFields = [],
    ) {
    }
} 