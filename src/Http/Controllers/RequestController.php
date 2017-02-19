<?php

namespace Loadsman\Laravel\Http\Controllers;

use Loadsman\Laravel\Contracts\RequestRepositoryInterface;
use Loadsman\Laravel\Entities\RequestEntity;
use Loadsman\Laravel\Http\Requests\StoreRequest;
use Loadsman\Laravel\Http\Requests\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class RequestController
 *
 * @package \Loadsman\Laravel\Http\Controllers
 */
class RequestController extends Controller
{
    /**
     * @type \Loadsman\Laravel\Contracts\RequestRepositoryInterface
     */
    protected $repository;

    /**
     * RequestController constructor.
     *
     * @param \Loadsman\Laravel\Contracts\RequestRepositoryInterface $repository
     */
    public function __construct(RequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $data = $this->repository->all();

        return response(compact('data'), 200);
    }

    /**
     *
     * @param \Loadsman\Laravel\Http\Requests\StoreRequest $storeRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $storeRequest)
    {
        $request = new RequestEntity($storeRequest->all());

        $this->repository->persist($request);

        $this->repository->flush();

        // TODO Serializable?
        return response(['data' => $request->toArray()], 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->id;

        if (! $this->repository->exists($id)) {
            return response(null, 404);
        }

        $this->repository->remove($id);

        $this->repository->flush();

        return response(null, 204);
    }

    /**
     * @param UpdateRequest $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(UpdateRequest $request)
    {
        $requestEntity = $this->repository->find($request->id);

        if (is_null($requestEntity)) {
            return response(404);
        }

        $requestEntity->update($request->all());
        $this->repository->flush();

        return response(['data' => $requestEntity->toArray()], 200);
    }
}