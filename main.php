<?php

require_once 'scripts.php';

function main($argv)
{
    try {
        if (count($argv) > 1) {
            $separator = $argv[1];
            $task = $argv[2] ?? null;

            if (!$task) {
                echo "Please entre the task after separator! \n";
            } else {
                $separator = match ($separator) {
                    'comma' => ',',
                    default => ';',
                };

                switch ($task) {
                    case 'countAverageLineCount':
                        echo countAverageLineCount($separator);
                        echo "\n";
                        break;
                    case 'replaceDates':
                        echo replaceDates($separator);
                        break;
                    default:
                        echo "Unknown task (( \n";
                }
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
}

main($argv);