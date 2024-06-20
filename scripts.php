<?php

/**
 * Read data from people.csv file
 *
 * @param string $separator
 * @return array
 * @throws Exception
 */
function getPeopleFileContent(string $separator): array
{
    $result = [];
    if (($handle = fopen("people.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
            if (count($data) > 1) {
                $result[] = $data;
            } else {
                throw new Exception("Wrong separator");
            }
        }

        fclose($handle);
    }

    if (!count($result)) {
        throw new Exception("people.csv file has no content");
    }

    return $result;
}

/**
 * Get lines from users file, split by users in array
 *
 * @param array $fileContent
 * @return array ['user_id' => ['name' => username, 'files' => ['file name' => [lines from file]]]]
 */
function getLinesFromUsersFiles(array $fileContent): array
{
    $userTexts = [];
    foreach ($fileContent as $userInfo) {
        $id = $userInfo[0];
        $name = $userInfo[1];
        $userTexts[$id]["name"] = $name;
        $files = scandir('./texts');
        foreach ($files as $file) {
            if (strripos(strval($file), "$id-") !== false) {
                $lines = file('./texts/' . $file);
                $userTexts[$id]["files"][$file] = $lines;
            }
        }
    }

    return $userTexts;
}

/**
 * @param string $separator
 * @return array
 * @throws Exception
 */
function getUserTexts(string $separator): array
{
    $fileContent = getPeopleFileContent($separator);
    return getLinesFromUsersFiles($fileContent);
}

/**
 * @param string $separator
 * @return string
 * @throws Exception
 */
function countAverageLineCount(string $separator): string
{
    $userTexts = getUserTexts($separator);

    $result = [];
    foreach ($userTexts as $id => $userText) {
        if (isset($userText['files'])) {
            foreach ($userText['files'] as $file => $lines) {
                $userText['linesCount'][] = count($lines);
            }
        }
        $result[$id] = sprintf(
            "For user %s average amount of rows is %.1f",
            $userText['name'],
            (array_sum($userText['linesCount']) / count($userText['linesCount']))
        );
    }

    return implode("\n", $result);
}

/**
 * @param string $separator
 * @return string
 * @throws Exception
 */
function replaceDates(string $separator): string
{
    $userTexts = getUserTexts($separator);

    $result = [];
    $pattern = '/\d{2}\/\d{2}\/\d{2}/';
    if (!is_dir('./output_texts')) {
        mkdir('./output_texts');
    }
    foreach ($userTexts as $id => $userText) {
        if (isset($userText['files'])) {
            $replacesCounter = 0;
            foreach ($userText['files'] as $file => $lines) {
                $resultLines = preg_replace_callback(
                    $pattern,
                    function (array $matches) {
                        return date('m-d-Y', strtotime($matches[0]));
                    },
                    $lines,
                    -1,
                    $count
                );
                $replacesCounter += $count;
                file_put_contents("./output_texts/$file", $resultLines);
            }

            $result[$id] = sprintf(
                "For user %s made %d replaces",
                $userText['name'],
                $replacesCounter
            );
        }
    }

    return implode("\n", $result) . "\n";
}