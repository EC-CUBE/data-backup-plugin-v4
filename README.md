# 商品レビュー EC-CUBE 4系

[![Build Status](https://travis-ci.org/EC-CUBE/ProductReview-plugin.svg?branch=feature%2F1.0.0)](https://travis-ci.org/EC-CUBE/ProductReview-plugin)
[![Build status](https://ci.appveyor.com/api/projects/status/oni9ptnqfs37uqdb?svg=true)](https://ci.appveyor.com/project/ECCUBE/ProductReview-plugin-9n48w)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5c61b4f6-edad-4908-9a9a-6b4f38574a93/mini.png)](https://insight.sensiolabs.com/projects/5c61b4f6-edad-4908-9a9a-6b4f38574a93)
[![Coverage Status](https://coveralls.io/repos/github/EC-CUBE/ProductReview-plugin/badge.svg)](https://coveralls.io/github/EC-CUBE/ProductReview-plugin)

## 機能概要
- EC-CUBE4.0/4.1のバックアップを行い4.2系への移行用のデータとするためのプラグイン

## インストール方法

EC-CUBE4系でのプラグインのインストール方法については、EC-CUBE4系開発ドキュメントのプラグインのインストールの項を参考にしてください。

## カスタマイズの詳細

### プラグイン全般

- 接続しているDB内のテーブル単位でcsv出力した結果をtar.gz圧縮したファイルをダウンロード
- /var/backupディレクトリを作成し、csv/圧縮ファイルを作成（バックアップ実行毎にディレクトリ・ファイルを作成します.(処理後削除はされない為、必要に応じて削除してください.)）

### 管理画面

- 設定＞システム管理＞データバックアップメニューの追加
- [バックアップ実行]ボタンクリックでバックアップを実行
