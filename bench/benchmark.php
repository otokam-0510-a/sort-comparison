<?php

declare(strict_types=1);

/**
 * ベンチマーク。
 * クイックソートとマージソートの実行時間を比べる。
 *
 * 実行: composer bench
 * 出力: 測定結果の表。
 * 比較:
 * 	- データ量とデータの並び方を組み合わせて測定
 * 	- 1条件につき数回測って中央値を採用する
 * 	- 参考としてPHP標準のusortも測定
 */
require __DIR__ . '/../vendor/autoload.php';

use Sorting\MergeSort;
use Sorting\QuickSort;

/** データ量 */
const SIZES = [1000, 10000, 100000];

/** 実行回数 */
const RUNS = 5;

/** 乱数のシード */
const SEED = 20261015;

/**
 * 並び方のパターン。
 * 	random			: ランダム
 * 	sorted			: 整列済み
 * 	reversed		: 逆順
 * 	manyDuplicates	: 重複が非常に多い
 */
const PATTERNS = ['random', 'sorted', 'reversed', 'manyDuplicates'];

/**
 * パターンに応じたテスト用データ生成。
 *
 * @return int[]
 */
function makeData(string $pattern, int $n): array
{
	// 測るたびに、データが変わらないようにシード固定
	mt_srand(SEED);

	return match ($pattern) {
		'random' => array_map(static fn () => mt_rand(0, $n * 10), range(1, $n)),
		'sorted' => range(0, $n - 1),
		'reversed' => range($n, 1),
		'manyDuplicates' => array_map(static fn () => mt_rand(0, 9), range(1, $n)),
	};
}

/**
 * 中央値を返す。
 * 測定値の外れ値を除外。
 *
 * @param float[] $values
 */
function median(array $values): float
{
	sort($values);

	return $values[intdiv(count($values), 2)];
}

/**
 * 実行して、かかった時間(ミリ秒)を返す。
 */
function measure(callable $fn): float
{
	$start = hrtime(true);
	$fn();

	return (hrtime(true) - $start) / 1e6;
}

// -------------
// 実行
// -------------

// 昇順の比較関数。3つのアルゴリズムすべてに同じものを渡して、条件をそろえる。
$cmp = static fn (int $a, int $b): int => $a <=> $b;

$algorithms = [
	'quickSort' => static fn (array $xs): array => QuickSort::sort($xs, $cmp),
	'mergeSort' => static fn (array $xs): array => MergeSort::sort($xs, $cmp),
	'usort'		=> static function (array $xs) use ($cmp): array {
		usort($xs, $cmp);
		return $xs;
	},
];

// 表の見出し
echo '| n | pattern | ' . implode(' | ', array_map(fn ($name) => "$name (ms)", array_keys($algorithms))) . " |\n";
echo '|---|---|' . str_repeat('---|', count($algorithms)) . "\n";

foreach (SIZES as $n) {
	foreach (PATTERNS as $pattern) {
		$data = makeData($pattern, $n);
		$cells = [];

		foreach ($algorithms as $sort) {
			// ウォームアップ
			// 1回目は、初回の準備で遅くなりやすいため測定には含めない
			$sort($data);

			$times = [];
			for ($i = 0; $i < RUNS; $i++) {
				$times[] = measure(fn () => $sort($data));
			}
			$cells[] = number_format(median($times), 2);
		}

		echo "| $n | $pattern | " . implode(' | ', $cells) . " |\n";
	}
}
