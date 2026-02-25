<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AddressController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $addresses = $this->userAddresses()->orderBy('is_default', 'desc')->get();

        return AddressResource::collection($addresses);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->userAddresses()->create($request->validated());

        if ($request->boolean('is_default')) {
            $this->userAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function show(Address $address): AddressResource|JsonResponse
    {
        if ($address->user_id !== request()->user()->id) {
            abort(404);
        }

        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): AddressResource|JsonResponse
    {
        if ($address->user_id !== request()->user()->id) {
            abort(404);
        }

        $address->update($request->validated());

        if ($request->boolean('is_default')) {
            $this->userAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return new AddressResource($address->fresh());
    }

    public function destroy(Address $address): JsonResponse
    {
        if ($address->user_id !== request()->user()->id) {
            abort(404);
        }

        $address->delete();

        return response()->json(null, 204);
    }

    private function userAddresses()
    {
        return request()->user()->addresses();
    }
}
