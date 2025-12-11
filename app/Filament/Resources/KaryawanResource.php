<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\Cabang;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $modelLabel = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Karyawan')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('nik_app', $state);
                            }),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('name', $state);
                            }),
                        Forms\Components\TextInput::make('jabatan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('foto')
                            ->image()
                            ->directory('karyawan-photos'),
                        Forms\Components\Select::make('kode_dept')
                            ->label('Departemen')
                            ->options(Department::all()->pluck('nama_dept', 'kode_dept'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('kode_cabang')
                            ->label('Cabang')
                            ->options(Cabang::all()->pluck('nama_cabang', 'kode_cabang'))
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Data User')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true,
                                ignorable: fn ($record) => $record?->user
                            )
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->revealable(),
                        Forms\Components\TextInput::make('nik_app')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('pwd_app')
                            ->label('Password App')
                            ->password()
                            ->maxLength(255)
                            ->revealable(),
                        Forms\Components\TextInput::make('id_chat_telegram')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_users')
                            ->options([
                                1 => 'Admin',
                                2 => 'User Biasa',
                            ])
                            ->default(2)
                            ->required(),
                        Forms\Components\TextInput::make('id_admin_telegram')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('status_users') == 1),
                    ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('foto'),
                Tables\Columns\TextColumn::make('department.nama_dept')
                    ->label('Departemen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kode_dept')
                    ->label('Departemen')
                    ->options(Department::all()->pluck('nama_dept', 'kode_dept'))
                    ->attribute('kode_dept'),
                Tables\Filters\SelectFilter::make('kode_cabang')
                    ->label('Cabang')
                    ->options(Cabang::all()->pluck('nama_cabang', 'kode_cabang'))
                    ->attribute('kode_cabang'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('setJamKerja')
                    ->label('Set Jam Kerja')
                    ->icon('heroicon-o-clock')
                    ->url(fn (Karyawan $record): string => static::getUrl('set-jam-kerja', ['record' => $record]))
                    ->color('success')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawan::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'view' => Pages\ViewKaryawan::route('/{record}'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
            'set-jam-kerja' => Pages\SetJamKerja::route('/{record}/set-jam-kerja'),
        ];
    }
}
