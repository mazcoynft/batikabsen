<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use App\Models\JamKerja;
use App\Models\Karyawan;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class SetJamKerja extends Page
{
    protected static string $resource = KaryawanResource::class;

    protected static string $view = 'filament.resources.karyawan-resource.pages.set-jam-kerja';
    
    public ?array $data = [];
    
    public Karyawan $record;
    
    public function mount(Karyawan $record): void
    {
        $this->record = $record;
        
        $formData = [];
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        
        foreach ($hari as $day) {
            $jamKerjaHari = DB::table('jam_kerja_karyawan')
                ->where('karyawan_id', $record->id)
                ->where('hari', $day)
                ->first();
            
            $formData[$day] = $jamKerjaHari ? $jamKerjaHari->jam_kerja_id : $record->jam_kerja_id;
        }
        
        $this->form->fill($formData);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Jam Kerja Karyawan')
                    ->columns(2)
                    ->schema([
                        Select::make('senin')
                            ->label('Senin')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('selasa')
                            ->label('Selasa')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('rabu')
                            ->label('Rabu')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('kamis')
                            ->label('Kamis')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('jumat')
                            ->label('Jumat')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('sabtu')
                            ->label('Sabtu')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                        Select::make('minggu')
                            ->label('Minggu')
                            ->options(JamKerja::all()->pluck('nama_jam_kerja', 'id'))
                            ->searchable(),
                    ])
            ])
            ->statePath('data');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Update')
                ->action('save'),
        ];
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        // Simpan jam kerja default untuk kompatibilitas dengan sistem lama
        $this->record->update([
            'jam_kerja_id' => $data['senin'],
        ]);
        
        // Simpan jam kerja per hari
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        
        foreach ($hari as $day) {
            // Hapus data lama jika ada
            DB::table('jam_kerja_karyawan')
                ->where('karyawan_id', $this->record->id)
                ->where('hari', $day)
                ->delete();
            
            // Simpan data baru
            if (!empty($data[$day])) {
                DB::table('jam_kerja_karyawan')->insert([
                    'karyawan_id' => $this->record->id,
                    'jam_kerja_id' => $data[$day],
                    'hari' => $day,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        Notification::make()
            ->title('Jam kerja berhasil diperbarui')
            ->success()
            ->send();
    }
}