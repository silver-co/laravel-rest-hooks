<?php

namespace SilverCO\RestHooks\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use SilverCO\RestHooks\Models\RestHook;
use SilverCO\RestHooks\Http\Requests\StoreRequest;
use SilverCO\RestHooks\Http\Requests\UpdateRequest;

class RestHookController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private Authenticatable $user;

    public function __construct()
    {
        if (!Auth::check()) {
            return false;
        }

        $this->user = Auth::user();
    }

    public function index()
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param  RestHook  $restHook A REST Hook model instance (if any).
     * @return JsonResponse
     */
    public function show(RestHook $restHook): JsonResponse
    {
        return Response::json($restHook);
    }

    /**
     * Create a REST Hook subscription.
     *
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->except(['id']);

        $data[$this->user->getForeignKey()] = $this->user->getKey();
        $restHook = RestHook::create($data);
        $message = Lang::get('resthooks::resource.store', ['event' => $restHook->event]);

        return $this->getDefaultResponse($message, $restHook->toArray());
    }

    /**
     * Modify a current subscription.
     *
     * @param  UpdateRequest  $request
     * @param  RestHook  $restHook
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, RestHook $restHook): JsonResponse
    {
        $data = $request->except(['id']);
        $restHook->update($data);
        $message = Lang::get('resthooks::resource.update', ['event' => $restHook->event]);

        return $this->getDefaultResponse($message, $restHook->toArray());
    }

    /**
     * Unsubscribe a rest hook.
     *
     * @param  RestHook  $restHook A resthook model instance.
     * @return JsonResponse
     */
    public function destroy(RestHook $restHook): JsonResponse
    {
        $restHook->delete();
        $message = Lang::get('resthooks::resource.destroy', ['event' => $restHook->event]);

        return $this->getDefaultResponse($message, $restHook->toArray());
    }

    /**
     * Format a default response for the resource.
     *
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    private function getDefaultResponse(string $message, array $data, $code = 200): JsonResponse
    {
        return Response::json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
