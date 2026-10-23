<?php

namespace App\Filament\Resources\Listings;

use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Filament\Resources\Listings\Pages\ListListings;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $slug = 'fichas';

    protected static ?string $navigationLabel = 'Fichas';

    protected static ?string $modelLabel = 'ficha';

    protected static ?string $pluralModelLabel = 'Fichas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identidad y clasificación')->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(255),
                TextInput::make('slug')->label('Slug')->required()->maxLength(180)->helperText('Único en todas las fichas. Después de publicar, cada URL anterior queda reservada y redirige mientras la ficha sea pública.'),
                Select::make('type')->label('Tipo')->options(['business' => 'Negocio', 'place' => 'Lugar público'])->default('business')->required()->live(),
                Select::make('city_id')->label('Ciudad')->options(fn (): array => City::query()->pluck('name', 'id')->all())->required()->live(),
                Select::make('area_id')->label('Zona')->options(fn (Get $get): array => Area::query()->where('city_id', $get('city_id'))->where('is_active', true)->pluck('name', 'id')->all()),
                Select::make('primary_category_id')->label('Categoría principal')->options(fn (Get $get): array => Category::query()->where('listing_type', $get('type') ?? 'business')->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->all())->required(),
                Select::make('category_ids')->label('Categorías asociadas')->multiple()->options(fn (Get $get): array => Category::query()->where('listing_type', $get('type') ?? 'business')->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->all())->required()->helperText('Incluye la categoría principal.'),
                Select::make('operating_status')->label('Estado operativo')->options(['open' => 'Abierto', 'temporarily_closed' => 'Cerrado temporalmente', 'permanently_closed' => 'Cerrado permanentemente'])->default('open')->required(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Contenido de la ficha')->schema([
                Textarea::make('summary')->label('Resumen')->maxLength(1000), Textarea::make('description')->label('Descripción')->rows(6)->maxLength(30000),
                Textarea::make('reasons')->label('Razones para visitar o contratar')->maxLength(5000), Textarea::make('address')->label('Dirección o referencia')->maxLength(3000),
                Textarea::make('opening_hours')->label('Horario')->helperText('Escribe únicamente horarios verificados.')->maxLength(3000),
                Repeater::make('faqs')->label('Preguntas frecuentes')->schema([TextInput::make('question')->label('Pregunta')->required()->maxLength(500), Textarea::make('answer')->label('Respuesta')->required()->maxLength(3000)])->maxItems(30)->defaultItems(0)->addActionLabel('Añadir pregunta'),
            ])->columnSpanFull(),
            Section::make('Contactos y Google Maps')->schema([
                TextInput::make('maps_url')->label('Enlace de Maps')->url()->maxLength(2048), TextInput::make('phone')->label('Teléfono')->maxLength(30),
                TextInput::make('whatsapp')->label('WhatsApp')->helperText('Código de país y número, solo dígitos.')->maxLength(15),
                TextInput::make('website_url')->label('Sitio web')->url()->maxLength(2048), TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(2048), TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(2048),
                TextInput::make('google_rating')->label('Calificación de Google')->numeric()->minValue(1)->maxValue(5)->step(0.1),
                TextInput::make('google_review_count')->label('Reseñas de Google')->numeric()->minValue(0), DatePicker::make('google_reviewed_at')->label('Fecha de revisión de Google')->maxDate(now('America/Cancun')),
            ])->columns(2)->columnSpanFull(),
            Section::make('Logo del negocio')->description('Obligatorio para publicar; independiente de portada, galería y PDF. Guarda el logo y luego autorízalo. Despublica antes de sustituirlo.')->schema([
                SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->collection('logo')->disk('listing_media')->visibility('private')->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->rules(['extensions:jpg,jpeg,png,webp'])->maxSize(5120)
                    ->disabled(fn (?Listing $record): bool => $record?->status === 'published')
                    ->afterStateUpdated(fn (Set $set) => $set('logo_approved', false)),
                TextInput::make('logo_alt')->label('Texto alternativo del logo')->maxLength(255),
                DatePicker::make('logo_reviewed_at')->label('Fecha de verificación del logo')->required(fn (Get $get): bool => (bool) $get('logo_approved'))->maxDate(now('America/Cancun')),
                Toggle::make('logo_approved')->label('Logo autorizado')->helperText('Confirmo que tengo permiso para usar este logo.')->live()->default(false),
            ])->columns(2)->columnSpanFull(),
            Section::make('Imágenes y menú o folleto')->description('Publica únicamente material con permiso. Los archivos permanecen privados hasta su aprobación.')->schema([
                TextInput::make('image_alt')->label('Descripción alternativa de las imágenes')->maxLength(255),
                DatePicker::make('media_reviewed_at')->label('Fecha de verificación del material')->required(fn (Get $get): bool => (bool) $get('media_approved'))->maxDate(now('America/Cancun')),
                SpatieMediaLibraryFileUpload::make('cover')->label('Imagen principal')->collection('cover')->disk('listing_media')->visibility('private')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->rules(['extensions:jpg,jpeg,png,webp'])->maxSize(5120)->customProperties(fn (Get $get): array => ['alt' => $get('image_alt')]),
                SpatieMediaLibraryFileUpload::make('gallery')->label('Galería')->collection('gallery')->disk('listing_media')->visibility('private')->multiple()->reorderable()->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->rules(['extensions:jpg,jpeg,png,webp'])->maxSize(5120)->maxFiles(12)->customProperties(fn (Get $get): array => ['alt' => $get('image_alt')]),
                SpatieMediaLibraryFileUpload::make('document')->label('Menú o folleto PDF')->collection('document')->disk('listing_media')->visibility('private')->acceptedFileTypes(['application/pdf'])->rules(['extensions:pdf', fn (): \Closure => function (string $attribute, mixed $file, \Closure $fail): void {
                    if ((new \finfo(FILEINFO_MIME_TYPE))->buffer($file->get()) !== 'application/pdf') {
                        $fail('Selecciona un archivo PDF válido.');
                    }
                }])->maxSize(10240),
                TextInput::make('document_button_label')->label('Texto del botón del PDF')->default('Descargar menú o folleto')->required()->maxLength(100)->helperText('Ejemplos: Descargar menú, Ver carta o Descargar folleto.'),
                Toggle::make('media_approved')->label('Medios autorizados para publicación')->helperText('Confirmo que tengo permiso para usar estos archivos.')->live()->default(false),
            ])->columnSpanFull(),
            Section::make('Revisión editorial y SEO')->schema([
                TextInput::make('source_url')->label('Fuente pública de verificación')->url()->maxLength(2048), DatePicker::make('reviewed_at')->label('Fecha de revisión')->maxDate(now('America/Cancun')),
                Toggle::make('content_approved')->label('Contenido revisado y aprobado')->default(false),
                TextInput::make('seo_title')->label('Título SEO')->maxLength(160), Textarea::make('seo_description')->label('Descripción SEO')->maxLength(500),
                Textarea::make('private_notes')->label('Notas privadas')->maxLength(10000),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->label('Nombre')->searchable()->wrap(), TextColumn::make('city.name')->label('Ciudad'), TextColumn::make('primaryCategory.name')->label('Categoría')->wrap(),
            TextColumn::make('status')->label('Publicación')->badge()->formatStateUsing(fn (string $state): string => self::statuses()[$state]),
            TextColumn::make('operating_status')->label('Operación')->formatStateUsing(fn (string $state): string => ['open' => 'Abierto', 'temporarily_closed' => 'Cerrado temporalmente', 'permanently_closed' => 'Cerrado permanentemente'][$state]),
        ])->filters([SelectFilter::make('status')->label('Publicación')->options(self::statuses())])->recordActions([EditAction::make()])->toolbarActions([])->emptyStateHeading('Todavía no hay fichas')->emptyStateDescription('Crea un borrador para preparar un negocio.');
    }

    public static function statuses(): array
    {
        return ['draft' => 'Borrador', 'published' => 'Publicada', 'paused' => 'Pausada', 'archived' => 'Archivada'];
    }

    public static function getPages(): array
    {
        return ['index' => ListListings::route('/'), 'create' => CreateListing::route('/create'), 'edit' => EditListing::route('/{record}/edit')];
    }
}
