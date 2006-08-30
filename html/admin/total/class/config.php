<?php

/*
	共通 
*/
// TTFフォントファイル
define("FONT_PATH", ROOT_DIR . "/data/fonts/ipag.ttf");
define("FONT_SIZE", 8);			// フォントサイズ
define("TITLE_FONT_SIZE", 10);	// タイトルフォントサイズ
define("BG_WIDTH", 720);		// 背景幅
define("BG_HEIGHT", 400);		// 背景高さ
define("LINE_PAD", 5);			// 行間
define("TEXT_RATE", 0.75);		// フォント補正値(実際の描画幅/フォントサイズ)

/*
	円グラフ
*/
define("PIE_LEFT", 200);			// 円グラフ位置
define("PIE_TOP", 150);				// 円グラフ位置
define("PIE_WIDTH", 230);			// 円グラフ幅
define("PIE_HEIGHT", 100);			// 円グラフ高さ
define("PIE_THICK", 30);			// 円グラフ太さ
define("PIE_LABEL_UP", 20);			// 円グラフのラベル位置を上にあげる
define("PIE_SHADE_IMPACT", 0.1);	// 値が大きいほど影が長くなる

/*
	折れ線グラフ
*/
define("LINE_Y_SCALE", 10);			// Y軸の目盛り数
define("LINE_X_SCALE", 10);			// X軸の目盛り数
define("LINE_LEFT", 60);			// 線グラフ位置
define("LINE_TOP", 50);				// 線グラフ位置
define("LINE_AREA_WIDTH", 600);		// 線グラフ背景のサイズ
define("LINE_AREA_HEIGHT", 300);	// 線グラフ背景のサイズ
define("LINE_MARK_SIZE", 6);		// 線グラフマークのサイズ
define("LINE_SCALE_SIZE", 6);		// 目盛り幅
define("LINE_XLABEL_MAX", 30);		// X軸のラベルの表示制限数
define("LINE_XTITLE_PAD", 25);		// X軸のタイトルと軸の間隔
define("LINE_YTITLE_PAD", 15);		// Y軸のタイトルと軸の間隔

/* 
	棒グラフ
*/
define("BAR_PAD", 6);				// グラフと目盛りの間隔

/*
	タイトルラベル
*/
define("TITLE_TOP", 10);	// 背景枠との上幅

/*
	凡例
*/
define("LEGEND_TOP", 10); 	// 背景枠との上幅
define("LEGEND_RIGHT", 10); // 背景枠との右幅


/*
	表示色
*/
// 凡例背景
$ARR_LEGENDBG_COLOR = array(245,245,245);
// ラベル背景
$ARR_LABELBG_COLOR = array(255,255,255);
// グラフカラー
$ARR_GRAPH_RGB = array(
	array(200,50,50),
	array(50,50,200),
	array(50,200,50),
	array(255,255,255),
	array(244,200,200),
	array(200,200,255),
	array(50,200,50),
	array(255,255,255),
	array(244,244,244),
);
// 影の色
$ARR_SHADE_COLOR = array(100,100,100);
// 縁の色
$ARR_FLAME_COLOR = array(100, 100, 140);
// 文字色
$ARR_TEXT_COLOR = array(55, 55, 55);
// 背景カラー
$ARR_BG_COLOR = array(255,255,255);	
// タイトル文字色
$ARR_TITLE_COLOR = array(0, 0, 0);
// グリッド線色
$ARR_GRID_COLOR = array(200, 200, 200);
// マークの色
$ARR_MARK_COLOR = array(130, 130, 255);


?>