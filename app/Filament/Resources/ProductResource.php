<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Device;
use App\Models\Product;
use App\Services\SafAutomation;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        TextInput::make('shopee_link')
                            ->label('Shopee Link')
                            ->required(),
                        TextInput::make('video_link')
                            ->label('Video Link')
                            ->required(),
                        Textarea::make('description')
                            ->rows(10)
                            ->label('Description')
                            ->required(),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shopee_link')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Posting')
                    ->requiresConfirmation()
                    ->form(fn(Form $form) => $form->schema([
                        Select::make('device_id')
                            ->label('Device')
                            ->options(\App\Models\Device::pluck('name', 'id')->toArray())
                            ->required(),
                    ]))
                    ->action(function ($record, $data) {
                        $device = Device::find($data['device_id']);
                        SafAutomation::make()->postProduct($record, $device);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
