<?php

namespace App\Repositories;

use App\Models\Element;
use App\Models\PageSection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Interfaces\Repositories\PageSectionRepositoryInterface;

class PageSectionRepository implements PageSectionRepositoryInterface
{
    public function all(int $perPage = 0, ?string $search = null, array $relations = [], bool $mapped = false)
    {
        $query = PageSection::query();

        if (!empty($relations)) {
            $query->with($relations);
        } else {
            $query->with('elements');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($mapped) {
            return $perPage > 0
                ? $query->paginate($perPage)->through(fn($item) => $this->mapPageSection($item))
                : $query->get()->map(fn($item) => $this->mapPageSection($item));
        }

        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    protected function mapPageSection(PageSection $pageSection)
    {
        return (object) [
            "id" => $pageSection->id,
            "name" => $pageSection->name,
            "title" => $pageSection->title,
            "description" => $pageSection->description,
            "elements" => $pageSection->elements->map(fn(Element $element) => (object)[
                'id' => $element->id,
                'name' => $element->name,
                'title' => ucwords(strtolower($element->title)),
                'content' => $element->content,
                'icon' => $element->icon,
                'type' => $element->type,
                'photo' => Storage::disk('public')->url($element->photo),
                'published' => $element->published ? true : false,
            ])->all(),
        ];
    }
    public function find(int $id): ?PageSection
    {
        return PageSection::find($id);
    }

    public function create(array $data): PageSection
    {
        // return PageSection::create($data);
        $pageSection = new PageSection();
        $pageSection->name = $data['name'];
        $pageSection->title = $data['title'];
        $pageSection->description = $data['description'];
        $pageSection->save();
        return $pageSection;
    }

    public function update(int $id, array $data): bool
    {
        $pageSection = $this->find($id);
        if (!$pageSection) {
            return false;
        }
        $pageSection->name = $data['name'];
        $pageSection->title = $data['title'];
        $pageSection->description = $data['description'];
        return $pageSection->save();
    }

    public function delete(int $id): bool
    {
        $pageSection = $this->find($id);
        if (!$pageSection) {
            return false;
        }
        return $pageSection->delete();
    }

    public function syncElements(int $pageSectionId, array $elementIds): array
    {
        $pageSection = $this->find($pageSectionId);
        if (!$pageSection) {
            return [];
        }
        return $pageSection->elements()->sync($elementIds);
    }
}
