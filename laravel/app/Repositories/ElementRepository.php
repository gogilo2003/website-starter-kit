<?php

namespace App\Repositories;

use App\Models\Element;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use App\Interfaces\Repositories\ElementRepositoryInterface;

class ElementRepository implements ElementRepositoryInterface
{
    public function all(int $perPage = 15, ?string $search = null): Collection|LengthAwarePaginator|SupportCollection
    {
        $query = Element::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        return $perPage > 0
            ? $query->paginate($perPage)
            ->through(
                fn(Element $element) => $this->mapElement($element)
            )
            : $query->get()
            ->map(
                fn(Element $element) => $this->mapElement($element)
            );
    }

    protected function mapElement(Element $element)
    {
        return (object)[
            'id' => $element->id,
            'name' => $element->name,
            'title' => ucwords(strtolower($element->title)),
            'content' => $element->content,
            'icon' => $element->icon,
            'type' => $element->type,
            'photo' => $element->photo,
            'published' => $element->published ? true : false,
        ];
    }

    public function find(int $id): ?Element
    {
        return Element::find($id);
    }
    public function findByName(string $name): ?Element
    {
        return Element::where('name', $name)->first();
    }

    public function create(array $data): Element
    {
        $element = new Element();
        $element->name = $data['name'];
        $element->title = $data['title'];
        $element->content = $data['content'];
        $element->type = $data['type'] ?? null;
        $element->photo = $data['photo'] ?? null;
        $element->icon = $data['icon'] ?? null;
        $element->published = $data['published'] ?? false;
        $element->save();

        return $element;
    }

    public function update(int $id, array $data): bool
    {
        $element = $this->find($id);
        if (!$element) {
            return false;
        }

        $element->name = $data['name'] ?? $element->name;
        $element->title = $data['title'] ?? $element->title;
        $element->content = $data['content'] ?? $element->content;
        $element->type = $data['type'] ?? $element->type;
        $element->photo = $data['photo'] ?? $element->photo;
        $element->icon = $data['icon'] ?? $element->icon;
        $element->published = $data['published'] ?? $element->published;

        return $element->save();
    }

    public function delete(int $id): bool
    {
        $element = $this->find($id);
        if (!$element) {
            return false;
        }
        return $element->delete();
    }

    public function syncPageSections(int $elementId, array $pageSectionIds): array
    {
        $element = $this->find($elementId);
        if (!$element) {
            return [];
        }
        return $element->pageSections()->sync($pageSectionIds);
    }
}
