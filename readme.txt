=== Logbook ===
Contributors: miyauchi, tarosky
Tags: comments, spam
Requires at least: 4.8
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: nightly
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

これは WordPress のアクティビティをログとして保存するプラグインです。何が変わったか誰がそれをしたかをいつでも確認することができます。

== Description ==

このプラグインは、WordPressの様々なアクティビティログを保存するためのプラグインです。
このプラグインをインストールして有効化すると以下のような様々なアクティビティを監視することができます。

=== 特徴 ===

* 軽量でパフォーマンスへのインパクトがほとんどありません。
* 100通り以上のユニットテストが実行されており、エンタープライズでも安心して利用できます。
* ログを保存するためのイベントは拡張可能であり、みなさん独自のログを保存するためのアドオンを開発することが可能です。
* WordPressでの各種のアクティビティを保存
	* 記事の公開や公開済みの記事のアップデートや削除
	* プラグインやテーマの有効化や無効化
	* WordPress本体やプラグイン、言語ファイルのアップデート
	* ユーザーのログイン
	* XML-RPCによるログインや記事の投稿
	* PHPのエラー（デバッグモードでは、WaringやNoticeも保存）

=== 保存するログの詳細 ===

* WordPress
	* Core updates
	* Plugin/Theme updates
	* Language updates
* Post/Page/Attachment
	* Created
	* Updated
	* Deleted
* Plugin
	* Activated
	* Deactivated
* Theme
	* Switched
* User
	* Logged in
* XML-RPC
	* Authenticated
	* Created
	* Updated
	* Deleted
* PHP
	* Errors
	* Warnings (WP_DEBUG only)
	* Notices (WP_DEBUG only)

== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==


== Changelog ==

= 1.0 =
* The first releasae.
