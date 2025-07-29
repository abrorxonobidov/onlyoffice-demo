<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
  public function index()
  {
    $documentPagination = Document::query()->paginate(20);
    return view('document.index', compact('documentPagination'));
  }

  public function upload(Request $request)
  {
    $file = $request->file('file');
    $path = $file->store('documents');
    $doc = Document::query()->create([
      'name' => $file->getClientOriginalName(),
      'path' => $path,
    ]);
    return back();
  }

  public function edit($id)
  {
    $doc = Document::query()->findOrFail($id);

    $documentConfig = [
      'document' => [
        'fileType' => $doc->ext,
        'key' => $doc->id . '-' . rand(15, 1000),
        'title' => $doc->name,
        'url' => route('document-get', $doc->id),
      ],
      'documentType' => 'word',
      'editorConfig' => [
        'mode' => 'edit',
        'lang' => 'en',
        'callbackUrl' => route('document-callback', ['id' => $doc->id]),
        'user' => [
          'id' => 55,
          'name' => 'Obidov A.A.',
        ],
        "customization" => [
          "forcesave" => false,
          'compactHeader' => true,
          "help" => false,
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
        ],
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
    $doc = Document::query()->findOrFail($id);
    //echo $doc->path;

    $path = Storage::path($doc->path);
    Log::info($path);
    return response()->file($path);
  }

  public function callback(Request $request, $id)
  {

    Log::info("id=$id");
    Log::info(json_encode($request->all()));

    $secret = config('onlyoffice.secret');

    $payload = $request->get('token');

    if (!$payload) {
      return response()->json(['error' => 1, 'message' => 'Missing token'], 400);
    }

    try {
      $decoded = JWT::decode($payload, new Key($secret, 'HS256'));
      $data = (array)$decoded;

      // ✅ Optional: handle different statuses
      if (isset($data['status'])) {
        switch ($data['status']) {
          case 2:
            Log::info("Document is ready for saving");
            // You can save the file here if you handle saving manually
            break;
          case 6:
            Log::info("Document must be force saved");
            break;
          default:
            Log::info("Callback status: " . $data['status']);
            break;
        }
      }

      return response()->json(['error' => 0]);

    } catch (\Exception $e) {
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
}
