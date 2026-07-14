<?php

namespace Gogilo\Products\Services;

use App\Services\PictureService;
use Gogilo\Products\Repositories\ProductRepositoryInterface;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    protected PictureService $pictureService;

    public function __construct(ProductRepositoryInterface $productRepository, PictureService $pictureService)
    {
        $this->productRepository = $productRepository;
        $this->pictureService = $pictureService;
    }

    public function getAllProducts(array $params = [], bool $mapped = false)
    {
        return $this->productRepository->all($params, $mapped);
    }

    public function getProductById(int $id, array $params = []): ?object
    {
        return $this->productRepository->find($id, $params);
    }

    public function getProductBySlug(string $slug, array $params = []): ?object
    {
        return $this->productRepository->findBySlug($slug, $params);
    }

    public function createProduct(array $data): object
    {
        $product = $this->productRepository->create($data);

        if (isset($data['picture'])) {
            $this->pictureService->createPicture($product, [
                'path' => 'products',
                'picture' => $data['picture'],
                'caption' => $product->title,
                'is_primary' => true,
            ]);
        }

        return $product;
    }

    public function updateProduct(int $id, array $data): object
    {
        $product = $this->productRepository->update($id, $data);

        if (isset($data['picture'])) {
            $this->pictureService->createPicture($product, [
                'path' => 'products',
                'picture' => $data['picture'],
                'caption' => $product->title,
                'is_primary' => true,
            ]);
        }

        return $product;
    }
}
