<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Product;
use App\Services\AndroidBridge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostingProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product, public Device $device)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adbService = AndroidBridge::make();
        $adbService->setDevice($this->device);

        // download video and save
        $videoPath = storage_path('app/' . $this->product->id . '.mp4');
        file_put_contents($videoPath, file_get_contents($this->product->video_link));

        $adbService->copyToGalery($videoPath);

        $adbService->closeApp("com.sec.android.gallery3d");
        $adbService->closeApp("com.shopee.id");

        $adbService->openApp("com.shopee.id", "com.shopee.app.ui.home.HomeActivity_");

        $adbService->clickOn(470, 1240, "Clicking on shopee video tab...", 5);
        $adbService->clickOn(58, 80, "Clicking on profile");
        $adbService->clickOn(700, 1200, "Clicking on posting video");
        $adbService->clickOn(640, 1125, "Clicking on pilih galery");
        $adbService->clickOn(181, 154, "Pilih video");
        $adbService->clickOn(700, 1131, "Lanjutkan");
        $adbService->clickOn(716, 1231, "Lanjutkan Posting");
        $adbService->clickOn(200, 160, "Lanjutkan Posting");

        $text = '#fyp #shopeeid #shopee #tiktok #mainan #populer #keren';
        // max text is 150 character
        $text = substr($text, 0, 150);
        $adbService->writeText($text, "Nulis Caption");

        $adbService->clickOn(300, 885, "Click Biar Hover Ilang", 1);
        $adbService->clickOn(600, 362, "Pilih Produk");
        $adbService->clickOn(500, 185, "Pilih Tab Semua Produk");
        $adbService->clickOn(93, 119, "Click Cari Produk");

        $search = str_replace('star+', '', $this->product->title);
        $adbService->writeText($search, "Nulis Caption");

        $adbService->clickOn(700, 400, "Tambah Produk");
        $adbService->clickOn(400, 1230, "Click Selesai");
        $adbService->clickOn(400, 1230, "Posting");

        // wait for upload to finish
        usleep(20 * 1000000);

        $adbService->closeApp("com.shopee.id");
    }
}
