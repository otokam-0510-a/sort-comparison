<?php

declare(strict_types=1);

namespace Sorting;

/**
 * クイックソート。
 * 	①基準点を選ぶ（要素の先頭・真ん中・末尾の3つの値の中央値）
 * 	②左から「基準点以上」を、右から「基準点以下」を探し、見つかった2つを入れ替える。左右の探索位置がすれ違うまで繰り返す
 * 	③探索位置がすれ違う位置で配列を分けそれぞれを再帰的に整列する
 */
final class QuickSort
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
		$compare ??= [Compare::class, 'natural'];

		$cnt = count($items);
		if (1 < $cnt) {
			self::sortRange($items, 0, $cnt - 1, $compare);
		}

		return $items;
	}

	/**
	 * 配列を、昇順に並べ替えて返す。
	 *
	 * @param array $items ソート対象の配列
	 * @param int $lo 範囲の先頭
	 * @param int $hi 範囲の末尾
	 * @param callable $compare 比較関数
	 */
	private static function sortRange(array &$items, int $lo, int $hi, callable $compare): void
	{
		while ($lo < $hi) {
			// 先頭・真ん中・末尾の3つの値の中央値を基準点とする
			$pivot = self::medianOfThree($items[$lo], $items[intdiv($lo + $hi, 2)], $items[$hi], $compare);

			$i = $lo; // 左からの探索位置
			$j = $hi; // 右からの探索位置
			while ($i <= $j) {
				// 左から基準点以上の要素が見つかるまで探索
				while (0 > $compare($items[$i], $pivot)) {
					$i++;
				}
				// 右から基準点以下の要素が見つかるまで探索
				while (0 < $compare($items[$j], $pivot)) {
					$j--;
				}
				// 見つかった2つを入れ替えて探索を続ける
				if ($i <= $j) {
					[$items[$i], $items[$j]] = [$items[$j], $items[$i]];
					$i++;
					$j--;
				}
			}

			// 小さいほうを再帰し、大きいほうは、次のループで処理する
			if ($j - $lo < $hi - $i) {
				self::sortRange($items, $lo, $j, $compare);
				$lo = $i;
			} else {
				self::sortRange($items, $i, $hi, $compare);
				$hi = $j;
			}
		}
	}

	/**
	 * 3つの値のうち、大きさが真ん中のものを返す。
	 *
	 * @param mixed $a 先頭の値
	 * @param mixed $b 真ん中の値
	 * @param mixed $c 末尾の値
	 * @param callable(mixed, mixed): int $compare 比較関数
	 */
	private static function medianOfThree(mixed $a, mixed $b, mixed $c, callable $compare): mixed
	{
		if (0 > $compare($a, $b)) {
			// a < b
			if (0 > $compare($b, $c)) {
				// a < b < c
				return $b;
			}
			// c <= b: a と c の大きいほうが中央
			return (0 > $compare($a, $c)) ? $c : $a;
		}

		// b <= a
		if (0 > $compare($a, $c)) {
			// b <= a < c
			return $a;
		}
		// c <= a: b と c の大きいほうが中央
		return (0 > $compare($b, $c)) ? $c : $b;
	}
}
