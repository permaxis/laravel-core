<?php

namespace Permaxis\Laravel\Core\App\Services\Blade;

/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 02/01/2020
 * Time: 18:09
 */
class BladeMacroServiceProvider
{
    const MACRO_REGEX = '/[\'"](\w+)[\'"],?(.*)?/';

    public static function boot()
    {
        \Blade::directive('macro', function ($expression) {
            if (preg_match(self::MACRO_REGEX, $expression, $matches)) {
                if (!empty($matches[2]))
                {
                    return "<?php \Html::macro('$matches[1]', function ($matches[2]) use (\$__env) { ob_start(); ?>\n";

                }
                else
                {
                    return "<?php \Html::macro('$matches[1]', function () use (\$__env) { ob_start(); ?>\n";
                }
            }
        });

        \Blade::directive('endmacro', function ($expression) {
            return "\n<?php return ob_get_clean(); }) ?".">\n";
        });
    }

}