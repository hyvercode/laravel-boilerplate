<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 08/04/22
 * Time: 10.33
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Services;

use App\Helpers\Constants;
use App\Repositories\PostRepository;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;

class PostService implements BaseService
{

    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function all(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->postRepository->all(['*'])
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function paginate(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->postRepository->paginate($request->searchBy, $request->searchParam, $request->perPage, ['*'], 'page', $request->currentPage)
        );
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.
    }

    public function deleteById($id, Request $request)
    {
        // TODO: Implement deleteById() method.
    }

    public function getById($id, Request $request)
    {
        // TODO: Implement getById() method.
    }

    public function updateById($id, Request $request)
    {
        // TODO: Implement updateById() method.
    }
}
