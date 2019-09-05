<?php

namespace Permaxis\Core\App\Services\Utils;

use Monolog\Formatter\JsonFormatter;

/**
 * A variation of the Monolog JsonFormatter which pretty-prints the JSON output.
 */
class UtilsJsonPrettyPrintFormatter extends JsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        return json_encode($record, JSON_PRETTY_PRINT) . PHP_EOL;

    }

}