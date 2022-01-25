<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentMainSection;
use App\Models\DocumentSection;
use App\Models\DocumentSubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    $newSubSection->contents = $secobj["content"];
                    $newSubSection->title = $secobj["title"];
                    $newSubSection->save();
                }
            }
        }

        return $this->returnResponse("Document saved successfully",  []);
    }

    public function getDocuments()
    {
        $depts = Department::orderBy('name', 'ASC')->get();

        if (count($depts) > 0) {
            foreach ($depts as $dept => $deptobj) {

                $documents = Document::where([['status', '=', 'Active'], ['dept_id', '=', $deptobj->id]])->get();

                if (count($documents) > 0) {

                    foreach ($documents as $doc => $obj) {
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
