<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Google Drive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Google Drive 同期開始.');

        // Google Drive インスタンス作成
        $client = new Client;
        $client->setAuthConfig([
            'type' => 'service_account',
            'private_key' => config('services.google.private_key'),
            'client_email' => config('services.google.client_email'),
            'client_id' => config('services.google.client_id'),
        ]);
        $client->setScopes([Drive::DRIVE]);
        $drive = new Drive($client);

        // ファイル一覧を取得
        $driveId = config('services.google.drive_id');
        $imageId = '';
        $thumbnailId = '';
        foreach ($drive->files->listFiles(['q' => "'{$driveId}' in parents"]) as $file) {
            // データベースファイルを削除. ディレクトリの Google ID を覚えておく
            if ($file->name === 'database.sqlite') {
                $drive->files->delete($file->id);
            } elseif ($file->name === 'image') {
                $imageId = $file->id;
            } elseif ($file->name === 'thumbnail') {
                $thumbnailId = $file->id;
            }
        }

        // データベースファイルアップロード
        $databasePath = database_path('database.sqlite');
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => basename($databasePath),
            'parents' => [$driveId],
        ]);
        $drive->files->create($fileMetadata, [
            'data' => file_get_contents($databasePath),
            'mimeType' => 'application/vnd.sqlite3',
            'uploadType' => 'media',
            'fields' => 'id',
        ]);
        $this->info('データベースファイルアップロード完了.');

        foreach (['image', 'thumbnail'] as $type) {
            $parentId = $type === 'image' ? $imageId : $thumbnailId;

            // Google Drive 上の image, thumbnail ファイルを一旦全削除
            foreach ($drive->files->listFiles(['q' => "'$parentId' in parents"]) as $file) {
                $drive->files->delete($file->id);
            }

            // image, thumbnail ファイルをアップロード
            foreach (glob(storage_path("app/public/$type") . '/*') as $file) {
                $fileMetadata = new Google_Service_Drive_DriveFile([
                    'name' => basename($file),
                    'parents' => [$parentId],
                ]);
                $drive->files->create($fileMetadata, [
                    'data' => file_get_contents($file),
                    'mimeType' => 'image/jpeg',
                    'uploadType' => 'media',
                    'fields' => 'id',
                ]);
                $this->info(basename($file) . ' アップロード完了.');
            }
        }

        $this->info("Google Drive 同期終了.");
    }
}
