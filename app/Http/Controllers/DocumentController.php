<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function destroy(Document $document)
    {
        Storage::delete($document->path);
        $document->delete();
        
        return response()->json(['success' => true]);
    }
}