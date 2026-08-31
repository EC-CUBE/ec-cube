<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Pdf;

/**
 * FPDF の上に「カーソルを持つ帳票描画」を載せる薄い書き込み器.
 *
 * 帳票は「現在位置から幅と高さを指定してセルを置き, カーソルが右か下へ進む」という
 * 組み方で作られている. {@see FpdfEngine} はページ内の絶対座標で描く API しか持たないため,
 * その間を埋めるのがこのクラスの役割.
 *
 * 単位は mm, 用紙は A4 縦, 原点は左上（下方向が y の正）で固定する.
 *
 * ## 座標の決め方
 *
 * セルの高さは「指定値」と「フォントサイズ x {@see CELL_HEIGHT_RATIO}」の大きい方.
 * 文字はセル内で上下中央に置く. ベースラインはセル上端から
 * `(セル高 + アセント - ディセント) / 2` の位置になる.
 * 左右は既定で {@see CELL_PADDING_X} だけ内側へ寄せる.
 *
 * これらは 4.3 まで使っていた TCPDF の既定値をそのまま引き継いだもので,
 * 納品書の座標を変えないための値. 変更すると既存の帳票の見た目が動く.
 */
class PdfWriter
{
    /** セル高さ / フォントサイズ の比 */
    public const CELL_HEIGHT_RATIO = 1.25;

    /** セルの左右パディング(mm) */
    public const CELL_PADDING_X = 1.0;

    /** 用紙サイズ(mm)の既定値. A4 縦 */
    public const DEFAULT_PAGE_WIDTH = 210.0;
    public const DEFAULT_PAGE_HEIGHT = 297.0;

    /** 用紙下端から自動改ページ位置までの距離(mm) */
    private const PAGE_BREAK_MARGIN = 20.0;

    /** セルの罫線の向き. {@see borderSides()} が返す配列の要素に使う */
    private const BORDER_TOP = 0;
    private const BORDER_RIGHT = 1;
    private const BORDER_BOTTOM = 2;
    private const BORDER_LEFT = 3;

    private readonly FpdfEngine $engine;

    private float $pageWidth = self::DEFAULT_PAGE_WIDTH;
    private float $pageHeight = self::DEFAULT_PAGE_HEIGHT;

    private float $leftMargin = 10.0;
    private float $topMargin = 10.0;
    private float $rightMargin = 10.0;

    /** フッタの下端からの距離(mm) */
    private float $footerMargin = 10.0;

    private float $x = 0.0;
    private float $y = 0.0;

    /** 直前に描画したセルの高さ(mm) */
    private float $lastCellHeight = 0.0;

    private string $fontFamily = '';
    private string $fontStyle = '';
    private float $fontSizePt = 12.0;

    /** 現在フォントのアセント・ディセント(mm, いずれも正の値) */
    private float $fontAscent = 0.0;
    private float $fontDescent = 0.0;

    private string $fillColor = '#ffffff';
    private string $textColor = '#000000';
    private string $drawColor = '#000000';
    private float $lineWidth = 0.2;

    /** 各ページへ重ねるテンプレート PDF. null なら重ねない */
    private bool $hasTemplate = false;

    /** フッタへ右寄せで出す文字列 */
    private string $footerText = '';

    /**
     * フッタのフォント指定 [フォント名, スタイル, サイズ(pt)]
     *
     * @var array{0: string, 1: string, 2: float}|null
     */
    private ?array $footerFont = null;

    private int $pageCount = 0;

    /** 確定前のページに積んだ塗り・罫線の命令 */
    private string $background = '';

    /** 確定前のページに積んだ文字・画像の命令 */
    private string $foreground = '';

    public function __construct()
    {
        $this->engine = new FpdfEngine();
    }

    public function getPageWidth(): float
    {
        return $this->pageWidth;
    }

    public function getPageHeight(): float
    {
        return $this->pageHeight;
    }

    /**
     * 余白を設定する. 右余白を省略すると左と同じ値になる.
     */
    public function setMargins(float $left, float $top, ?float $right = null): void
    {
        $this->leftMargin = $left;
        $this->topMargin = $top;
        $this->rightMargin = $right ?? $left;
    }

    /**
     * @return array{left: float, top: float, right: float}
     */
    public function getMargins(): array
    {
        return [
            'left' => $this->leftMargin,
            'top' => $this->topMargin,
            'right' => $this->rightMargin,
        ];
    }

    /**
     * 自動改ページ位置(mm). この位置を超える描画は次ページへ送る.
     */
    public function getPageBreakTrigger(): float
    {
        return $this->pageHeight - self::PAGE_BREAK_MARGIN;
    }

    public function setFooterMargin(float $margin): void
    {
        $this->footerMargin = $margin;
    }

    /**
     * フッタの内容を設定する.
     *
     * @param array{0: string, 1: string, 2: float} $font [フォント名, スタイル, サイズ(pt)]
     */
    public function setFooter(string $text, array $font): void
    {
        $this->footerText = $text;
        $this->footerFont = $font;
    }

    /**
     * 以降のページへ重ねるテンプレート PDF を読み込む.
     *
     * 用紙サイズはテンプレートに合わせる（テンプレートは A4 ちょうどとは限らない）.
     */
    public function setTemplateFile(string $path): void
    {
        $size = $this->engine->loadTemplate($path);

        $this->hasTemplate = true;
        $this->pageWidth = $size['width'];
        $this->pageHeight = $size['height'];
    }

    /**
     * ページを追加し, カーソルを左上の余白位置へ戻す.
     */
    public function addPage(bool $withTemplate = true): void
    {
        // 次のページへ移る前に, 今のページへ積んだ分を確定させる
        $this->flushContent();

        $this->engine->beginPage($this->pageWidth, $this->pageHeight);
        ++$this->pageCount;

        if ($withTemplate && $this->hasTemplate) {
            $this->engine->overlayTemplate($this->pageWidth, $this->pageHeight);
        }

        $this->x = $this->leftMargin;
        $this->y = $this->topMargin;

        $this->renderFooter();

        // フッタ描画でフォントが変わっているので, 呼び出し側の指定へ戻す
        if ($this->fontFamily !== '') {
            $this->applyFont($this->fontFamily, $this->fontStyle, $this->fontSizePt);
        }
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    /**
     * フォントを設定する. 空文字は現在値の据え置きを意味する.
     */
    public function setFont(?string $family = null, ?string $style = null, float|int|null $sizePt = null): void
    {
        $family = ($family === null || $family === '') ? $this->fontFamily : $family;
        $style ??= $this->fontStyle;
        $sizePt = ($sizePt === null || (float) $sizePt <= 0.0) ? $this->fontSizePt : (float) $sizePt;

        $this->applyFont($family, $style, $sizePt);
    }

    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    public function getFontStyle(): string
    {
        return $this->fontStyle;
    }

    public function getFontSizePt(): float
    {
        return $this->fontSizePt;
    }

    /**
     * フォントサイズを mm で返す.
     */
    public function getFontSize(): float
    {
        return $this->engine->toUnit($this->fontSizePt);
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }

    /**
     * y 座標を設定する. 既定では x も左余白へ戻す.
     */
    public function setY(float $y, bool $resetX = true): void
    {
        $this->y = $y;
        if ($resetX) {
            $this->x = $this->leftMargin;
        }
    }

    public function setXY(float $x, float $y): void
    {
        $this->y = $y;
        $this->x = $x;
    }

    /**
     * 直前に描画したセルの高さ(mm).
     */
    public function getLastCellHeight(): float
    {
        return $this->lastCellHeight;
    }

    public function setFillColor(int $red, int $green, int $blue): void
    {
        $this->fillColor = $this->toHexColor($red, $green, $blue);
    }

    public function setTextColor(int $red, int $green, int $blue): void
    {
        $this->textColor = $this->toHexColor($red, $green, $blue);
    }

    public function setDrawColor(int $red, int $green, int $blue): void
    {
        $this->drawColor = $this->toHexColor($red, $green, $blue);
    }

    public function setLineWidth(float $width): void
    {
        $this->lineWidth = $width;
    }

    /**
     * 指定座標へ 1 行だけ文字を描く. カーソルは動かさない.
     */
    public function text(float $x, float $y, string $text): void
    {
        if ($text === '') {
            return;
        }

        $height = $this->minCellHeight();
        $this->addContent($this->textOps($text, $x + self::CELL_PADDING_X, $y, $height));
    }

    /**
     * 1 行のセルを描画し, カーソルを進める.
     *
     * @param float $width セル幅. 0 以下なら右余白まで広げる
     * @param float $height セルの最小高さ
     * @param int|string $border 罫線. 1 で四辺, 'T' 'R' 'B' 'L' の並びで各辺
     * @param int $lineBreak 描画後のカーソル移動. 0=右へ / 1=次行の左端へ / 2=真下へ
     * @param string $align 水平位置 'L' 'C' 'R'
     */
    public function cell(
        float $width,
        float $height = 0,
        string $text = '',
        int|string $border = 0,
        int $lineBreak = 0,
        string $align = '',
        bool $fill = false,
    ): void {
        $width = $this->resolveWidth($width);
        $height = max($height, $this->minCellHeight());

        // TCPDF の Cell() と同じく, 収まらなければ描画前に改ページする
        $this->checkPageBreak($height);

        $this->addDecoration($this->cellDecorationOps($this->x, $this->y, $width, $height, $border, $fill));
        if ($text !== '') {
            $this->addContent($this->textOps($text, $this->x, $this->y, $height, $width, $align));
        }

        $this->lastCellHeight = $height;
        $this->advanceCursor($this->x, $this->y, $width, $height, $lineBreak);
    }

    /**
     * 折り返しのあるセルを描画し, カーソルを進める.
     *
     * セルの高さは「指定した最小高さ」と「行数 x 行送り」の大きい方になる.
     *
     * @param float $width セル幅. 0 以下なら右余白まで広げる
     * @param float $height セルの最小高さ
     * @param int|string $border 罫線. {@see cell()} と同じ
     * @param string $align 水平位置 'L' 'C' 'R'
     * @param int $lineBreak 描画後のカーソル移動. 0=右へ / 1=次行の左端へ / 2=真下へ
     */
    public function multiCell(
        float $width,
        float $height,
        string $text,
        int|string $border = 0,
        string $align = 'L',
        bool $fill = false,
        int $lineBreak = 1,
    ): void {
        $width = $this->resolveWidth($width);
        $lineHeight = $this->minCellHeight();
        $lines = $this->splitLines($text, $width - (2 * self::CELL_PADDING_X));
        // 上罫線と文字が重ならないよう, 罫線がある側は線幅の半分だけ内側へ寄せる
        $paddingTop = in_array(self::BORDER_TOP, $this->borderSides($border), true) ? ($this->lineWidth / 2) : 0.0;
        $cellHeight = max($height, $paddingTop + (count($lines) * $lineHeight));

        // 収まらないときだけ改ページする。罫線も塗りも無いセルは TCPDF の MultiCell() と
        // 同じく行単位で送る。装飾があるセルは矩形をまとめて描くため行では割れないので,
        // セルごと次ページへ送る（setFancyTable が事前に改ページするため実際には起きない）。
        $decorated = $fill || $this->borderSides($border) !== [];
        if (!$decorated && ($this->y + $cellHeight) > $this->getPageBreakTrigger()) {
            $this->writeLinesWithPageBreak($lines, $width, $lineHeight, $align, $lineBreak);

            return;
        }

        $this->checkPageBreak($cellHeight);

        $originX = $this->x;
        $originY = $this->y;

        $this->addDecoration($this->cellDecorationOps($originX, $originY, $width, $cellHeight, $border, $fill));
        foreach ($lines as $index => $line) {
            if ($line === '') {
                continue;
            }
            $this->addContent($this->textOps($line, $originX, $originY + $paddingTop + ($index * $lineHeight), $lineHeight, $width, $align));
        }

        $this->lastCellHeight = $cellHeight;
        $this->advanceCursor($originX, $originY, $width, $cellHeight, $lineBreak);
    }

    /**
     * 装飾の無い折り返しセルを 1 行ずつ描き, 収まらない行の前で改ページする.
     *
     * TCPDF の MultiCell() と同じ分割位置になる.
     *
     * @param string[] $lines
     */
    private function writeLinesWithPageBreak(array $lines, float $width, float $lineHeight, string $align, int $lineBreak): void
    {
        $originX = $this->x;
        $height = 0.0;

        foreach ($lines as $line) {
            if ($this->checkPageBreak($lineHeight)) {
                // 改ページしたら, その行がこのセルの新しい起点になる
                $this->x = $originX;
                $height = 0.0;
            }
            if ($line !== '') {
                $this->addContent($this->textOps($line, $this->x, $this->y, $lineHeight, $width, $align));
            }
            $this->y += $lineHeight;
            $height += $lineHeight;
        }

        $this->lastCellHeight = $height;
        // ここまでで y は最終行の下端。advanceCursor に渡す起点へ戻して共通処理に合わせる
        $this->advanceCursor($originX, $this->y - $height, $width, $height, $lineBreak);
    }

    /**
     * 次の行へ送る. 高さを省略すると直前のセルの高さ分だけ送る.
     */
    public function newLine(?float $height = null): void
    {
        $this->x = $this->leftMargin;
        $this->y += $height ?? $this->lastCellHeight;
    }

    /**
     * 残り高さが足りなければ改ページする.
     *
     * @return bool 改ページしたら true
     */
    public function checkPageBreak(float $height): bool
    {
        if (($this->y + $height) <= $this->getPageBreakTrigger()) {
            return false;
        }

        // 自動改ページで増えたページにはテンプレート PDF を重ねない。
        // 4.3 までも重ね書きは addPdfPage() の 1 回だけで, あふれた明細が載る
        // 2 ページ目以降は白紙の上に描いていた（重ねると納品書の枠が二重に出る）。
        // 改ページしても x は引き継ぐ（表の途中で送られたとき列位置を保つため）。
        $x = $this->x;
        $this->addPage(withTemplate: false);
        $this->x = $x;

        return true;
    }

    /**
     * 画像を配置する. 高さは画像の縦横比から決まる.
     */
    public function image(string $file, float $x, float $y, float $width): void
    {
        $size = @getimagesize($file);
        if ($size === false || $size[0] <= 0) {
            return;
        }

        // 形式は拡張子ではなく中身から決める。FPDF は $type 省略時に拡張子で解析器を選び,
        // 中身と食い違うと Error で PDF 全体が落ちる（4.3 までの TCPDF は中身で判定していた）。
        $type = match ($size[2]) {
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_JPEG => 'jpg',
            // GIF の解析だけ FPDF が GD へ委譲する（_parsegif が imagecreatefromgif を呼び,
            // 無ければ Error で PDF 全体が落ちる）。ext-gd は推奨止まりなので自前で弾く
            IMAGETYPE_GIF => \function_exists('imagecreatefromgif') ? 'gif' : null,
            default => null,
        };
        if ($type === null) {
            // FPDF が解析できない形式, または GD 不在の GIF。ロゴを描かないだけにして帳票自体は出す
            return;
        }

        $height = $width * $size[1] / $size[0];
        $this->addContent($this->engine->getSetImage($file, $x, $y, $width, $height, $type));
    }

    /**
     * PDF のバイナリを返す.
     */
    public function output(): string
    {
        $this->flushContent();

        return $this->engine->render();
    }

    /**
     * 文字列の描画幅(mm)を返す.
     */
    public function getStringWidth(string $text): float
    {
        if ($text === '') {
            return 0.0;
        }

        return $this->engine->toUnit($this->engine->getStringWidthPt($text));
    }

    /**
     * フォントを実際に切り替え, ページへ選択命令を出す.
     */
    private function applyFont(string $family, string $style, float $sizePt): void
    {
        $metric = $this->engine->selectFont($family, $style, $sizePt);

        $this->fontFamily = $family;
        $this->fontStyle = $style;
        $this->fontSizePt = $sizePt;
        // アセント・ディセントは mm の正値へ揃える
        $this->fontAscent = $this->engine->toUnit($metric['ascent']);
        $this->fontDescent = $this->engine->toUnit(-$metric['descent']);
    }

    /**
     * 現在のフォントで文字が収まる最小のセル高さ(mm).
     */
    private function minCellHeight(): float
    {
        return round($this->getFontSize() * self::CELL_HEIGHT_RATIO, 6);
    }

    private function resolveWidth(float $width): float
    {
        if ($width > 0) {
            return $width;
        }

        return $this->pageWidth - $this->rightMargin - $this->x;
    }

    /**
     * セル内の 1 行を描く命令を返す.
     */
    private function textOps(string $text, float $x, float $y, float $height, ?float $width = null, string $align = ''): string
    {
        // ベースラインはセル内で上下中央に置いたときの位置
        $baseline = $y + (($height + $this->fontAscent - $this->fontDescent) / 2);

        if ($width === null) {
            $posX = $x;
        } else {
            $posX = $x + $this->alignOffset($text, $width, $align);
        }

        return $this->engine->getTextLine($text, $posX, $baseline, $this->textColor);
    }

    /**
     * セル左端から文字の左端までの距離(mm).
     */
    private function alignOffset(string $text, float $width, string $align): float
    {
        return match ($align) {
            'C' => ($width - $this->getStringWidth($text)) / 2,
            'R' => $width - self::CELL_PADDING_X - $this->getStringWidth($text),
            default => self::CELL_PADDING_X,
        };
    }

    /**
     * セルの背景と罫線を描く命令を返す.
     */
    private function cellDecorationOps(float $x, float $y, float $width, float $height, int|string $border, bool $fill): string
    {
        $ops = '';

        if ($fill) {
            $ops .= $this->engine->getBasicRect($x, $y, $width, $height, 'f', ['fillColor' => $this->fillColor]);
        }

        $sides = $this->borderSides($border);
        if ($sides === []) {
            return $ops;
        }

        // lineCap は TCPDF の既定と同じ square。butt にすると罫線の両端が
        // 線幅の半分ずつ短くなり, 表の角が欠ける
        $style = ['lineColor' => $this->drawColor, 'lineWidth' => $this->lineWidth, 'lineCap' => 'square'];
        if (count($sides) === 4) {
            return $ops.$this->engine->getBasicRect($x, $y, $width, $height, 'S', $style);
        }

        $corners = [
            self::BORDER_TOP => [$x, $y, $x + $width, $y],
            self::BORDER_RIGHT => [$x + $width, $y, $x + $width, $y + $height],
            self::BORDER_BOTTOM => [$x, $y + $height, $x + $width, $y + $height],
            self::BORDER_LEFT => [$x, $y, $x, $y + $height],
        ];
        foreach ($sides as $side) {
            [$x1, $y1, $x2, $y2] = $corners[$side];
            $ops .= $this->engine->getLine($x1, $y1, $x2, $y2, $style);
        }

        return $ops;
    }

    /**
     * 罫線指定を辺の一覧へ正規化する.
     *
     * @return array<int, int>
     */
    private function borderSides(int|string $border): array
    {
        if ($border === 1 || $border === '1' || $border === 'LTRB') {
            return [self::BORDER_TOP, self::BORDER_RIGHT, self::BORDER_BOTTOM, self::BORDER_LEFT];
        }

        if (!is_string($border) || $border === '') {
            return [];
        }

        $map = [
            'T' => self::BORDER_TOP,
            'R' => self::BORDER_RIGHT,
            'B' => self::BORDER_BOTTOM,
            'L' => self::BORDER_LEFT,
        ];

        $sides = [];
        foreach ($map as $letter => $side) {
            if (str_contains($border, $letter)) {
                $sides[] = $side;
            }
        }

        return $sides;
    }

    /**
     * 描画後のカーソル移動.
     */
    private function advanceCursor(float $originX, float $originY, float $width, float $height, int $lineBreak): void
    {
        switch ($lineBreak) {
            case 1:
                $this->x = $this->leftMargin;
                $this->y = $originY + $height;
                break;
            case 2:
                $this->x = $originX;
                $this->y = $originY + $height;
                break;
            default:
                $this->x = $originX + $width;
                $this->y = $originY;
                break;
        }
    }

    /**
     * 指定幅で折り返した行の一覧を返す.
     *
     * 改行文字で必ず折り, 幅を超えたら直前の空白位置で折る.
     * 空白が無ければ超えた位置で折る（日本語のようにそもそも空白が無い文字列のため）.
     *
     * @return array<int, string>
     */
    private function splitLines(string $text, float $maxWidth): array
    {
        $text = str_replace("\r\n", "\n", $text);
        // 末尾の改行は「次の行がある」ことを意味しない（4.3 までの TCPDF も 1 行と数える）
        if (str_ends_with($text, "\n")) {
            $text = substr($text, 0, -1);
        }

        $lines = [];
        foreach (explode("\n", $text) as $paragraph) {
            if ($paragraph === '') {
                $lines[] = '';
                continue;
            }
            if ($maxWidth <= 0) {
                $lines[] = $paragraph;
                continue;
            }
            foreach ($this->wrap($paragraph, $maxWidth) as $line) {
                $lines[] = $line;
            }
        }

        return $lines === [] ? [''] : $lines;
    }

    /**
     * 1 段落を指定幅で折り返す.
     *
     * @return array<int, string>
     */
    private function wrap(string $paragraph, float $maxWidth): array
    {
        $chars = preg_split('//u', $paragraph, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false || $chars === []) {
            return [$paragraph];
        }

        $lines = [];
        $current = '';
        $lastSpace = null;
        foreach ($chars as $char) {
            $candidate = $current.$char;
            if ($current !== '' && $this->getStringWidth($candidate) > $maxWidth) {
                if ($lastSpace !== null) {
                    $lines[] = rtrim(mb_substr($current, 0, $lastSpace));
                    $current = mb_substr($current, $lastSpace);
                } else {
                    $lines[] = $current;
                    $current = '';
                }
                $lastSpace = null;
                $candidate = $current.$char;
            }
            $current = $candidate;
            if ($char === ' ') {
                $lastSpace = mb_strlen($current);
            }
        }

        // 文字が 1 つ以上ある段落しか渡されないので, 最後の行は必ず残っている
        $lines[] = $current;

        return $lines;
    }

    private function renderFooter(): void
    {
        if ($this->footerText === '' || $this->footerFont === null) {
            return;
        }

        $previous = [$this->fontFamily, $this->fontStyle, $this->fontSizePt];
        $this->applyFont((string) $this->footerFont[0], (string) $this->footerFont[1], (float) $this->footerFont[2]);

        $footerY = $this->pageHeight - $this->footerMargin;
        $width = $this->pageWidth - $this->leftMargin - $this->rightMargin;
        $height = $this->minCellHeight();
        // フッタはセルのパディングを持たない
        $offset = $width - $this->getStringWidth($this->footerText);
        $this->addContent($this->textOps($this->footerText, $this->leftMargin + $offset, $footerY, $height));

        [$family, $style, $size] = $previous;
        if ($family !== '') {
            $this->applyFont($family, $style, $size);
        }
    }

    /**
     * 文字や画像の描画命令を積む.
     */
    private function addContent(string $ops): void
    {
        if ($ops === '') {
            return;
        }

        $this->foreground .= $ops;
    }

    /**
     * セルの塗りと罫線を積む.
     *
     * **文字とは別の入れ物へ積み, ページ確定時に文字より前へ出す。**
     * 帳票は「1 行目は文字だけ描き, 高さが確定してから 2 行目で罫線を描く」というように
     * 同じ場所を複数回描くことがあり, 素直に出力順どおりへ並べると
     * 後から出した塗りが先に描いた文字を覆って**文字が消える**.
     * 4.3 まで使っていた TCPDF も同じ理由で塗りと罫線だけをページ先頭側へ差し込んでいる
     * （`TCPDF::setPageMark()` のコメント "Borders and fills are always created after
     * content and inserted on the position marked by this method."）.
     */
    private function addDecoration(string $ops): void
    {
        if ($ops === '') {
            return;
        }

        $this->background .= $ops;
    }

    /**
     * 積んだ描画命令を現在のページへ書き出す. 塗り・罫線が先, 文字が後.
     */
    private function flushContent(): void
    {
        $ops = $this->background.$this->foreground;
        $this->background = '';
        $this->foreground = '';

        if ($ops !== '') {
            $this->engine->addContent($ops);
        }
    }

    private function toHexColor(int $red, int $green, int $blue): string
    {
        return sprintf('#%02x%02x%02x', max(0, min(255, $red)), max(0, min(255, $green)), max(0, min(255, $blue)));
    }
}
