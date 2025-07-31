<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $code
 * @property string $path
 * @property string $ext
 * @property string $created_at
 * @property string $updated_at
 * @property int $pdf_status
 * @property string $pdf_updated_at
 */
class Document extends Model
{

  const PDF_STATUS_EDITED = 1;
  const PDF_STATUS_CONVERTING = 2;
  const PDF_STATUS_READY = 3;

  protected $table = 'document';

  protected $fillable = [
    'name',
    'code',
    'path',
    'ext',
    'created_at',
    'updated_at',
    'pdf_status',
    'pdf_updated_at',
  ];

  public function documentType(): string
  {
    return Helpers::documentTypeList()[$this->ext];
  }

  public function pdfPath(): string
  {
    return str_replace(".$this->ext", '.pdf', $this->path);
  }


  public function isPdfReady(): bool
  {
    return $this->pdf_status == self::PDF_STATUS_READY;
  }

  public function isPdfConverting(): bool
  {
    return $this->pdf_status == self::PDF_STATUS_CONVERTING;
  }

}
