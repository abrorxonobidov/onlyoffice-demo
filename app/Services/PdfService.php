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
   * @return bool
   * @throws Exception
   */
  public static function convertDocumentToPdf(Document $doc): bool
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
  private static function convert($payload, $result_path): bool
  {
    $jwt_token = Jwt::encode($payload, config('onlyoffice.secret'));

    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $jwt_token
    ])
      ->withoutVerifying()
      ->post(config('onlyoffice.server_url') . '/ConvertService.ashx', $payload);

    echo $jwt_token;

    if (!$response->successful())
      throw new Exception('Error on saving converted file. Unsuccessful', $response->status());


    $data = @$response->json();

    if (empty($data))
      throw new Exception('Error on saving converted file. Empty data', 404);

    if (empty(@$data['fileUrl']))
      throw new Exception('Error on saving converted file. Empty fileUrl', 404);

    if (($fileContent = file_get_contents($data['fileUrl'])) === false)
      throw new Exception('Error on saving converted file. file_get_contents error', 500);

    if (Storage::put($result_path, $fileContent) === false)
      throw new Exception('Error on saving converted file. file_put_contents error', 500);

    return $data['endConvert'];
  }

}
