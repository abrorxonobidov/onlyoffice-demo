<?php
/**
 * Created by PhpStorm
 * User: a_obidov
 * Date: 2025-07-31
 * Time: 12:33
 */

namespace App\Services;


use App\Helpers\Jwt;
use App\Models\Document;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PdfService
{

  /**
   * @param Document $doc
   * @return array
   * @throws Exception
   */
  public static function convertDocumentToPdf(Document $doc): array
  {

    return self::convert([
      'async' => true,
      'filetype' => $doc->ext,
      'key' => md5($doc->code . $doc->updated_at),
      'outputtype' => 'pdf',
      'title' => $title ?? 'PDF document',
      'url' => route('get-file', ['code' => $doc->code])
    ], $doc->pdfPath());
  }

  /**
   * @throws Exception
   */
  private static function convert($payload, $result_path): array
  {
    $jwt_token = Jwt::encode($payload, config('onlyoffice.secret'));

    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $jwt_token
    ])
      ->withoutVerifying()
      ->post(config('onlyoffice.server_url') . '/ConvertService.ashx', $payload);

    if (!$response->successful())
      throw new Exception('Error on saving converted file. Unsuccessful', $response->status());


    $data = @$response->json();
    $error = null;

    if (!$data['endConvert'])
      return [
        'percent' => $data['percent'],
        'endConvert' => $data['endConvert'],
        'error' => $error,
      ];

    if (empty($data))
      $error = 'Error on saving converted file. Empty data';

    if (empty(@$data['fileUrl']))
      $error = 'Error on saving converted file. Empty fileUrl';

    if (($fileContent = file_get_contents($data['fileUrl'])) === false)
      $error = 'Error on saving converted file. file_get_contents error';

    if (Storage::put($result_path, $fileContent) === false)
      $error = 'Error on saving converted file. file_put_contents error';

    return [
      'percent' => $data['percent'],
      'endConvert' => $data['endConvert'],
      'error' => $error,
    ];
  }

}
