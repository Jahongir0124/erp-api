<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;


class CustomerController extends Controller
{
    public function __construct(protected readonly CustomerService $customerService)
    {}

    public function index()
    {
        return CustomerResource::collection($this->customerService->index());
    }


    public function store(CustomerStoreRequest $request)
    {
        $customer = $this->customerService->store($request->validated());
        return new CustomerResource($customer);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(Customer $customer, CustomerUpdateRequest $request)
    {
        $updated = $this->customerService->update($customer, $request->validated());
        return new CustomerResource($updated);
    }

    public function destroy(Customer $customer)
    {
        $this->customerService->destroy($customer);
        return response()->json([
            'msg' => 'Customer deleted succesfully'
        ]);
    }
}
