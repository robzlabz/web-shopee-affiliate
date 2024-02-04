<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Device;

class AndroidBridge
{
    protected $adbPath = '/Applications/Unity/Hub/Editor/2022.3.14f1/PlaybackEngines/AndroidPlayer/SDK/platform-tools/';
    private $deviceName;


    public static function make()
    {
        return new static();
    }

    public function setDevice(Device $device)
    {
        $this->deviceName = $device->name;
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

    public function clickOn($x, $y, $message, $sleep_time = 3)
    {
        echo $message . "\n";
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell input tap $x $y");
        usleep($sleep_time * 1000000);
    }

    public function writeText($text, $message = null, $sleep_time = 3)
    {
        if ($message) {
            echo $message . "\n";
        }
        $text = str_replace(' ', '%s', $text);
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell input text '$text'");
        usleep(1000000);
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell input keyevent 4");  // simulate back button press
        usleep($sleep_time * 1000000);
    }

    public function closeApp($package_name)
    {
        echo "Closing $package_name app...\n";
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell am force-stop $package_name");
        usleep(3 * 1000000);
    }

    public function openApp($package_name, $activity_name)
    {
        echo "Opening $package_name app...\n";
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell am start -n $package_name/$activity_name");
        usleep(5 * 1000000);
    }

    public function copyToGalery($file_name)
    {
        echo "Copying to galery...\n";
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} push $file_name /sdcard/DCIM/Camera");
        usleep(3 * 1000000);
        // Send a media scan intent
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell am broadcast -a android.intent.action.MEDIA_SCANNER_SCAN_FILE -d file:///sdcard/DCIM/Camera/");
        usleep(3 * 1000000);
    }

    public function deleteFile($file_name)
    {
        echo "Deleting file...\n";
        shell_exec("{$this->adbPath}adb -s {$this->deviceName} shell rm /sdcard/DCIM/Camera/$file_name");
        usleep(3 * 1000000);
    }
}
