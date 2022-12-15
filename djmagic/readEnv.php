<?php

class ReadEnv
{
    /**
     * Reads the gitignored env file places in the project directory
     * @param string $header the header to search for
     * @return string|bool
     * Returns **string** value based on the header key, **false** if
     * the header does not exist
     */
    public function read(string $header): string|bool
    {
        // Open the env file in read mode
        $file = fopen("../.env", "r");

        // If file was opened successfully
        if ($file) {
            // While file is not at eof
            while (($line = fgets($file)) !== false) {
                // If the current line contains the header return the value
                if (str_contains($line, $header))
                    return substr($line, strlen($header) + 1);
            }
        }
        // If header isn't found
        return false;
    }
}