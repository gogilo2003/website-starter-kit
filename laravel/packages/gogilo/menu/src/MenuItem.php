<?php

namespace Gogilo\Menu;

class MenuItem
{
    public function __construct(
        public string $name,
        public string $caption,
        public ?string $icon = null,
        public ?string $route = null,
        public ?string $url = null,
        public array $altNames = [],
        public int $order = 100,
        public ?string $group = null,
        public mixed $children = null,
        public ?string $ability = null,
    ) {}

    public function toArray(): array
    {
        $children = $this->children;

        if (is_callable($children)) {
            $children = call_user_func($children);
        }

        if (! is_array($children)) {
            $children = [];
        }

        return [
            'name' => $this->name,
            'caption' => $this->caption,
            'icon' => $this->icon,
            'route' => $this->route,
            'url' => $this->url,
            'alt_names' => $this->altNames,
            'order' => $this->order,
            'group' => $this->group,
            'items' => $children,
            'children' => $children,
        ];
    }
}
