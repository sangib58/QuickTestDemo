<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
	protected function successData($data=null, int $code = 200)
	{
		return response()->json($data,$code);
	}

	protected function successMsg(string $message, int $code = 200)
	{
		return response()->json([
			'status' => 'success',
			'responseMsg' => $message
		],$code);
	}

	protected function error(string $message = null, int $code,string $status, $logMsg=null,$data = null)
	{
		if($logMsg!=null)
		{
			Log::error($logMsg);
		}
		return response()->json([
			'status' => $status,
			'responseMsg' => $message
		], $code);
	}

	protected function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

	protected function emailConfiguration()
	{
		if(env('MAIL_MAILER')==null || env('MAIL_HOST')==null || env('MAIL_PORT')==null ||
		env('MAIL_USERNAME')==null || env('MAIL_PASSWORD')==null || env('MAIL_ENCRYPTION')==null){
			return false;
		}
		return true;
	}

}