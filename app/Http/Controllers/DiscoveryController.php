<?php

namespace App\Http\Controllers;

use App\Actions\Listings\RecordDiscoveryInteraction;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\HomeSelection;
use App\Models\Listing;
use App\Queries\PublicListingQuery;
use App\Support\DiscoveryPageData;
use App\Support\PublicIndexability;
use App\Support\PublicTaxonomy;
use App\Support\PublicUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscoveryController extends Controller
{
    public function __construct(private PublicListingQuery $listings, private PublicTaxonomy $public, private DiscoveryPageData $page, private RecordDiscoveryInteraction $interactions) {}

    public function home(Request $request, string $city): Response
    {
        $city = $this->public->city($city);
        [$categories, $areas] = $this->navigation($city);
        $categories = $categories->filter(fn (Category $category): bool => $category->public_count > 0)->values();
        $areas = $areas->filter(fn (Area $area): bool => $area->public_count > 0)->values();
        $selectedIds = HomeSelection::query()->where('city_id', $city->id)->orderBy('position')->pluck('listing_id')->all();
        $featured = $this->listings->prepare($this->cards($city)->whereIn('id', $selectedIds)->where('operating_status', '!=', 'permanently_closed')->get())
            ->sortBy(fn (Listing $listing): int => array_search($listing->id, $selectedIds, true))->take(6)->values();
        $recent = $this->listings->prepare($this->cards($city)->whereNotIn('id', $featured->modelKeys())
            ->where('operating_status', '!=', 'permanently_closed')->orderByDesc('published_at')->orderByDesc('id')->limit(6)->get());
        $baseUrl = PublicUrl::city($city);
        $identity = $this->page->identity($city);
        $seo = $this->page->seo($city, $city->name, 'Negocios e información práctica de '.$city->name.'.', $baseUrl,
            ! PublicIndexability::discovery($this->listings->inCity($city)->get(['listings.id', 'is_demo'])));
        $breadcrumbs = [['label' => 'Inicio', 'url' => null]];
        $this->interactions->handle($request, 'city', $city);

        return response()->view('discovery.home', compact('city', 'categories', 'areas', 'featured', 'recent', 'identity', 'seo', 'breadcrumbs'));
    }

    public function businesses(Request $request, string $city): Response
    {
        return $this->index($request, $this->public->city($city));
    }

    public function category(Request $request, string $city, string $category): Response
    {
        return $this->index($request, $this->public->city($city), $this->public->category($category));
    }

    public function area(Request $request, string $city, string $area): Response
    {
        $city = $this->public->city($city);

        return $this->index($request, $city, area: $this->public->area($city, $area));
    }

    private function index(Request $request, City $city, ?Category $category = null, ?Area $area = null): Response
    {
        $filters = [];
        $filterCategory = $category;
        $filterArea = $area;
        if (! $category && ($slug = $this->filter($request, 'categoria')) !== null) {
            $filterCategory = $this->public->category($slug);
            $filters['categoria'] = $slug;
        }
        if (! $area && ($slug = $this->filter($request, 'zona')) !== null) {
            $filterArea = $this->public->area($city, $slug);
            $filters['zona'] = $slug;
        }
        $rawPage = $request->query('page', '1');
        abort_unless((is_string($rawPage) || is_int($rawPage)) && preg_match('/^[1-9][0-9]*$/D', (string) $rawPage)
            && filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 4294967295]]) !== false, 404);
        $pageNumber = (int) $rawPage;
        $baseUrl = $category ? PublicUrl::category($city, $category)
            : ($area ? PublicUrl::area($city, $area) : PublicUrl::businesses($city));
        $query = $this->cards($city)
            ->when($filterCategory, fn (Builder $query): Builder => $query->whereHas('categories', fn (Builder $query): Builder => $query->where('categories.id', $filterCategory->id)))
            ->when($filterArea, fn (Builder $query): Builder => $query->where('area_id', $filterArea->id));
        $listings = $query->orderByDesc('published_at')->orderByDesc('id')->paginate(12, ['*'], 'page', $pageNumber)->withPath($baseUrl)->appends($filters);
        abort_if($pageNumber > $listings->lastPage(), 404);
        $listings->setCollection($this->listings->prepare($listings->getCollection()));
        [$categories, $areas] = $this->navigation($city);
        $record = $category ?? $area ?? $city;
        $title = $category ? $category->name.' en '.$city->name : ($area ? 'Negocios en '.$area->name : 'Negocios en '.$city->name);
        $identity = $this->page->identity($record, $category?->name ?? ($area ? 'Negocios en '.$area->name : 'Todos los negocios'));
        if (! $category && ! $area) {
            $identity['tagline'] = 'Encuentra negocios por categoría y zona.';
            $identity['description'] = '';
        }
        $canonical = PublicUrl::canonical($baseUrl, $pageNumber, $filters !== []);
        $seo = $this->page->seo($record, $title.($pageNumber > 1 ? ' · Página '.$pageNumber : ''), 'Consulta las fichas y los datos prácticos de negocios en '.$city->name.'.', $canonical,
            ! PublicIndexability::discovery($listings->getCollection(), $filters !== []));
        $breadcrumbs = [['label' => 'Inicio', 'url' => PublicUrl::city($city)]];
        if ($category || $area) {
            $breadcrumbs[] = ['label' => 'Negocios', 'url' => PublicUrl::businesses($city)];
        }
        $breadcrumbs[] = ['label' => $category?->name ?? $area?->name ?? 'Negocios', 'url' => null];
        $this->interactions->handle($request, $category ? 'category' : ($area ? 'area' : 'businesses'), $city, $filterCategory, $filterArea, $pageNumber);

        return response()->view('discovery.index', compact('city', 'category', 'area', 'filterCategory', 'filterArea', 'filters', 'categories', 'areas', 'listings', 'baseUrl', 'identity', 'seo', 'breadcrumbs'));
    }

    private function filter(Request $request, string $key): ?string
    {
        $value = $request->query($key);
        if ($value === null || $value === '') {
            return null;
        }
        abort_unless(is_string($value) && strlen($value) <= 180 && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $value), 404);

        return $value;
    }

    private function cards(City $city): Builder
    {
        return $this->listings->inCity($city)->with(['city', 'area', 'primaryCategory', 'categories', 'media']);
    }

    /** @return array{Collection<int, Category>, Collection<int, Area>} */
    private function navigation(City $city): array
    {
        $ids = $this->listings->availableIds($city);
        $count = fn (Builder $query): Builder => $query->whereIn('listings.id', $ids);

        return [$this->public->categories()->with('media')->withCount(['listings as public_count' => $count])->orderBy('sort_order')->orderBy('id')->get(),
            $this->public->areas($city)->with('media')->withCount(['listings as public_count' => $count])->orderBy('name')->orderBy('id')->get()];
    }
}
