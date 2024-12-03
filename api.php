<?php

/**
 * 消費税込みの金額を取得するAPI
 *
 * 早田 夏月
 */

header('Content-Type: application/json; charset=utf-8', true, 200);

// HTTPリクエストチェック
checkHttpRequest();

// パラメータ取得・チェック
$params = getAndCheckParams();

// レスポンス設定
$response = [
    'status' => 200,
    'data' => getResponseData($params),
];
print json_encode($response, JSON_PRETTY_PRINT);
exit();

/**
 * Httpリクエストチェック
 */
function checkHttpRequest(): void
{
    // HTTPリクエストがGETでない場合は403エラー
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        header('Content-Type: application/json; charset=utf-8', true, 403);
        $response = [
            'status' => 403,
            'errors' => '403. Forbidden.',
        ];

        print json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
}

/**
 * パラメータ取得・チェック
 */
function getAndCheckParams(): array
{
    // 許可されているパラメータのみ取得
    $params = [
        'value' => $_GET['value'] ?? null,
        'is_keigen_zeiritsu' => $_GET['is_keigen_zeiritsu'] ?? null,
    ];

    // パラメータのエラーチェック
    if (is_null($params['value'])) {
        $errors[] = '「value」パラメータを指定してください';
    } elseif ($params['value'] == '') {
        $errors[] = '「value」パラメータの値を指定してください';
    } elseif (!preg_match('/^[0-9]+$/', $params['value'])) {
        $errors[] = '「value」パラメータは半角数字のみで指定してください';
    } elseif (mb_strlen($params['value']) > 10) {
        $errors[] = '「value」パラメータは10桁以下で指定してください';
    }

    if (isset($params['is_keigen_zeiritsu'])) {
        if ($params['is_keigen_zeiritsu'] == '') {
            $errors[] = '「is_keigen_zeiritsu」パラメータの値を指定してください';
        } elseif ($params['is_keigen_zeiritsu'] != 'true' && $params['is_keigen_zeiritsu'] != 'false') {
            $errors[] = '「is_keigen_zeiritsu」パラメータの値が不正です';
        }
    }

    // エラーがある場合は返却
    if (!empty($errors)) {
        $response = [
            'status' => 400,
            'errors' => $errors,
        ];

        print json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    // 軽減税率フラグ設定
    $params['is_keigen_zeiritsu'] = $params['is_keigen_zeiritsu'] == 'true';

    return $params;
}

/**
 * レスポンスデータ取得
 */
function getResponseData(array $params): array
{
    // 税率
    $taxRate = 0.1;  // 10%
    if ($params['is_keigen_zeiritsu']) {
        $taxRate = 0.08;  // 8%
    }

    // 消費税計算（端数処理：切り捨て）
    $tax = floor($params['value'] * $taxRate);

    return [
        'tax' => $tax,                               // 消費税
        'tax_exclusive' => $params['value'],         // 税抜き金額
        'tax_inclusive' => $params['value'] + $tax,  // 税込み金額
    ];
}
