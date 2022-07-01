<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\DocumentMainSection;
use App\Models\DocumentSection;
use App\Models\DocumentSubSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends BaseController
{
    //

    public function addDocumentation(Request $request)
    {

        $document = json_decode($request->document, true);
        $newdoc = new Document();
        $newdoc->name = $request->title;
        $newdoc->dept_id = $request->dept_id;
        $newdoc->user_id = Auth::user()->id;
        $newdoc->save();

        if ($request->file('file') != null) {

            $filepath = Storage::disk('public_uploads')->put("documents", $request->file('file'));
            $file = new DocumentFile();
            $file->url = '/' . 'assets/' . $filepath;
            $file->document_id = $newdoc->id;
            $file->title = $request->file_title;
            $file->save();
        }

        foreach ($document as $doc => $obj) {
            $newContent = new DocumentMainSection();
            $newContent->document_id = $newdoc->id;
            $newContent->contents = $obj["content"];
            $newContent->save();

            foreach ($obj["sections"] as $sec => $secobj) {
                $newSection =  new DocumentSection();
                $newSection->contents = $secobj["content"];
                $newSection->content_id  = $newContent->id;
                $newSection->title = $secobj["title"];
                $newSection->save();

                foreach ($secobj["sub_section"] as $subsec => $subsecObj) {
                    $newSubSection = new DocumentSubSection();
                    $newSubSection->section_id = $newSection->id;
                    $newSubSection->contents = $subsecObj["content"];
                    $newSubSection->title = $subsecObj["title"];
                    $newSubSection->save();
                }
            }
        }

        return $this->returnResponse("Document saved successfully",  []);
    }
    public function deleteFile(Request $request)
    {
        DocumentFile::whereId($request->id)->delete();
        return $this->returnResponse("File deleted successfully",  []);
    }
    public function updateDocumentation(Request $request)
    {

        $document = json_decode($request->document, true);


        Document::whereId($request->id)->update([
            "name" => $request->title
        ]);


        if ($request->file('file') != null) {

            $filepath = Storage::disk('public_uploads')->put("documents", $request->file('file'));
            $file = new DocumentFile();
            $file->url = '/' . 'assets/' . $filepath;
            $file->document_id = $request->id;
            $file->title = $request->file_title;
            $file->save();
        }
        foreach ($document['content'] as $doc => $obj) {

            $checkDock = DocumentMainSection::whereDocumentId($request->id)->first();

            if ($checkDock) {
                DocumentMainSection::whereDocumentId($request->id)->update([
                    "contents" => $obj["contents"]
                ]);
            } else {
                if ($obj["contents"] != null && $obj["contents"] != '') {
                    $newDocCont = new DocumentMainSection();
                    $newDocCont->document_id = $request->id;
                    $newDocCont->contents = $obj["contents"];
                    $newDocCont->save();
                }
            }

            foreach ($obj["sections"] as $sec => $secobj) {
                DocumentSection::whereContentId($secobj['content_id'])->update([
                    "contents" => $secobj["contents"],
                    "title" => $secobj["title"]
                ]);

                foreach ($secobj["sub_sections"] as $subsec => $subsecObj) {

                    DocumentSubSection::whereSectionId($subsecObj['section_id'])->update([
                        "contents" => $subsecObj["contents"],
                        "title" => $subsecObj["title"]
                    ]);
                }
            }
        }

        return $this->returnResponse("Document updated successfully",  $document);
    }

    public function getDocuments()
    {
        $depts = Department::all();

        if (count($depts) > 0) {
            foreach ($depts as $dept => $deptobj) {

                $documents = Document::where([['status', '=', 'Active'], ['dept_id', '=', $deptobj->id]])->get();

                if (count($documents) > 0) {

                    foreach ($documents as $doc => $obj) {
                        $user = User::whereId($obj->user_id)->first();
                        $obj["created_by"] = $user->name;
                        $files = DocumentFile::whereDocumentId($obj->id)->get();
                        $obj['files'] = $files;
                        $main_sections = DocumentMainSection::whereDocumentId($obj->id)->get();
                        $obj["content"] = $main_sections;

                        foreach ($obj["content"] as $mainsec => $mainsecobj) {
                            $sections = DocumentSection::whereContentId($mainsecobj->id)->get();
                            $mainsecobj["sections"] = $sections;

                            foreach ($mainsecobj["sections"] as $subsec => $subsecobj) {
                                $subcontent = DocumentSubSection::whereSectionId($subsecobj->id)->get();

                                $subsecobj["sub_sections"] = $subcontent;
                            }
                        }
                    }
                }

                $deptobj["document"] = $documents;
            }
        }

        return $this->returnResponse("Documents",    $depts);
    }
}
