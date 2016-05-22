<?php namespace Nht\Http\Controllers\Api;

use Illuminate\Http\Request;
use Nht\Http\Controllers\Controller;
use Illuminate\Http\Response as HttpResponse;
use Nht\Hocs\Helpers\NhtFractal;

abstract class ApiController extends Controller
{
    protected $fractal;

    /**
     * Status code for response
     * @var int
     */
    protected $statusCode = HttpResponse::HTTP_OK;

    /**
     * Show response with fractal resource for an Item
     * @param  mix $data            Resource
     * @param  FractalTransformer $transformer
     * @return json
     */
    protected function showResponse($data, $transformer = null)
    {
        $fractal = \App::make(NhtFractal::class);
        $data = $fractal->setTransformer($transformer)->getData($data);
        return $this->response($data);
    }

    /**
     * Show response with fractal resource for a Collection
     * @param  mix $data            Resource
     * @param  FractalTransformer $transformer
     * @return json
     */
    protected function listResponse($data, $transformer = null)
    {
        $fractal = \App::make(NhtFractal::class);
        $data = $fractal->setTransformer($transformer)->getData($data);
        return $this->response($data);
    }

    /**
     * Not found response
     * @param  string $message
     * @return json
     */
    protected function notFoundResponse($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->response([
            'status' => 'error',
            'data' => [],
            'message' => $message
        ]);
    }

    /**
     * Delete resource response
     * @return json
     */
    protected function deletedResponse()
    {
        return $this->setStatusCode(204)->response([
            'status' => 'success',
            'data' => [],
            'message' => 'Resource deleted'
        ]);
    }

    /**
     * Client error response
     * @param  array $data
     * @return json
     */
    protected function clientErrorResponse($data)
    {
        return $this->setStatusCode(422)->response([
            'status' => 'error',
            'data' => $data,
            'message' => 'Unprocessable entity'
        ]);
    }

    /**
     * Validate incoming request
     * @param  Request $request
     * @param  array   $rules
     * @return null|array
     */
    protected function validRequest(Request $request, $rules = [])
    {
        try {
            $this->validate($request, $rules);
        } catch(\Exception $e) {
            $response = ['form_validations' => $e->validator->errors(), 'exception' => $e->getMessage()];
            return $response;
        }
    }

    /**
     * Set status code for response
     * @param int $code Http status code
     */
    protected function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get status code
     * @return int $code Http status code
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Resource response
     * @param  array $data
     * @param  array  $headers
     * @return json
     */
    private function response($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
