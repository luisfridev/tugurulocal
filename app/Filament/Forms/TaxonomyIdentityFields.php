<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class TaxonomyIdentityFields
{
    public static function make(): Section
    {
        $fields = [
            ColorPicker::make('primary_color')->label('Color primario')->hex()->rules(['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'])->helperText('HEX de seis dígitos. Vacío usa el color de marca; el texto se ajusta por contraste.'),
            TextInput::make('tagline')->label('Tagline')->maxLength(255),
            RichEditor::make('description')->label('Descripción larga')->toolbarButtons([
                ['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo'],
            ])->fileAttachments(false)->maxLength(30000)->helperText('Párrafos, encabezados, listas, citas y enlaces. Imágenes solo en los campos de medios.')->columnSpanFull(),
        ];
        foreach (['og_image' => 'Imagen principal (OG)', 'icon' => 'Icono'] as $collection => $label) {
            $fields[] = Section::make($label)->schema([
                SpatieMediaLibraryFileUpload::make($collection)->label($label)->collection($collection)->disk('listing_media')->visibility('private')
                    ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->rules(['extensions:jpg,jpeg,png,webp'])->maxSize(5120),
                TextInput::make($collection.'_alt')->label('Texto alternativo')->maxLength(255),
                DatePicker::make($collection.'_reviewed_at')->label('Fecha de verificación')->required(fn (Get $get): bool => (bool) $get($collection.'_approved'))->maxDate(now('America/Cancun')),
                Toggle::make($collection.'_approved')->label('Medio autorizado')->helperText('Confirmo que tengo permiso para usar este archivo.')->live()->default(false),
            ])->columns(2)->columnSpanFull();
        }

        return Section::make('Identidad y contenido')->description('Campos opcionales. Nombre y emojis se conservan; el slug no admite emojis. La identidad es opcional y conserva la marca global.')
            ->schema($fields)->columns(2)->columnSpanFull();
    }
}
