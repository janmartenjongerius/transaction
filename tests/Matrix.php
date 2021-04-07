<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Traversable;

trait Matrix
{
    /**
     * Create a full matrix for the given axes.
     *
     * E.g.: The following axes create the resulting matrix:
     *
     *   ['foo' => [1, 2, 3], 'bar' => [true, false]]
     *
     * Resulting matrix:
     *
     *   [
     *     ['foo' => 1, 'bar' => true],
     *     ['foo' => 2, 'bar' => true],
     *     ['foo' => 3, 'bar' => true],
     *     ['foo' => 1, 'bar' => false],
     *     ['foo' => 2, 'bar' => false],
     *     ['foo' => 3, 'bar' => false],
     *   ]
     *
     * @param iterable $axes
     *
     * @return array
     */
    public static function createMatrix(iterable $axes): array
    {
        $matrix = [];

        // Process each axis.
        foreach ($axes as $axis => $options) {
            // Allow traversable options.
            if ($options instanceof Traversable) {
                $options = iterator_to_array($options);
            }

            // Explicitly wrap non-array values in an array.
            // Casting values to array may yield different results.
            // null => cast (array)null => []
            // null => wrap [null]      => [null]
            if (!is_array($options)) {
                $options = [$options];
            }

            // Create the initial axis.
            // This also allows for initial axes to be empty.
            if (empty($matrix)) {
                $matrix = array_map(
                    fn ($option) => [$axis => $option],
                    $options
                );

                continue;
            }

            // Expand the matrix with the current axis and its options.
            $matrix = array_reduce(
                $options,
                function (array $carry, $option) use ($axis, $matrix): array {
                    // Multiply by all existing records.
                    foreach ($matrix as $row) {
                        $carry[] = array_merge($row, [$axis => $option]);
                    }

                    return $carry;
                },
                []
            );
        }

        return $matrix;
    }
}
