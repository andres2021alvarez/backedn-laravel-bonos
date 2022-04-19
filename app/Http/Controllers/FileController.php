<?php

namespace App\Http\Controllers;

use App\Models\File;
use Aws\Credentials\Credentials;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use Illuminate\Http\Request;

date_default_timezone_set('UTC');

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->getDataFromDynamoDb();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = explode(";", $request->getContent());
        $data = str_replace('', '"', $data);
        $datas = $this->setDataFromDynamoDb($data);
        return $datas;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $status_request = $request->status;
        if ($status_request == "0") {
            $status_request = "FALSE";
        } else {
            $status_request = "TRUE";
        }

        $id_request = $request->id;

        $data = $this->updateDataFromDynamoDb($status_request, $id_request);
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }

    public function updateDataFromDynamoDb($status_requestes, $id_request)
    {
        $credentials = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));

        $client = new Sdk([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => $credentials,
        ]);

        $dynamodb = $client->createDynamoDb();
        $marshaler = new Marshaler();

        $tableName = 'bono_settings';

        $statusChange = $status_requestes;
        $idchange = $id_request;

        $id_key = $idchange;

        $key = $marshaler->marshalJson('
            {
                "id": "' . $id_key . '"
            }
        ');

        $eav = $marshaler->marshalJson('
            {
                ":s": "' . $statusChange . '"
            }
        ');

        $params = [
            'TableName' => $tableName,
            'Key' => $key,
            'UpdateExpression' => 'set status_app = :s',
            'ExpressionAttributeValues' => $eav,
            'ReturnValues' => 'UPDATED_NEW',
        ];

        try {
            $result = $dynamodb->updateItem($params);

            return $result['Attributes']['status_app'];

        } catch (DynamoDbException $e) {
            echo "Unable to update item:\n";
            echo $e->getMessage() . "\n";
        }

    }

    public function getDataFromDynamoDb()
    {
        $credentials = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));

        $client = new Sdk([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => $credentials,
        ]);

        $dynamodb = $client->createDynamoDb();
        $marshaler = new Marshaler();

        $tableName = 'bono_settings';

        $id_key = 2;

        $key = $marshaler->marshalJson('
                {
                    "id": "' . $id_key . '"
                }
            ');

        $params = [
            'TableName' => $tableName,
            'Key' => $key,
        ];

        try {
            $result = $dynamodb->getItem($params);
            return $result["Item"]["status_app"];
        } catch (DynamoDbException $e) {
            echo "Unable to get item:\n";
            echo $e->getMessage() . "\n";
        }
    }

    public function setDataFromDynamoDb($data)
    {
        $credentials = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));

        $client = new Sdk([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => $credentials,
        ]);

        $dynamodb = $client->createDynamoDb();
        $marshaler = new Marshaler();

        $tableName = 'benefiaries';

        $id = str_replace('"', '', $data[0]);
        $school = str_replace('"', '', $data[1]);
        $grade = str_replace('"', '', $data[2]);
        $beneficiary = str_replace('"', '', $data[3]);
        $descriptions = str_replace('"', '', $data[4]);
        $status = str_replace('"', '', $data[5]);
        $worker = str_replace('"', '', $data[6]);
        $name = str_replace('"', '', $data[7]);

        $item = $marshaler->marshalJson('
            {
                "id": "' . $id . '",
                "school": "' . $school . '",
                "grade": "' . $grade . '",
                "beneficiary": "' . $beneficiary . '",
                "descriptions": "descriptions",
                "status": "' . $status . '",
                "worker": "' . $worker . '",
                "name": "' . $name . '"
            }
        ');

        $params = [
            'TableName' => $tableName,
            'Item' => $item,
        ];

        try {
            $result = $dynamodb->putItem($params);
            if ($result['@metadata']['statusCode'] == 200) {
                return "Envio Exitoso";
            } else {
                return $result['@metadata'];
            }
        } catch (DynamoDbException $e) {
            echo "Unable to add item:\n";
            echo $e->getMessage() . "\n";
        }
    }
}
