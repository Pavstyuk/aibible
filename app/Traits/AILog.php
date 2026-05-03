<?php

namespace App\Traits;

trait AILog
{
    public function logToFile(array $data)
    {
        $file_name = date('Y-m-d-h') . '.log';
        $path = base_path();

        $result = file_put_contents(
            $path . '/storage/logs/' . $file_name,
            print_r($data, true),
            FILE_APPEND
        );

        if ($result) {
            return true;
        } else {
            return 'Log error.';
        }
    }
}
