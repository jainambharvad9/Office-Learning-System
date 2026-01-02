<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class ProcessVideoDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:process-durations {--limit=10 : Number of videos to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process video durations for videos that don\'t have duration set';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $videos = Video::where('duration', 0)->limit($limit)->get();

        if ($videos->isEmpty()) {
            $this->info('No videos found that need duration processing.');
            return;
        }

        $this->info("Processing {$videos->count()} videos...");

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        foreach ($videos as $video) {
            try {
                $duration = $this->getVideoDuration($video);
                if ($duration > 0) {
                    $video->update(['duration' => $duration]);
                    $this->info("Updated video {$video->id} with duration {$duration}s");
                }
            } catch (\Exception $e) {
                $this->error("Failed to process video {$video->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Duration processing completed!');
    }

    private function getVideoDuration($video)
    {
        $filePath = storage_path('app/public/' . $video->video_path);

        if (!file_exists($filePath)) {
            throw new \Exception('Video file not found');
        }

        // Try getID3 first
        if (class_exists('getID3')) {
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($filePath);
            if (isset($fileInfo['playtime_seconds'])) {
                return (int) $fileInfo['playtime_seconds'];
            }
        }

        // Fallback to FFmpeg
        if (class_exists('FFMpeg\FFMpeg')) {
            try {
                $ffmpeg = \FFMpeg\FFMpeg::create();
                $videoObj = $ffmpeg->open($filePath);
                $duration = $videoObj->getFormat()->get('duration');
                return (int) $duration;
            } catch (\Exception $e) {
                // FFmpeg failed
            }
        }

        return 0;
    }
}
