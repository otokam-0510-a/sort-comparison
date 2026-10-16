<?php

declare(strict_types=1);

namespace Sorting\Tests;

use PHPUnit\Framework\TestCase;
use Sorting\MergeSort;

/**
 * マージソートの安定性のテスト。
 * 比較して「等しい」要素どうしの元の並び順が保たれかをテストする。
 */
final class MergeSortStabilityTest extends TestCase
{
	/**
	 * キーが同じ要素が、元の順番のまま並ぶこと
	 */
	public function testEqualKeysKeepOriginalOrder(): void
	{
		$input = [
			['key' => 2, 'id' => 'a'],
			['key' => 1, 'id' => 'b'],
			['key' => 2, 'id' => 'c'],
			['key' => 1, 'id' => 'd'],
			['key' => 2, 'id' => 'e'],
		];

		$result = MergeSort::sort($input, fn (array $x, array $y): int => $x['key'] <=> $y['key']);

		$this->assertSame(['b', 'd', 'a', 'c', 'e'], array_column($result, 'id'));
	}
}
