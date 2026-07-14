<?php

namespace Gogilo\Menu;

use Illuminate\Support\Facades\Gate;

class MenuRegistry
{
    protected array $menus = [
        'admin' => [],
        'public' => [],
    ];

    public function register(string $menu, MenuItem $item): static
    {
        if (! isset($this->menus[$menu])) {
            $this->menus[$menu] = [];
        }

        // Keyed by name so packages can override core-registered items.
        $this->menus[$menu][$item->name] = $item;

        return $this;
    }

    public function registerMany(string $menu, array $items): static
    {
        foreach ($items as $item) {
            if ($item instanceof MenuItem) {
                $this->register($menu, $item);
            }
        }

        return $this;
    }

    public function has(string $menu): bool
    {
        return isset($this->menus[$menu]);
    }

    public function resolve(string $menu): array
    {
        if (! isset($this->menus[$menu])) {
            return [];
        }

        $resolved = [];

        foreach ($this->menus[$menu] as $item) {
            if ($item->ability && Gate::denies($item->ability)) {
                continue;
            }

            $resolved[] = $item->toArray();
        }

        usort($resolved, function (array $a, array $b) {
            return $a['order'] <=> $b['order']
                ?: strcmp((string) $a['caption'], (string) $b['caption']);
        });

        return $resolved;
    }

    public function resolveAll(): array
    {
        return [
            'admin' => $this->resolve('admin'),
            'public' => $this->resolve('public'),
        ];
    }
}
