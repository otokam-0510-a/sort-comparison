<?php

declare(strict_types=1);

namespace Sorting;

/**
 * マージソート。
 * 	①配列を半分に分ける
 * 	②それぞれを再帰的に整列してから、整列済みの2つをマージして1つにする。
 */
final class MergeSort
{
	/**
	 * 配列を、昇順に並べ替えて返す。
	 * 
	 * @param array $items ソート対象の配列
	 * @param ?callable $compare 比較関数
	 * @return array
	 */
	public static function sort(array $items, ?callable $compare = null): array
	{
		$result = $items;

		$compare ??= [Compare::class, 'natural'];

		$cnt = count($items);
		if (1 < $cnt) {

			// 配列を２分割
			$mid = intval($cnt / 2);
			$left = array_slice($items, 0, $mid);
			$right = array_slice($items, $mid);

			$result = self::merge(self::sort($left, $compare), self::sort($right, $compare), $compare);
		}

		return $result;
	}

	/**
	 * マージ処理。
	 *
	 * @param array $left  左側の配列
	 * @param array $right 右側の配列
	 * @param callable $compare 比較関数
	 * @return array
	 */
	private static function merge(array $left, array $right, callable $compare): array
	{
		$result = [];
		$i = 0;
		$j = 0;
		$leftCount = count($left);
		$rightCount = count($right);

		// 左右の現在位置の要素を比較し、先に来る方を入れる
		while ($i < $leftCount && $j < $rightCount) {
			if (0 >= $compare($left[$i], $right[$j])) {
				$result[] = $left[$i];
				$i++;
			} else {
				$result[] = $right[$j];
				$j++;
			}
		}

		// 残った要素を、そのまま後ろに入れる
		while ($i < $leftCount) {
			$result[] = $left[$i];
			$i++;
		}
		while ($j < $rightCount) {
			$result[] = $right[$j];
			$j++;
		}

		return $result;
	}
}
