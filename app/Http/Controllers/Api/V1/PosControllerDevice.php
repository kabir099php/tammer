<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PosDevice;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\BannerLogic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Storage ; 

class PosControllerDevice extends Controller
{

     /**
     * Connect a device to a terminal using a code.
     */
    public function connectDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:8|exists:pos_devices,code',
            'device_id' => 'required|string|max:255|unique:pos_devices,device_id',
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json($errorMessage);
        }
        try {
            // Find the POS device using the unique code
            $posDevice = PosDevice::where('code', $request->code)->first();
            if ($posDevice->connection_status === 1) {

                return response()->json('Device already connected.', 'Please conatct admin');
            }



            // Generate JWT Token for authentication
            $jwtToken = $this->generateToken($posDevice);

            // Update the POS device with the provided device ID
            $posDevice->update([
                'device_id' => $request->device_id,
                'connection_status' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device connected successfully.',
                'data' => [
                    'terminal_id' => $posDevice->terminal_id,
                    'device_id' => $posDevice->device_id,
                    'connection_status' => $posDevice->connection_status ? 'Connected' : 'Disconnected',
                    'jwt_token' => $jwtToken,

                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Device connection failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function generateToken($posDevice)
    {

        // $privateKeyPath = storage_path('app/pos_key.pem');
        // if (!file_exists($privateKeyPath)) {
        //     throw new \Exception("Private key file not found.");
        // }
        // Read the private key content from S3
        try {
            $privateKey = Storage::disk(env("FILESYSTEM_DISK", 's3'))->get('keys/pos_key.pem');

            // No need for file_exists() check here — if the file is missing, get() will throw an exception

            $payload = [
                'data' => [
                    'ops' => 'auth',
                    'client_uuid' => env('NEARPAY_CLIENT_KEY'),
                    'terminal_id' => $posDevice->terminal_id,
                ]
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256');

            return response()->json([
                'token' => $jwt,
            ]);
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            return response()->json(['error' => 'Private key file not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Disconnect a device from the terminal.
     */
    public function disconnectDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
        ]);

        // Find the POS device using the device ID
        $posDevice = PosDevice::where('device_id', $request->device_id)->first();
        
        if (!$posDevice) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found or already disconnected.'
            ], 400);
        }

        // Update the POS device to remove the device ID and mark it as disconnected
        $posDevice->update([
            'device_id' => null,
            'connection_status' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device disconnected successfully.',
            'data' => [
                'terminal_id' => $posDevice->terminal_id,
                'connection_status' => 'Disconnected'
            ]
        ]);
    }

}
