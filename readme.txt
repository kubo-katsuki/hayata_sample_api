消費税込みの金額を取得するAPI

▼説明
税抜き金額から消費税（10%）と税込み金額を計算します
軽減税率フラグ（is_keigen_zeiritsu）に「true」を指定した場合、軽減税率（8%）で計算します
消費税の端数処理は「切り捨て」です

▼エンドポイント
GET api.php

▼パラメータ
value              | 税抜き金額（必須）
is_keigen_zeiritsu | 軽減税率フラグ（任意）

▼レスポンス
data/tax           | 消費税
data/tax_exclusive | 税抜き金額
data/tax_inclusive | 税込み金額

▼サンプル
1）標準税率で計算
https://ドメイン/api.php?value=1000
----------------------------------
{
  "status": 200,
  "data": {
    "tax": 100,
    "tax_exclusive": "1000",
    "tax_inclusive": 1100
  }
}
----------------------------------

2）軽減税率で計算
https://ドメイン/api.php?value=1000&is_keigen_zeiritsu=true
----------------------------------
{
  "status": 200,
  "data": {
    "tax": 80,
    "tax_exclusive": "1000",
    "tax_inclusive": 1080
  }
}
----------------------------------
