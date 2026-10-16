<?php

declare(strict_types=1);

namespace Sorting;

/**
 * 比較関数。
 * aが先なら負、同じなら0、後ろなら正を返す。
 */
final class Compare
{
	public static function natural(mixed $a, mixed $b): int
	{
		return $a <=> $b;
	}
}
