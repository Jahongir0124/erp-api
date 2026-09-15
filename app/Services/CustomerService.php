<?php



namespace app\Services;

use App\Models\Customer;





class CustomerService
{
    public function store(array $data)
    {
        return Customer::create($data);
    }

    public function index()
    {
        return Customer::latest()->get();
    }
}