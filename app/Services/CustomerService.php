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

    public function update(Customer $customer, array $data)
    {
        $customer->update($data);
        return $customer->fresh();
    }

    public function destroy(Customer $customer): void
    {
        $customer->delete();
    }

   
}