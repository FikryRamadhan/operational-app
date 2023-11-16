<?php

/**
 *	Develop by Rohim Wahyudin (adiva)
 *	Manfaat class ini
 *	-> Mengirim pesan chat whatsapp
 * 	-> Mengirim pesan gambar/media ke whatsapp
 * 
 *	@static json sendChat(array $data) untuk mengirim chat
 *	@static json sendMedia(array $data) untuk mengirim gambar/media
 * */


namespace App\MyClass;

class Whatsapp
{

	const STATUS_PENDING	= 1;
	const STATUS_SENT		= 2;
	const STATUS_RECEIVED	= 3;
	const STATUS_READ 		= 4;
	const STATUS_CANCELED	= 5;
	
	/**
	 * 	Edit method dibawah untuk menyesuaikan dengan aplikasi kamu
	 * */

	/**
	* Untuk mendapat Api Server Whatsapp
	* @return String
	*/
	private static function getApiServer()
	{
		return 'http://103.242.105.85:50000';
	}

	/**
	 * 	Untuk reformating nomor telepon
	 * 	@param string|int $phoneNumber
	 * 	@return string|int
	 * */
	private static function reformatPhoneNumber($phoneNumber)
	{
		try {
			$phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($phoneNumber);
		} catch (\Exception $e) {}
		return $phoneNumber;
	}

	/**
	 * 	Cek ketersediaan file
	 * 	@param string $path
	 * 	@return bool
	 * */
	private static function isFileOrDirectoryExists($path)
	{
		return \File::exists($path);
	}

	/**
	 * 	Directory Media
	 * 	@return string
	 * */
	private static function mediaDirectory($filename = '')
	{
		return storage_path('app/public/whatsapp_media/'.$filename);
	}

	/**
	 * 	Directory Media
	 * 	@return string
	 * */
	private static function mediaLink($filename = '')
	{
		return url('storage/whatsapp_media/'.$filename);
	}

	/**
	 * 	Membuat direktori
	 * 	@return bool
	 * */
	private static function createDirectory($path)
	{
		return \File::makeDirectory($path);
	}

	/**
	 * 	Membuat file
	 * 	@return bool
	 * */
	private static function createFile($path, $content)
	{
		return \File::put($path, $content);
	}

	/**
	 * 	Mime to Ext
	 * 	@param string $mime
	 * 	@return string
	 * */
	private static function mimeToExtension($mime)
	{
		return \App\MyClass\FileHelper::mimeToExtension($mime);
	}


	/**
	 * 	Put temps
	 * */
	private static function putTemps($filename, $content)
	{
		try {
			self::createFile(\Setting::temps($filename), json_encode($content));
		} catch (\Exception $e) {}
	}

	/**
	 * 	End
	 * */


	/**
	 * 	Parse data
	 * 	@param array $data
	 * 	@param string $type. hanya mendukung 'text' atau 'media'
	 * 	@return array
	 * */
	private static function parseData($data, $type = 'text')
	{
		$result = [];

		if(!in_array($type, [ 'text', 'media' ])) {
			throw new \Exception("Type tidak valid. Hanya menerima type 'text' atau 'media'");
		}

		if(!array_key_exists('to', $data) && !array_key_exists('phone', $data)) {
			throw new \Exception("Harap masukan nomor telepon dengan key 'to' atau 'phone'");
		}

		$result['phone'] = $data['to'] ?? $data['phone'];
		$result['phone'] = self::reformatPhoneNumber($result['phone']);

		if($type == 'text') {
			if(!array_key_exists('text', $data) && !array_key_exists('message', $data)) {
				throw new \Exception("Harap masukan pesan dengan key 'text' atau 'message'");
			} else {
				$result['message'] = $data['text'] ?? $data['message'];
			}
		} elseif($type == 'media') {
			if(!array_key_exists('path', $data)) {
				throw new \Exception("Harap masukkan path file dengan key 'path'");
			}
			if(!self::isFileOrDirectoryExists($data['path'])) throw new \Exception("File tidak ditemukan");
			
			$result['file'] = new \CURLFile($data['path'], mime_content_type($data['path']));
		}

		return $result;
	}



	/**
	* 	Untuk kirim chat WhatsApp
	* 	@param array $data
	* 	@example ada dibawah
	* 	@return json
	*/
	public static function sendChat($data)
	{
		$data = self::parseData($data, 'text');
		return self::send($data);
	}

	/**
	* 	Untuk kirim media WhatsApp
	* 	@param array $data
	*	@example ada dibawah
	* 	@return json
	*/
	public static function sendMedia($data)
	{
		$data = self::parseData($data, 'media');
		return self::send($data);
	}


	/**
	* 	Untuk eksekusi pengiriman pesan/media
	* 	@param array $sendData
	* 	@return json
	*/
	private static function send($sendData)
	{
		$ch = curl_init(self::getApiServer() . '/send');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}


	/**
	* 	Untuk menerima data respon dari api
	* 	@param \Illuminate\Http\Request $request
	* 	@return json
	*/
	public static function receive($request)
	{
		$phone 	= explode('@', $request->phone)[0];
		$type 	= $request->type;

		if($type == 'ack')
		{
			$status = $request->message;
			$statusCode = 0;

			if ($status == 'SERVER') {
				$status 	= 'sent';
				$statusCode	= self::STATUS_SENT;
			} elseif ($status == 'DEVICE') {
				$status 	= 'received';
				$statusCode	= self::STATUS_RECEIVED;
			} elseif ($status == 'READ') {
				$status 	= 'read';
				$statusCode	= self::STATUS_READ;
			}

			$result = [
				'type'				=> $type,
				'is_acknowledge'	=> true,
				'is_message'		=> false,
				'phone'				=> $phone,
				'status'			=> $status,
				'status_code'		=> $statusCode,
			];

			self::putTemps('whatsapp_acknowledge.txt', $result);
			return $result;
		}
		elseif ($type == 'reply')
		{
			$hasFile 	= false;
			$fileData 	= [];

			if($request->is_file === true)
			{
				self::createMediaDirectoryIfNotExists();
				$hasFile = true;
				$ext = '';
				$fileData['file_mime'] 	= $request->file_mime;
				try {
					$ext = self::mimeToExtension($request->file_mime);
				} catch (\Exception $e) {}
				$fileData['file_extension'] = $ext;

				$filename = 'file_'.date('Ymd_His').'.'.$ext;
				$filepath = self::mediaDirectory($filename);
				self::createFile($filepath, base64_decode($request->file_data));
				$fileData['file_path']	= $filepath;
				$fileData['file_link']	= self::mediaLink($filename);
				$fileData['file_data']	= $request->file_data;
			}

			$returnData = [
				'type'				=> $type,
				'is_acknowledge'	=> false,
				'is_message'		=> true,
				'phone'				=> $phone,
				'message'			=> $request->message,
				'is_has_file'		=> $hasFile,
			];

			$result = array_merge($returnData, $fileData);
			self::putTemps('whatsapp.txt', $result);

			return $result;
		}
	}


	private static function createMediaDirectoryIfNotExists()
	{
		$path = self::mediaDirectory();

		if(!self::isFileOrDirectoryExists($path)) {
			self::createDirectory($path);
		}
	}


}

/**
* @see Send Chat Tutorial
parameter array
# string to|phone 		=> Nomor Telepon
# string text|message 	=> Isi Pesan
example :
Whatsapp::sendChat([
	'to'	=> "6282316425264",
	'text'	=> "Text Pesan"
]);
*/


/**
* @see Send Media Tutorial
parameter array
# string to|phone	=> Nomor Telepon
# string path 		=> Path file
example : 
Whatsapp::sendMedia([
	'to'		=> "6282316425264",
	'path'		=> "invoice/INV0001.pdf",
]);
*/