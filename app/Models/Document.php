<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

  protected $table = 'document';

  protected $fillable = [
    'name',
    'code',
    'path',
    'ext',
    'created_at',
    'updated_at'
  ];

  public function documentType(): string
  {
    return Helpers::documentTypeList()[$this->ext];
  }

  public function pdfPath(): string
  {
    return str_replace(".$this->ext", '.pdf', $this->path);
  }
}
