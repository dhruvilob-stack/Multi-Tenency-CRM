<?php

namespace App\Support;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MasterCatalogSync
{
    /**
     * @return Collection<int, Organization>
     */
    public function partnerOrganizations(): Collection
    {
        return Organization::query()
            ->whereIn('type', ['buyer', 'supplier'])
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
    }

    public function isMasterRecord(Model $record): bool
    {
        if (! method_exists($record, 'organization')) {
            return false;
        }

        $organization = $record->organization;

        return $organization instanceof Organization && $organization->type === 'manufacturer';
    }

    public function ensureSyncedForPartnerOrganization(int $partnerOrganizationId): void
    {
        $cacheKey = "master_catalog_synced_for_org_{$partnerOrganizationId}";

        if (Cache::get($cacheKey)) {
            return;
        }

        $this->syncAllToPartnerOrganization($partnerOrganizationId);

        Cache::put($cacheKey, true, now()->addHours(12));
    }

    public function syncBrand(Brand $masterBrand): void
    {
        if (! $this->isMasterRecord($masterBrand) || filled($masterBrand->master_brand_id)) {
            return;
        }

        foreach ($this->partnerOrganizations() as $partnerOrganization) {
            $this->upsertBrandClone($partnerOrganization->getKey(), $masterBrand);
        }
    }

    public function syncCategory(Category $masterCategory): void
    {
        if (! $this->isMasterRecord($masterCategory) || filled($masterCategory->master_category_id)) {
            return;
        }

        foreach ($this->partnerOrganizations() as $partnerOrganization) {
            $this->upsertCategoryClone($partnerOrganization->getKey(), $masterCategory);
        }
    }

    public function syncProduct(Product $masterProduct): void
    {
        if (! $this->isMasterRecord($masterProduct) || filled($masterProduct->master_product_id)) {
            return;
        }

        foreach ($this->partnerOrganizations() as $partnerOrganization) {
            $this->upsertProductClone($partnerOrganization->getKey(), $masterProduct);
        }
    }

    private function syncAllToPartnerOrganization(int $partnerOrganizationId): void
    {
        $masterBrands = Brand::query()
            ->withoutGlobalScope('organization')
            ->whereNull('master_brand_id')
            ->whereHas('organization', fn ($query) => $query->where('type', 'manufacturer'))
            ->orderBy('id')
            ->get();

        foreach ($masterBrands as $brand) {
            $this->upsertBrandClone($partnerOrganizationId, $brand);
        }

        $masterCategories = Category::query()
            ->withoutGlobalScope('organization')
            ->whereNull('master_category_id')
            ->whereHas('organization', fn ($query) => $query->where('type', 'manufacturer'))
            ->orderBy('id')
            ->get();

        foreach ($masterCategories as $category) {
            $this->upsertCategoryClone($partnerOrganizationId, $category);
        }

        $masterProducts = Product::query()
            ->withoutGlobalScope('organization')
            ->whereNull('master_product_id')
            ->whereHas('organization', fn ($query) => $query->where('type', 'manufacturer'))
            ->orderBy('id')
            ->get();

        foreach ($masterProducts as $product) {
            $this->upsertProductClone($partnerOrganizationId, $product);
        }
    }

    private function upsertBrandClone(int $partnerOrganizationId, Brand $masterBrand): Brand
    {
        $cloneQuery = Brand::query()
            ->withoutGlobalScope('organization')
            ->where('organization_id', $partnerOrganizationId)
            ->where('master_brand_id', $masterBrand->getKey());

        $clone = $cloneQuery->first();

        if (! $clone) {
            $clone = new Brand;
        }

        $clone->forceFill([
            'organization_id' => $partnerOrganizationId,
            'master_brand_id' => $masterBrand->getKey(),
            'name' => $masterBrand->name,
            'is_active' => $masterBrand->is_active,
        ])->save();

        return $clone;
    }

    private function upsertCategoryClone(int $partnerOrganizationId, Category $masterCategory): Category
    {
        $parentCloneId = null;

        if (filled($masterCategory->parent_id)) {
            $parent = Category::query()
                ->withoutGlobalScope('organization')
                ->find($masterCategory->parent_id);

            if ($parent instanceof Category && blank($parent->master_category_id)) {
                $parentCloneId = $this->upsertCategoryClone($partnerOrganizationId, $parent)->getKey();
            }
        }

        $cloneQuery = Category::query()
            ->withoutGlobalScope('organization')
            ->where('organization_id', $partnerOrganizationId)
            ->where('master_category_id', $masterCategory->getKey());

        $clone = $cloneQuery->first();

        if (! $clone) {
            $clone = new Category;
        }

        $clone->forceFill([
            'organization_id' => $partnerOrganizationId,
            'master_category_id' => $masterCategory->getKey(),
            'parent_id' => $parentCloneId,
            'name' => $masterCategory->name,
            'is_active' => $masterCategory->is_active,
        ])->save();

        return $clone;
    }

    private function upsertProductClone(int $partnerOrganizationId, Product $masterProduct): Product
    {
        $brandCloneId = $this->resolveBrandCloneId($partnerOrganizationId, (int) $masterProduct->brand_id);
        $categoryCloneId = $this->resolveCategoryCloneId($partnerOrganizationId, (int) $masterProduct->category_id);

        $cloneQuery = Product::query()
            ->withoutGlobalScope('organization')
            ->where('organization_id', $partnerOrganizationId)
            ->where('master_product_id', $masterProduct->getKey());

        $clone = $cloneQuery->first();

        if (! $clone) {
            $clone = new Product;
        }

        $clone->forceFill([
            'organization_id' => $partnerOrganizationId,
            'master_product_id' => $masterProduct->getKey(),
            'brand_id' => $brandCloneId,
            'category_id' => $categoryCloneId,
            'name' => $masterProduct->name,
            'sku' => $masterProduct->sku,
            'price_cents' => $masterProduct->price_cents,
            'sustainability_score' => $masterProduct->sustainability_score,
            'carbon_kg_per_unit' => $masterProduct->carbon_kg_per_unit,
            'is_active' => $masterProduct->is_active,
            'description' => $masterProduct->description,
            'variant_options' => $masterProduct->variant_options,
        ])->save();

        return $clone;
    }

    private function resolveBrandCloneId(int $partnerOrganizationId, int $masterBrandId): int
    {
        $existingCloneId = Brand::query()
            ->withoutGlobalScope('organization')
            ->where('organization_id', $partnerOrganizationId)
            ->where('master_brand_id', $masterBrandId)
            ->value('id');

        if ($existingCloneId) {
            return (int) $existingCloneId;
        }

        $masterBrand = Brand::query()
            ->withoutGlobalScope('organization')
            ->whereNull('master_brand_id')
            ->find($masterBrandId);

        if (! $masterBrand) {
            throw new \RuntimeException("Master brand #{$masterBrandId} not found.");
        }

        return (int) $this->upsertBrandClone($partnerOrganizationId, $masterBrand)->getKey();
    }

    private function resolveCategoryCloneId(int $partnerOrganizationId, int $masterCategoryId): int
    {
        $existingCloneId = Category::query()
            ->withoutGlobalScope('organization')
            ->where('organization_id', $partnerOrganizationId)
            ->where('master_category_id', $masterCategoryId)
            ->value('id');

        if ($existingCloneId) {
            return (int) $existingCloneId;
        }

        $masterCategory = Category::query()
            ->withoutGlobalScope('organization')
            ->whereNull('master_category_id')
            ->find($masterCategoryId);

        if (! $masterCategory) {
            throw new \RuntimeException("Master category #{$masterCategoryId} not found.");
        }

        return (int) $this->upsertCategoryClone($partnerOrganizationId, $masterCategory)->getKey();
    }
}
