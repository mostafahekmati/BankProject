<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;

class BaseRepository
{
    public function returnSuccessResponse( $data = null, $message = "" ) : JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message
        ];
        if ( $data ) {
            $response[ 'data' ] = $data;
        }
        return response() -> json( $response );
    }

    public function returnFailureResponse( $data = null, $message = "") : JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message
        ];
        if ( $data ) {
            $response[ 'data' ] = $data;
        }
        return response() -> json( $response );
    }
}
