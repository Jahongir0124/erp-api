<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;

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
}
