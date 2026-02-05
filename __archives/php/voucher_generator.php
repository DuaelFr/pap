<?php

/**
 * Generate voucher PNGs
 *
 * Example:
 *   $voucher = new VoucherGenerator();
 *   $voucher->product = "Trial glider flight";
 *   $voucher->remarks = "Thank you for making a reservation first.";
 *   $voucher->code = "TRIAL-GLIDER";
 *   $voucher->quantity = "001";
 *   $voucher->transaction = "2GA48505VL5872006";
 *   $voucher->paid_by = "John Doe";
 *   $voucher->paid_at = "2016-11-12 19:14";
 *   $voucher->to_png("voucher.png");
 *
 * Sample Example:
 *   $voucher = new VoucherGenerator();
 *   $voucher->sample = true;
 *   ...
 */
class VoucherGenerator {
  const TEMPLATE_PATH = "/__images/identity/voucher_template-f5955251.png";
  const PRINTER_FONT_PATH = "/__assets/fonts/fake_receipt-79b516b4.ttf";
  const SAMPLE_FONT_PATH = "/__assets/fonts/copy_paste-46e0eaea.ttf";

  public $image, $color, $charset;
  public $sample = false;
  public $voucher, $remarks;
  public $code, $number, $quantity, $transaction, $paid_by, $paid_at;

  public function __construct($charset="UTF-8") {
    $this->charset = $charset;
    $this->image = imageCreateFromPNG($_SERVER['DOCUMENT_ROOT'].self::TEMPLATE_PATH);
    imageSaveAlpha($this->image, true);
    $this->color = imageColorAllocate($this->image, 80, 80, 100);
  }

  public function to_png($filename) {
    $this->image_print(140, 300, 40, $this->product);
    $this->image_print(140, 600, 51, $this->remarks);
    $this->image_print(1620, 290, 19, $this->code);
    $this->image_print(1620, 377, 19, $this->quantity);
    $this->image_print(1620, 464, 19, $this->transaction);
    $this->image_print(1620, 638, 19, $this->paid_by);
    $this->image_print(1620, 551, 19, $this->paid_at);
    if ($this->sample) {
      $red = imageColorAllocateAlpha($this->image, 200, 0, 0, 50);
      $font = $_SERVER['DOCUMENT_ROOT'].self::SAMPLE_FONT_PATH;
      imageTTFText($this->image, 130, 16, 190, 830, $red, $font, "SPÉCIMEN - SAMPLE");
    }
    imagePNG($this->image, $filename);
  }

  private function image_print($x, $y, $w, $text) {
    if ($this->charset != 'ISO-8859-1') {
      $text = iconv($this->charset, 'ISO-8859-1//TRANSLIT', $text);
    }
    $text = wordWrap($text, $w, "\t", true);
    foreach (explode("\t", $text) as $line) {
      imageTTFText($this->image, 28, 0, $x, $y, $this->color, $_SERVER['DOCUMENT_ROOT'].self::PRINTER_FONT_PATH, $line);
      $y += 35;
    }
  }
}

?>
