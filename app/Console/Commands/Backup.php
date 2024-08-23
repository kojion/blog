<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class Backup extends Command
{
    /**
     * バックアップファイル保持数.
     *
     * @var int
     */
    private const COUNT = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup files';

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
        $this->info('バックアップ開始.');

        // ZIP ファイルオープン
        $zip = new ZipArchive();
        $path = storage_path('app/backup/' . date('Ymd-Hi') . '.zip');
        $this->comment("ファイルパス: $path");
        if (($res = $zip->open($path, ZipArchive::CREATE)) !== true) {
            $this->error("zip ファイル作成失敗. エラーコード: ". (string) $res);
            return;
        }

        // DB ファイル追加
        $databasePath = database_path('database.sqlite');
        $zip->addFile($databasePath, basename($databasePath));

        // 画像ファイル追加
        foreach (['image', 'thumbnail'] as $type) {
            $zip->addEmptyDir($type);
            foreach (glob(storage_path("app/public/$type") . '/*') as $file) {
                $zip->addFile($file, "$type/" . basename($file));
            }
        }

        // ZIP ファイル保存し 666 に変更
        $this->comment('zip ファイルを close しています (時間がかかります).');
        $zip->close();
        chmod($path, 0666);
        $this->info("$path に保存.");

        // バックアップファイル規定数以上は削除 (glob 関数は標準でソートされる)
        $backupFiles = glob(storage_path('app/backup') . '/*');
        $backupFileCount = count($backupFiles);
        foreach ($backupFiles as $index => $file) {
            if ($index + self::COUNT < $backupFileCount) {
                unlink($file);
                $this->comment("$file を削除.");
            }
        }
        $this->info("バックアップ終了.");
    }
}
