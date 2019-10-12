<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll($collection, $code = 200)
    {
        return $this->successResponse(['code' => $code, 'data' => $collection], $code);
    }

    protected function showOne($collection, $code = 200)
    {
        return $this->successResponse(['code' => $code, 'data' => $collection], $code);
    }

    protected function createdResponse($message, $data , $code = 201)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' =>$data]);
    }

    protected function updatedResponse($message, $data, $code = 200)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' =>$data]);
    }

    protected function deletedResponse($message, $data, $code = 200)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' =>$data]);
    }
}