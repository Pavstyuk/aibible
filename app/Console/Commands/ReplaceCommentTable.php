<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:replace-comment-table')]
#[Description('Command description')]

class ReplaceCommentTable extends Command
{

    public array $translations = ['rbo', 'rst', 'nasb'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $comments_all = DB::table('ai_comments')
            ->get()->map(function ($row) {
                return (array) $row;
            })->toArray();

        $translations = $this->translations;
        $results = [];
        foreach ($translations as $translation) {

            $comments = array_values(array_filter($comments_all, fn($a) => $a['translation'] === $translation));

            $db_name = 'ai_comments_' . $translation;

            $result = DB::table($db_name)->insert($comments);

            $results[$translation] = $result;
        }

        print_r($results);
    }
}
