<?php

namespace App\Http\Controllers\API;
// require_once __DIR__ . '/vendor/autoload.php';
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\FaxDoc;
use App\Http\Resources\FaxDocResource;
use PDF;
// require_once __DIR__ . '/vendor/autoload.php';

class FaxDocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = FaxDoc::latest()->get();
        return response()->json([FaxDocResource::collection($data), 'FaxDocs fetched.']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {//return $_SERVER['DOCUMENT_ROOT'];
        $validator = Validator::make($request->all(),[
            'from' => 'required|max:255',
            'to' => 'required',
            'faxDetail'=>"required",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        // $faxDoc = FaxDoc::create([
        //     'from' => $request->from,
        //     'to' => $request->to,
        //     'faxDetail' => $request->faxDetail,
        //  ]);

        // Create document file by $request in Public folder 
        // $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // $section = $phpWord->addSection();
        // $text = $section->addText($request->from);
        // $text = $section->addText($request->to);
        // $text = $section->addText($request->faxDetail);
        // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        // $objWriter->save('Faxdetail.docx');
        //return response()->json('FaxDoc created successfully.');

        $this->pdfGenerater($request);

        // sendClick function


        // Configure HTTP basic authorization: BasicAuth
        $config = \ClickSend\Configuration::getDefaultConfiguration()
                    ->setUsername('funnyaraki9595@gmail.com')
                    ->setPassword('24B88227-AE72-5E5E-BD9B-5F44328F45A3');

        $apiInstance = new \ClickSend\Api\UploadApi(new \GuzzleHttp\Client(),$config);
        $localFilePath = $_SERVER['DOCUMENT_ROOT'] . '\faxDetail.pdf';
        $localFileContent = base64_encode(file_get_contents($localFilePath));

        $upload_file = new \ClickSend\Model\UploadFile(); // \ClickSend\Model\UploadFile | Your file to be uploaded
        $upload_file->setContent($localFileContent);
        $convert = "fax"; // string | 

        try {
            $result = $apiInstance->uploadsPost($upload_file, $convert);
        } catch (\Exception $e) {
            echo 'Exception when calling UploadApi->uploadsPost: ', $e->getMessage(), PHP_EOL;
        }

        $response = json_decode( $result, true );
        $uploadedFileUrl = $response['data']['_url'];

        $apiInstance = new \ClickSend\Api\FAXApi(new \GuzzleHttp\Client(),$config);
        $fax_message_list= new \ClickSend\Model\FaxMessage();
        $fax_message_list->setSource("php");
        $fax_message_list->setTo($request->to);
        // $fax_message_list->setListId("185161");
        $fax_message_list->setCustomString("custom_string");
        $fax_message_list->setFromEmail("dreamjob0415@gmail.com");
        // \ClickSend\Model\FaxMessageCollection | FaxMessageCollection model
        $fax_message = new \ClickSend\Model\FaxMessageCollection();
        $fax_message->setMessages([$fax_message_list]);
        $fax_message->setFileUrl( $uploadedFileUrl ); 
        
        try {
            $result = $apiInstance->faxSendPost($fax_message);
            print_r($result);
        } catch (\Exception $e) {
            echo 'Exception when calling FAXApi->faxSendPost: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function pdfGenerater($request) 
    {
        $data = [
            'imagePath'    => public_path('img/profile.png'),
            'from'         => $request->from,
            'to'      => $request->to,
            'faxDetail' => $request->faxDetail,
            'email'        => 'dreamjob0415@gmail.com'
        ];
        $pdf = PDF::loadView('faxDetail', $data);
        $output = $pdf->output();
        file_put_contents('faxDetail.pdf', $output);
    }

    

   
}