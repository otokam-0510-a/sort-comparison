<?php

declare(strict_types=1);

namespace Sorting\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sorting\MergeSort;
use Sorting\QuickSort;

/**
 * ソートテスト。
 * 
 * テストケース:
 * 	- 境界(空、1要素)
 * 	- 並びの違い(重複、整列済み、逆順、全要素同じ、ランダム)
 * 	- 標準関数 sort() との結果の一致
 */
final class SortTest extends TestCase
{
	/**
	 * テスト対象のソート関数の一覧。
	 *
	 * @return array<string, array{callable}>
	 */
	public static function sorters(): array
	{
		return [
			'quickSort' => [[QuickSort::class, 'sort']],
			'mergeSort' => [[MergeSort::class, 'sort']],
		];
	}

	/**
	 * 昇順の比較関数(負 / 0 / 正を返す。usort と同じ)。
	 */
	private static function asc(int $a, int $b): int
	{
		return $a <=> $b;
	}

	/**
	 * 空配列のケース
	 */
	#[DataProvider('sorters')]
	public function testEmptyArray(callable $sort): void
	{
		$this->assertSame([], $sort([], self::asc(...)));
	}

	/**
	 * 1要素のケース
	 */
	#[DataProvider('sorters')]
	public function testSingleElement(callable $sort): void
	{
		$this->assertSame([1], $sort([1], self::asc(...)));
	}

	/**
	 * 重複ありのケース
	 */
	#[DataProvider('sorters')]
	public function testWithDuplicates(callable $sort): void
	{
		$this->assertSame([1, 1, 2, 2, 3, 3], $sort([3, 1, 2, 3, 1, 2], self::asc(...)));
	}

	/**
	 * 整列済みのケース
	 */
	#[DataProvider('sorters')]
	public function testAlreadySorted(callable $sort): void
	{
		$input = range(1, 1000);
		$this->assertSame($input, $sort($input, self::asc(...)));
	}

	/** 逆順のケース */
	#[DataProvider('sorters')]
	public function testReversed(callable $sort): void
	{
		$input = range(1000, 1);
		$this->assertSame(range(1, 1000), $sort($input, self::asc(...)));
	}

	/**
	 * 全要素が同じ値のケース
	 */
	#[DataProvider('sorters')]
	public function testAllSameValues(callable $sort): void
	{
		$input = array_fill(0, 999, 1);
		$this->assertSame($input, $sort($input, self::asc(...)));
	}

	/**
	 * ランダムのケース
	 */
	#[DataProvider('sorters')]
	public function testRandomMatchesBuiltinSort(callable $sort): void
	{
		mt_srand(12345);
		$n = mt_rand(0, 100);
		$input = [];
		for ($i = 0; $i < $n; $i++) {
			$input[] = mt_rand(-100, 100);
		}
		$expected = $input;
		sort($expected);
		$this->assertSame($expected, $sort($input, self::asc(...)));
	}

}
