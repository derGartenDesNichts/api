<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;

class DocumentController extends Controller
{
    public function getDocuments(){
		
		$documents = Document::select('id', 'filename', 'thumbnail')->get();
		
		return $documents->toArray();		
	}
	
	public function getDocument($id){
	
		$document = Document::find($id);

		return $document->content;  
	}
	
	public function uploadDocument(Request $request){

		if ($request->file->isValid() && ($request->file->getClientMimetype() == "application/pdf")) {
			
			$path  = $request->file->path(); 

			$handler = fopen($path, 'r');
			$content = fread($handler, filesize($path));
			fclose($handler);
			
			$document = new Document;
			
			$document->filename = $request->file->getClientOriginalName();
			$document->thumbnail = $this->createThumbnail( $path );//"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAVCAMAAACaPIWZAAACl1BMVEUAAAD////////IJCjWWlvXXl/YYWLYYWPLMTXOPUDRR0rIJCjWWlzWXF3bb3HbcHLJKy/IJCjWWVvKLTHNOT3OPUDSTE/WWlvXYGLYYWLYZGbabG7JKCvXX2DZZWfZZ2nabG7KKi3IJirWWlzXYGHZaWvIJCjbcXTYYWLZaGrIJirJKSvIJCjYYmPJKi7LLjLIJCjbcnTWXF3XXl/YZGbcdHfJKS3IJSnWXV/YYmTJJyrLMTTJJyvJKS3WWlzYYWPYYmTKKy/KLjHLMTXLMjbIJCjIJirJKS3KKizKKy3LLjDLLzPLMDTMMTLMMzTNNjfNOT3ONjbPPT7RPzzTQTrTSUbUTUrUTkvWW1zWXF3WXF7XUUrXU0vXYGHYYWLYYmLYYmPYYmTYY2XYZGbYZWbZZ2nZamzaVEXaZ2faaWnaa23abW/bbnDbb3LcWUjccHDdY1bddXXdd3necWzfZlffd3Lgd27hcWDhfHTidGTjgXfjhX7jhoHlc1nlh33mdFvmemHnf2fninvnjoLojXzojX/oj4HolIvpfF3pg2rpiHPpk4Pplorqkn/rgmHrgmLrg2Lrg2PrhGPrhGTrhWXrim7rloTrl4XshWXshmbsh2jsiGjsiWrsimvsimzsi2zsi23sjnPsknjsloLsmITsm4jtjG3tjW/tjnDtj3LtkHPtk3rtnIrtoI7uknXuk3buk3fulXnulnrulnvul3vul3zvmH3vmX7vmX/vm4Dvm4HvnILvnYPwo4vwpY/xpIvxpIzxpYzxpY3xpo3xpo7xp47xp4/xqJDxqJHxqZHxqZLxqpLxqpPxqpTxqpXxq5Txq5Xyq5XyrJXyrJbyrZbyrZfyrZjyrpjyrpnyr5nyr5rysJvzsJzyiLqjAAAARXRSTlMAAQIQEBAQEBESEyAgICAgITAwMTQ1QEBAQEBAQVBQYHBxgICAgI+Pn5+goa+vsbK/v8/Pz8/Q39/f4OHv7+/v7/Dw8PAM2YqHAAABm0lEQVQY02NgAAMhcR4IQ0KSnQEGWJTtIiKjLGQZ2DTsExKTTOQgwrxGkWUtvU3FcbqWGVXTpreVpmqChFmN4xfvP3Dw0OHW5Mylx0+cPHW6OUsBKK4Su+Tg4SNHjx07Xrfm1KkzZ86fv1Cdw8/A4FBxBCgGUnjmzNnzFy5dvnL1XKE6g1RK9/ETJyAKL16+fPXq9Rs3b9WYMsin7oMpBIpdv3nz1u3bd9qLGFTTzpy/AFJ47frenduWL5/f19c3oyCAQTp7LUjhkY3z+hAgxICBL7fn+qlts+FC/f39fR0eagwMhvnr+yZMnAgVnThlyuQJ6a7cDAxirnkTZsyeNWPShL6+CZNnL1g2p95NDeRhHc/aOStWLp8zc/LkmXNXbdo4NdiKGSTObO7VsG7r5vUr5s1bsWH7zkWhTsIMYCBo49e4ZdeubRs3btu1e3W4swgjRJxBwMy/fMuuPbt379nVFeYiChNmYODQ845ZuGXHji2VQdYiCGEGBkZFR/+SFZ3RPvpcyMJACU5t90BfW0UmVGEQkNFS4oBzAFKSw6+AQbf5AAAAAElFTkSuQmCC";
			die('<pre>' . var_export($document->thumbnail, true));
			$document->content = $content;

			$document->save();
			
			return array(
				'id' => $document->id,
				'thumbnail' => $document->thumbnail,
				'filename' => $document->filename
			);
		} else {
			return response(array(
                'error' => true,
                'message' =>'File is invalid!',
            ),415);  
		}
	}
	
	public function createThumbnail( $filename ){
		
		$im = new imagick($filename.'[0]');
		$im->setImageFormat('jpg');
		header('Content-Type: image/jpeg');
		return $im;
	}
}
