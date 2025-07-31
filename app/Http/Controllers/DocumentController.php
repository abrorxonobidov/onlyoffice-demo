<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Document;
use App\Services\PdfService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
  public function index()
  {
    $documentPagination = Document::query()->paginate(10);
    return view('document.index', compact('documentPagination'));
  }

  public function create(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'ext' => 'required|string|in:xlsx,docx,pptx,pdf',
    ]);

    $file = new UploadedFile(Storage::disk('sample')->path("template." . $request->input('ext')), $request->input('name'));
    $date = date('Y-m-d H:i:s');
    $folder = date('Y/m/d', strtotime($date));
    $code = md5(Str::random(32) . time());
    $ext = $file->getExtension();
    $path = $file->storeAs($folder, "$code.$ext");
    $doc = Document::query()->create([
      'name' => $file->getClientOriginalName() . '.' . $ext,
      'path' => $path,
      'code' => $code,
      'ext' => $ext,
      'created_at' => $date,
    ]);
    return redirect()->route('document-edit', [
      'id' => $doc->id
    ]);
  }

  public function upload(Request $request)
  {
    $request->validate([
      'file' => 'required|file|extensions:docx,xlsx|max:2048'
    ]);

    $file = $request->file('file');
    $date = date('Y-m-d H:i:s');
    $folder = date('Y/m/d', strtotime($date));
    $code = md5(Str::random(32) . time());
    $ext = $file->getClientOriginalExtension();
    $path = $file->storeAs($folder, "$code.$ext");
    $doc = Document::query()->create([
      'name' => $file->getClientOriginalName(),
      'path' => $path,
      'code' => $code,
      'ext' => $ext,
      'created_at' => $date,
    ]);
    return redirect()->route('document-edit', [
      'id' => $doc->id
    ]);
  }

  public function edit($id)
  {
    $doc = Document::query()->findOrFail($id);

    $documentConfig = [
      'document' => [
        'fileType' => $doc->ext,
        'key' => $doc->id . '-' . md5($doc->updated_at) . time(),
        'title' => $doc->name,
        'url' => route('document-get', $doc->id),
      ],
      'documentType' => Helpers::documentTypeList()[$doc->ext],
      'editorConfig' => [
        'mode' => 'edit',
        'lang' => 'en',
        'callbackUrl' => route('document-callback', ['id' => $doc->id]),
        'user' => [
          'id' => Auth::id(),
          'name' => 'Obidov A.A.',
        ],
        'customization' => [
          'forcesave' => true,
          'compactHeader' => true,
          'help' => false,
          'macros' => false,
          'macrosMode' => "disabled",
          'plugins' => false,
          'features' => [
            'spellcheck' => false
          ],
          'goback' => [
            'blank' => false,
            'text' => 'Exit',
            'url' => route('document-index'),
          ],
          'uiTheme' => 'theme-light'
        ]
      ]
    ];

    $jwtSecret = config('onlyoffice.secret');
    $token = JWT::encode($documentConfig, $jwtSecret, 'HS256');
    $documentConfig['token'] = $token;

    return view('document.edit', [
      'doc' => $doc,
      'config' => json_encode($documentConfig),
      'serverUrl' => config('onlyoffice.server_url'), // change to your OnlyOffice server
    ]);
  }

  public function get($id)
  {
    Log::info("Get $id");
    $doc = Document::query()->findOrFail($id);
    $path = Storage::path($doc->path);
    return response()->file($path);
  }


  /**
   * @throws Exception
   */
  public function getPdf($code)
  {
    $doc = Document::query()->where('code', $code)->firstOrFail();

    $pdfPath = Storage::path($doc->pdfPath());

    if (!$doc->isPdfReady()) {
      $res = PdfService::convertDocumentToPdf($doc);
      if ($res['endConvert']) {
        $doc->update([
          'pdf_status' => Document::PDF_STATUS_READY,
          'pdf_updated_at' => date('Y-m-d H:i:s'),
        ]);
      } else
        $pdfPath = Storage::disk('sample')->path('converting.pdf');
    }

    if (!file_exists($pdfPath))
      $pdfPath = Storage::disk('sample')->path('not_found.pdf');

    return response()->file($pdfPath);
  }

  /**
   * @throws Exception
   */
  public function checkPdf($code)
  {
    $doc = Document::query()->where('code', $code)->firstOrFail();

    if ($doc->isPdfReady())
      return [
        'percent' => 100,
        'endConvert' => true,
        'error' => null
      ];

    $res = PdfService::convertDocumentToPdf($doc);

    if ($res['endConvert']) {
      $doc->update([
        'pdf_status' => Document::PDF_STATUS_READY,
        'pdf_updated_at' => date('Y-m-d H:i:s'),
      ]);
    } else {
      $doc->update([
        'pdf_status' => Document::PDF_STATUS_CONVERTING,
      ]);
    }

    return $res;

  }

  public function callback(Request $request, $id)
  {
    $secret = config('onlyoffice.secret');

    $payload = $request->get('token');

    if (!$payload) {
      return response()->json(['error' => 1, 'message' => 'Missing token'], 400);
    }

    $document = Document::query()->find($id);

    if (empty($document)) {
      return response()->json(['error' => 1, 'message' => 'Document not found'], 400);
    }


    try {
      $decoded = JWT::decode($payload, new Key($secret, 'HS256'));
      $data = (array)$decoded;
      $error = 1;
      switch ($data['status']) {
        case 1:
          Log::info("Document is opened");
          $error = 0;
          break;
        case 2:
          Log::info("Document is ready for saving");
          $error = 0;
          break;
        case 4:
          Log::info("Document is closed");
          $error = 0;
          break;
        case 6:
          Log::info("Force save");
          $downloadUri = $data["url"];
          if (($new_data = file_get_contents($downloadUri)) === false) {
            Log::error('Failed to download document');
          } else {
            try {
              $save_res = Storage::put($document->path, $new_data);
            } catch (Exception $exception) {
              $save_res = false;
              Log::error($exception->getMessage());
            }
            if ($save_res) {
              $document->update([
                'updated_at' => date('Y-m-d H:i:s')
              ]);
              $error = 0;
            }
          }
          Log::info("Document must be force saved");
          break;
        default:
          Log::info("Callback status: " . $data['status']);
          break;
      }

      return response()->json(['error' => $error]);

    } catch (Exception $e) {
      Log::error('OnlyOffice JWT verification failed: ' . $e->getMessage());
      return response()->json(['error' => 1, 'message' => 'Invalid token'], 403);
    }

  }

  public function delete($id)
  {
    $doc = Document::query()->findOrFail($id);
    $doc->delete();
    return redirect()->to('document-index');
  }

  public function view($id)
  {
    $doc = Document::query()->findOrFail($id);
    return view('document.view', [
      'doc' => $doc
    ]);
  }

  /**
   * @throws Exception
   */
  public function getFile($code)
  {
    $doc = Document::query()->where('code', $code)->firstOrFail();

    $filePath = Storage::path($doc->path);

    if (!file_exists($filePath))
      throw new Exception("File not found", 404);

    return response()->file($filePath);
  }


}
