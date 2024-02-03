<?php

declare(strict_types=1);

namespace App\Services;

class AndroidBridge
{
    protected $adbPath = '/Applications/Unity/Hub/Editor/2022.3.14f1/PlaybackEngines/AndroidPlayer/SDK/platform-tools/';

    public static function make()
    {
        return new static();
    }

    public function listOfDevices(): array
    {
        // Execute adb devices command
        $output = shell_exec($this->adbPath . 'adb devices');
        // Split the output by newline to create an array
        $devices = explode("\n", trim($output));

        // each list of array contains "1c7b659936037ece\tdevice" \tdevice, it should removed
        $devices = array_map(function ($device) {
            return explode("\tdevice", $device)[0];
        }, $devices);

        // Remove the first line (it's just a header)
        array_shift($devices);

        // Now $devices is an array of connected devices
        return $devices;
    }

    public function connectDevice(string $ip, string $port): bool
    {
        // Execute adb connect command
        shell_exec($this->adbPath . "adb tcpip 5555");
        $output = shell_exec($this->adbPath . "adb connect $ip:$port");

        // if th eoutput is "connected to" then return true
        $isConnected = str_contains($output, 'connected to');

        if (!$isConnected) {
            throw new \Exception('Failed to connect to the device');
        }

        return $isConnected;
    }
}
