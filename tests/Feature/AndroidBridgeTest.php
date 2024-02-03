<?php

namespace Tests\Feature;

use App\Services\AndroidBridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AndroidBridgeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_list_of_devices(): void
    {
        $response = AndroidBridge::make()->listOfDevices();

        $this->assertIsArray($response);

        dd($response);
    }
}
