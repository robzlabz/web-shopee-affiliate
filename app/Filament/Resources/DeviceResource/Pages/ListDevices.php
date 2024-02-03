<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use App\Models\Device;
use App\Services\AndroidBridge;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('getListOfDevices')
                ->label('Get List of Devices')
                ->requiresConfirmation()
                ->action(function () {
                    $listDevice = AndroidBridge::make()->listOfDevices();

                    if (is_array($listDevice)) {
                        foreach ($listDevice as $device) {
                            Device::updateOrCreate([
                                'name' => $device,
                                'ip' => '0',
                                'port' => '5555',
                            ]);
                        }
                    } else {
                        Notification::make()
                            ->title('Failed to get list of devices')
                            ->send();
                    }
                }),
        ];
    }
}
