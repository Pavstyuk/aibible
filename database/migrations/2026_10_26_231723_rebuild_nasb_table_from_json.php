<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'nasb';

    private const JSON_PATH = 'assets/json/nasb_bible.json';

    private const CHUNK_SIZE = 1000;

    /**
     * Rebuild the NASB table from the canonical JSON file.
     *
     * Existing IDs are mapped to their new canonical IDs before the table is
     * rebuilt, so references from comments, notes and favorites stay valid.
     */
    public function up(): void
    {
        $rows = $this->readRowsFromJson();
        $oldRefs = $this->oldRefs();
        $idMap = $this->buildIdMap($oldRefs, $rows);

        DB::transaction(function () use ($rows, $idMap): void {
            $this->remapVerseIds('ai_comments_nasb', $idMap);
            $this->remapVerseIds('ai_comments', $idMap, 'translation', 'nasb');
            $this->remapVerseIds('notes', $idMap, 'translation_name', 'nasb');
            $this->remapFavorites($idMap);

            DB::table(self::TABLE)->delete();

            foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
                DB::table(self::TABLE)->insert($chunk);
            }

            $expected = count($rows);
            $actual = DB::table(self::TABLE)->count();

            if ($actual !== $expected) {
                throw new RuntimeException(
                    "NASB rebuild failed: expected {$expected} rows, got {$actual}."
                );
            }
        });
    }

    /**
     * This is an irreversible data migration. The previous NASB contents are
     * not kept in the repository, so rollback intentionally leaves the table
     * untouched.
     */
    public function down(): void
    {
        //
    }

    /**
     * @return list<array{id:int,book_name:string,book_num:int,chapter_num:int,verse_num:int,verse:string}>
     */
    private function readRowsFromJson(): array
    {
        $path = public_path(self::JSON_PATH);

        if (! is_file($path)) {
            throw new RuntimeException("NASB JSON not found: {$path}");
        }

        $json = file_get_contents($path);

        if ($json === false) {
            throw new RuntimeException("Unable to read NASB JSON: {$path}");
        }

        $books = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($books) || $books === []) {
            throw new RuntimeException("NASB JSON must contain a non-empty book object: {$path}");
        }

        $rows = [];
        $id = 0;
        $bookNum = 0;

        foreach ($books as $bookName => $chapters) {
            $bookNum++;

            if (! is_string($bookName) || $bookName === '' || ! is_array($chapters) || $chapters === []) {
                throw new RuntimeException("Invalid book entry #{$bookNum} in NASB JSON.");
            }

            ksort($chapters, SORT_NUMERIC);

            foreach ($chapters as $chapterKey => $verses) {
                $chapterNum = (int) $chapterKey;

                if ($chapterNum < 1 || ! is_array($verses) || $verses === []) {
                    throw new RuntimeException("Invalid chapter {$chapterKey} in {$bookName}.");
                }

                ksort($verses, SORT_NUMERIC);

                foreach ($verses as $verseKey => $text) {
                    $verseNum = (int) $verseKey;

                    if ($verseNum < 1 || ! is_string($text)) {
                        throw new RuntimeException(
                            "Invalid verse {$bookName} {$chapterKey}:{$verseKey}."
                        );
                    }

                    $rows[] = [
                        'id' => ++$id,
                        'book_name' => $bookName,
                        'book_num' => $bookNum,
                        'chapter_num' => $chapterNum,
                        'verse_num' => $verseNum,
                        'verse' => $text,
                    ];
                }
            }
        }

        if ($rows === []) {
            throw new RuntimeException("NASB JSON contains no verses: {$path}");
        }

        return $rows;
    }

    /**
     * @return array<string,int> Map of "book:chapter:verse" to the current ID.
     */
    private function oldRefs(): array
    {
        $refs = [];

        foreach (DB::table(self::TABLE)->get(['id', 'book_num', 'chapter_num', 'verse_num']) as $row) {
            $key = $this->refKey(
                (int) $row->book_num,
                (int) $row->chapter_num,
                (int) $row->verse_num
            );

            if (isset($refs[$key])) {
                throw new RuntimeException("Duplicate NASB reference in database: {$key}");
            }

            $refs[$key] = (int) $row->id;
        }

        return $refs;
    }

    /**
     * @param  array<string,int>  $oldRefs
     * @param  list<array{id:int,book_name:string,book_num:int,chapter_num:int,verse_num:int,verse:string}>  $rows
     * @return array<int,int> Old ID => new ID.
     */
    private function buildIdMap(array $oldRefs, array $rows): array
    {
        $newRefs = [];

        foreach ($rows as $row) {
            $newRefs[$this->refKey(
                $row['book_num'],
                $row['chapter_num'],
                $row['verse_num']
            )] = $row['id'];
        }

        $idMap = [];
        $missing = [];

        foreach ($oldRefs as $ref => $oldId) {
            if (! isset($newRefs[$ref])) {
                $missing[] = $ref;
                continue;
            }

            $idMap[$oldId] = $newRefs[$ref];
        }

        if ($missing !== []) {
            $preview = implode(', ', array_slice($missing, 0, 10));

            throw new RuntimeException(
                'NASB JSON is missing '.count($missing).
                " existing reference(s), e.g. {$preview}."
            );
        }

        return $idMap;
    }

    private function refKey(int $bookNum, int $chapterNum, int $verseNum): string
    {
        return "{$bookNum}:{$chapterNum}:{$verseNum}";
    }

    /**
     * @param  array<int,int>  $idMap
     */
    private function remapVerseIds(
        string $table,
        array $idMap,
        ?string $translationColumn = null,
        ?string $translationValue = null
    ): void {
        if ($idMap === [] || ! Schema::hasTable($table)) {
            return;
        }

        $query = DB::table($table)->select(['id', 'verse_id']);

        if ($translationColumn !== null && $translationValue !== null) {
            $query->whereRaw(
                'LOWER('.$translationColumn.') = ?',
                [strtolower($translationValue)]
            );
        }

        foreach ($query->get() as $row) {
            $oldId = (int) $row->verse_id;

            if (! isset($idMap[$oldId])) {
                continue;
            }

            DB::table($table)
                ->where('id', $row->id)
                ->update(['verse_id' => $idMap[$oldId]]);
        }
    }

    /**
     * @param  array<int,int>  $idMap
     */
    private function remapFavorites(array $idMap): void
    {
        if ($idMap === [] || ! Schema::hasTable('favorites')) {
            return;
        }

        foreach (DB::table('favorites')->get(['id', 'verse_ids']) as $row) {
            if (! is_string($row->verse_ids) || $row->verse_ids === '') {
                continue;
            }

            $data = json_decode($row->verse_ids, true);

            if (! is_array($data)) {
                continue;
            }

            $changed = false;

            foreach ($data as $translation => $value) {
                if (strtolower((string) $translation) !== 'nasb') {
                    continue;
                }

                $newValue = $this->remapFavoriteValue($value, $idMap);

                if ($newValue !== $value) {
                    $data[$translation] = $newValue;
                    $changed = true;
                }
            }

            if (! $changed) {
                continue;
            }

            DB::table('favorites')->where('id', $row->id)->update([
                'verse_ids' => json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                ),
            ]);
        }
    }

    /**
     * @param  array<int,int>  $idMap
     */
    private function remapFavoriteValue(mixed $value, array $idMap): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->remapFavoriteValue($item, $idMap);
            }

            return $value;
        }

        if (! is_string($value) || preg_match('/^(\d+)(?:-(\d+))?$/', $value, $matches) !== 1) {
            return $value;
        }

        $start = (int) $matches[1];
        $end = isset($matches[2]) ? (int) $matches[2] : $start;

        if (! isset($idMap[$start]) || ! isset($idMap[$end])) {
            return $value;
        }

        $newStart = $idMap[$start];
        $newEnd = $idMap[$end];

        if ($newStart > $newEnd) {
            [$newStart, $newEnd] = [$newEnd, $newStart];
        }

        return $newStart === $newEnd ? (string) $newStart : "{$newStart}-{$newEnd}";
    }
};
